<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ExtraService;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderEvidence;
use App\Models\ActivityTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderReadyToProcessNotification;
use App\Notifications\OrderCompletedNotification;

class ServiceOrderController extends Controller
{
    // =========================================================================
    // HELPERS
    // =========================================================================

    // Cek apakah user adalah Staff (Admin/Guru)
    private function isStaff()
    {
        return Auth::guard('web')->check();
    }

    // Cek apakah user adalah Siswa/Wali
    private function isStudent()
    {
        return Auth::guard('student')->check();
    }

    // Helper untuk mengambil Admin (untuk notifikasi)
    private function getAdmins()
    {
        // Asumsi role_id 1 adalah Admin (Sesuaikan dengan DB Anda)
        return User::where('role_id', 1)->get();
    }

    // Helper untuk mengambil Guru (untuk notifikasi pengerjaan)
    private function getTeachers()
    {
        // Asumsi role_id 2 adalah Guru/Pengajar
        return User::where('role_id', 2)->get();
    }

    // =========================================================================
    // 1. ALUR PEMESANAN (CUSTOMER / STAFF)
    // =========================================================================

    public function selectStudent(Request $request)
    {
        $query = ActivityTransaction::with('student')->where('student_status', true);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('student_number', 'like', '%' . $searchTerm . '%');
            });
        }

        $activityTransactions = $query->orderBy('id', 'desc')->paginate(12);
        return view('admin.extra-service.extra-service-list.index', compact('activityTransactions'));
    }

    public function catalog(Request $request, Student $student)
    {
        $query = ExtraService::where('is_active', true);

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $services = $query->orderBy('name')->get();
        return view('admin.extra-service.extra-service-katalog.index', compact('student', 'services'));
    }

    public function checkout(Student $student, ExtraService $service)
    {
        return view('admin.extra-service.extra-service-order.index', compact('student', 'service'));
    }

    public function store(Request $request)
    {
        $isStaff = $this->isStaff();

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'extra_service_id' => 'required|exists:extra_services,id',
            'order_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:pay_now,bill_later',
            'discount_note' => 'nullable|string',
            'is_free' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $service = ExtraService::findOrFail($request->extra_service_id);
            $basePrice = $service->base_price;

            // Logic Harga (Gratis jika admin mencentang is_free)
            $isFree = $isStaff && ($request->has('is_free') && $request->is_free == '1');
            $finalPricePerUnit = $isFree ? 0 : $basePrice;
            $totalFinalPrice = $finalPricePerUnit * $request->quantity;

            $note = $request->discount_note;
            if ($isFree && empty($note)) {
                $note = 'Digratiskan oleh Admin/Pengajar';
            }

            // Logic Status Awal
            if ($request->payment_method === 'pay_now') {
                $initialStatus = 'pending_payment'; // Tunggu upload bukti
            } else {
                $initialStatus = $isStaff ? 'pending_process' : 'pending_confirmation';
            }

            $order = ServiceOrder::create([
                'student_id' => $request->student_id,
                'extra_service_id' => $request->extra_service_id,
                'order_date' => $request->order_date,
                'quantity' => $request->quantity,
                'base_price_at_order' => $basePrice,
                'final_price_per_unit' => $finalPricePerUnit,
                'total_final_price' => $totalFinalPrice,
                'discount_note' => $note,
                'payment_method' => $request->payment_method,
                'status' => $initialStatus,
                'processed_by' => $isStaff ? Auth::id() : null,
            ]);

            DB::commit();

            // NOTIFIKASI 1: Pesanan Masuk -> Kirim ke Admin
            Notification::send($this->getAdmins(), new NewOrderNotification($order));

            // Redirect Flow
            if ($request->payment_method === 'pay_now') {
                return redirect()->route('orders.payment', $order->id)
                    ->with('success', 'Pesanan dibuat. Silakan upload bukti pembayaran.');
            }

            if ($isStaff) {
                // [FIX] Menggunakan nama route yang benar sesuai web.php
                return redirect()->route('orders.select-student')->with('success', 'Pesanan berhasil dibuat.');
            } else {
                return redirect()->route('student.service-orders.history')->with('success', 'Pesanan terkirim.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 2. ALUR PEMBAYARAN (UPLOAD BUKTI)
    // =========================================================================

    public function payment(ServiceOrder $order)
    {
        if ($order->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'Status pesanan tidak valid untuk upload pembayaran.');
        }
        return view('admin.extra-service.extra-service-payment-upload.index', compact('order'));
    }

    public function processPayment(Request $request, ServiceOrder $order)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'sender_name' => 'nullable|string',
            'bank_destination' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $path = $request->file('proof_image')->store('payments', 'public');

            // Simpan ke Payment
            $order->payments()->create([
                'amount' => $order->total_final_price,
                'proof_image' => $path,
                'sender_name' => $request->sender_name,
                'bank_destination' => $request->bank_destination,
                'status' => 'pending',
            ]);

            // Ubah status Order
            $order->update(['status' => 'pending_confirmation']);

            DB::commit();

            // Notifikasi ke Admin
            Notification::send($this->getAdmins(), new NewOrderNotification($order));

            // [FIX] Mengarahkan ke halaman Index Pesanan agar user bisa melihat statusnya berubah
            $redirectRoute = $this->isStaff() ? 'orders.index' : 'student.service-orders.history';

            // Fallback jika route student belum ada/aktif
            if (!$this->isStaff() && !\Illuminate\Support\Facades\Route::has('student.service-orders.history')) {
                return redirect()->route('orders.index') // Asumsi student bisa akses ini atau ganti route lain
                    ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi Admin.');
            }

            return redirect()->route($redirectRoute)
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi Admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 3. ALUR ADMIN: KONFIRMASI / VERIFIKASI PESANAN
    // =========================================================================

    public function updateStatus(Request $request, ServiceOrder $order)
    {
        // Hanya Staff/Admin yang boleh akses method ini
        if (!$this->isStaff()) abort(403);

        $request->validate([
            'status' => 'required|in:pending_process,cancelled,rejected'
        ]);

        DB::beginTransaction();
        try {
            // A. Jika Status diubah jadi 'pending_process' (Artinya Admin Menerima/Memvalidasi)
            if ($request->status === 'pending_process') {

                // 1. Jika metode pay_now, validasi data paymentnya juga
                if ($order->payment_method === 'pay_now') {
                    $lastPayment = $order->payments()->latest()->first();
                    if ($lastPayment) {
                        $lastPayment->update([
                            'status' => 'verified',
                            'verified_by' => Auth::id()
                        ]);
                    }
                }

                // 2. Update status Order
                $order->update([
                    'status' => 'pending_process', // Siap dikerjakan Guru
                    'processed_by' => Auth::id()
                ]);

                // NOTIFIKASI 2: Order Valid -> Kirim ke Pengajar/Guru
                Notification::send($this->getTeachers(), new OrderReadyToProcessNotification($order));

                $msg = 'Pesanan divalidasi. Notifikasi dikirim ke Pengajar.';
            }
            // B. Jika Dibatalkan/Ditolak
            else {
                $order->update([
                    'status' => $request->status,
                    'processed_by' => Auth::id()
                ]);

                // Opsional: Jika ditolak, reject payment juga
                if ($request->status === 'rejected') {
                    $order->payments()->update(['status' => 'rejected']);
                }

                $msg = 'Status pesanan diperbarui menjadi ' . $request->status;
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 4. ALUR PENGAJAR: PENYELESAIAN (UPLOAD BUKTI & COMPLETE)
    // =========================================================================

    public function completion(ServiceOrder $order)
    {
        // Pastikan hanya order yang sedang diproses yang bisa diselesaikan
        if ($order->status !== 'pending_process') {
            return back()->with('error', 'Order ini belum siap dikerjakan atau sudah selesai.');
        }

        return view('admin.extra-service.extra-service-completion.index', compact('order'));
    }

    public function storeCompletion(Request $request, ServiceOrder $order)
    {
        $request->validate([
            'completion_note' => 'nullable|string',
            'evidence_photos' => 'nullable|array', // Opsional upload
            'evidence_photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Foto Bukti (Jika ada)
            if ($request->hasFile('evidence_photos')) {
                foreach ($request->file('evidence_photos') as $photo) {
                    $path = $photo->store('service-evidences', 'public');

                    ServiceOrderEvidence::create([
                        'service_order_id' => $order->id,
                        'file_path' => $path,
                        'uploaded_by' => Auth::id(),
                        'description' => 'Bukti pengerjaan',
                    ]);
                }
            }

            // 2. Update Status Order -> COMPLETED
            $order->update([
                'status' => 'completed',
                'completion_note' => $request->completion_note,
                'completed_at' => now(),
                'processed_by' => Auth::id(), // ID Pengajar yang menyelesaikan
            ]);

            DB::commit();

            // NOTIFIKASI 3: Order Selesai -> Kirim ke Siswa/Orang Tua
            if ($order->student) {
                Notification::send($order->student, new OrderCompletedNotification($order));
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Layanan selesai! Notifikasi dikirim ke orang tua.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan pesanan: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 5. MANAJEMEN UMUM (INDEX, SHOW, DELETE)
    // =========================================================================

    /**
     * MENAMPILKAN RIWAYAT & PROGRESS ORDER (MANAGEMENT)
     */
    public function index(Request $request)
    {
        // Eager load relasi yang dibutuhkan (termasuk 'processor' untuk melihat siapa yang memproses)
        $query = ServiceOrder::with(['student', 'extraService', 'payments', 'processor', 'evidences'])
            ->orderBy('created_at', 'desc');

        // 1. Filter by Status (Pending, Process, Completed, etc)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 2. Search by Student Name or Service Name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Cari berdasarkan Nama Siswa atau Nomor Induk
                $q->whereHas('student', function ($subQ) use ($search) {
                    $subQ->where('student_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                })
                    // ATAU Cari berdasarkan Nama Layanan
                    ->orWhereHas('extraService', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 3. Logic Khusus Role (Opsional)
        // Jika yang login adalah GURU (role_id 2), mungkin ingin melihat tugas yg belum diambil?
        // Saat ini kita biarkan Guru melihat semua agar transparan.
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->role_id == 2) {
            // Tambahan logic jika perlu, misal: $query->where('status', '!=', 'cancelled');
        }

        // Gunakan withQueryString() agar parameter search/status tidak hilang saat klik pagination
        $orders = $query->paginate(10)->withQueryString();

        return view('admin.order-service.order-service-index.index', compact('orders'));
    }


    public function show(ServiceOrder $order)
    {
        $order->load(['payments', 'evidences.uploader', 'processor']);
        return view('admin.extra-service.extra-service-show.index', compact('order'));
    }

    public function destroy(ServiceOrder $order)
    {
        if (!in_array($order->status, ['pending_confirmation', 'pending_payment', 'cancelled', 'rejected'])) {
            return back()->with('error', 'Order yang sedang diproses atau selesai tidak bisa dihapus.');
        }

        $order->delete();
        return back()->with('success', 'Pesanan dihapus.');
    }
}
