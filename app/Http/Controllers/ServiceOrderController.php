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

    private function isStaff()
    {
        return Auth::guard('web')->check();
    }

    private function isStudent()
    {
        return Auth::guard('student')->check();
    }

    private function getAdmins()
    {
        return User::where('role_id', 1)->get();
    }

    private function getTeachers()
    {
        return User::where('role_id', 2)->get();
    }

    // =========================================================================
    // 1. ALUR PEMESANAN
    // =========================================================================

    public function selectStudent(Request $request)
    {
        $query = ActivityTransaction::with('student')->where('student_status', true);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('student_number', 'like', '%' . $searchTerm . '%');
            });
        }

        $activityTransactions = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        return view('admin.extra-service.extra-service-list.index', compact('activityTransactions'));
    }

    public function catalog(Request $request, Student $student)
    {
        $query = ExtraService::where('is_active', true);

        if ($request->filled('search')) {
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

            // Logic Harga
            $isFree = $isStaff && ($request->has('is_free') && $request->is_free == '1');
            $finalPricePerUnit = $isFree ? 0 : $basePrice;
            $totalFinalPrice = $finalPricePerUnit * $request->quantity;

            $note = $request->discount_note;
            if ($isFree && empty($note)) {
                $note = 'Digratiskan oleh Admin/Pengajar';
            }

            // --- LOGIC STATUS (DIPERBARUI) ---
            if ($request->payment_method === 'pay_now') {
                // Jika Bayar Langsung -> Menunggu Upload Bukti
                $initialStatus = 'pending_payment';
            } else {
                // Jika Tagihan (Bill Later) -> WAJIB Konfirmasi Admin dulu
                // Baik user maupun admin yang buat, tetap harus lewat fase approval
                // agar masuk ke list "Menunggu Konfirmasi" di dashboard Admin.
                $initialStatus = 'pending_confirmation';
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

            // Notifikasi ke Admin ada order baru
            Notification::send($this->getAdmins(), new NewOrderNotification($order));

            // Redirect Flow
            if ($request->payment_method === 'pay_now') {
                return redirect()->route('orders.payment', $order->id)
                    ->with('success', 'Pesanan dibuat. Harap upload bukti pembayaran.');
            }

            if ($isStaff) {
                // Jika Bill Later & Staff, kembali ke pilih siswa
                return redirect()->route('orders.select-student')
                    ->with('success', 'Pesanan berhasil dibuat. Menunggu persetujuan Admin.');
            } else {
                // Jika Siswa, kembali ke history
                return redirect()->route('student.service-orders.history')
                    ->with('success', 'Pesanan terkirim. Menunggu verifikasi Admin.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 2. PEMBAYARAN (UPLOAD BUKTI)
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
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'sender_name' => 'nullable|string',
            'bank_destination' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $path = $request->file('proof_image')->store('payments', 'public');

            $order->payments()->create([
                'amount' => $order->total_final_price,
                'proof_image' => $path,
                'sender_name' => $request->sender_name,
                'bank_destination' => $request->bank_destination,
                'status' => 'pending',
            ]);

            // Update status jadi pending_confirmation (Menunggu Admin Cek)
            $order->update(['status' => 'pending_confirmation']);

            DB::commit();

            Notification::send($this->getAdmins(), new NewOrderNotification($order));

            // Redirect ke Index agar user lihat status berubah
            $redirectRoute = $this->isStaff() ? 'orders.index' : 'student.service-orders.history';

            // Fallback check
            if (!$this->isStaff() && !\Illuminate\Support\Facades\Route::has('student.service-orders.history')) {
                $redirectRoute = 'orders.index';
            }

            return redirect()->route($redirectRoute)
                ->with('success', 'Bukti terkirim! Menunggu verifikasi Admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 3. ADMIN: KONFIRMASI / VERIFIKASI PESANAN
    // =========================================================================

    public function updateStatus(Request $request, ServiceOrder $order)
    {
        if (!$this->isStaff()) abort(403);

        $request->validate([
            'status' => 'required|in:pending_process,cancelled,rejected'
        ]);

        DB::beginTransaction();
        try {
            // A. ADMIN TERIMA PESANAN (Verifikasi)
            if ($request->status === 'pending_process') {

                // Validasi pembayaran jika Pay Now
                if ($order->payment_method === 'pay_now') {
                    $lastPayment = $order->payments()->latest()->first();
                    if ($lastPayment) {
                        $lastPayment->update([
                            'status' => 'verified',
                            'verified_by' => Auth::id()
                        ]);
                    }
                }

                // Ubah status jadi 'pending_process' (Siap Dikerjakan)
                $order->update([
                    'status' => 'pending_process',
                    'processed_by' => Auth::id()
                ]);

                // Notifikasi ke Guru
                Notification::send($this->getTeachers(), new OrderReadyToProcessNotification($order));

                $msg = 'Pesanan DISETUJUI. Pesanan kini masuk antrian Pengajar.';
            }
            // B. ADMIN TOLAK / BATALKAN
            else {
                $order->update([
                    'status' => $request->status,
                    'processed_by' => Auth::id()
                ]);

                if ($request->status === 'rejected') {
                    $order->payments()->update(['status' => 'rejected']);
                }

                $msg = 'Pesanan telah ' . strtoupper($request->status) . '.';
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 4. PENGAJAR: SELESAIKAN ORDER
    // =========================================================================

    public function completion(ServiceOrder $order)
    {
        if ($order->status !== 'pending_process') {
            return back()->with('error', 'Pesanan ini belum siap dikerjakan atau sudah selesai.');
        }
        return view('admin.extra-service.extra-service-completion.index', compact('order'));
    }

    public function storeCompletion(Request $request, ServiceOrder $order)
    {
        $request->validate([
            'completion_note' => 'nullable|string',
            'evidence_photos' => 'nullable|array',
            'evidence_photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        DB::beginTransaction();
        try {
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

            $order->update([
                'status' => 'completed',
                'completion_note' => $request->completion_note,
                'completed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            DB::commit();

            if ($order->student) {
                Notification::send($order->student, new OrderCompletedNotification($order));
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pekerjaan Selesai! Bukti & notifikasi telah dikirim ke Wali Murid.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 5. LIST & HISTORY (MANAGEMENT)
    // =========================================================================

    public function index(Request $request)
    {
        $query = ServiceOrder::with(['student', 'extraService', 'payments', 'processor'])
            ->orderBy('created_at', 'desc');

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($subQ) use ($search) {
                    $subQ->where('student_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                })
                    ->orWhereHas('extraService', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.order-service.order-service-index.index', compact('orders'));
    }

    public function show(ServiceOrder $order)
    {
        $order->load(['payments', 'evidences.uploader', 'processor']);
        return view('admin.order-service.order-service-show.index', compact('order'));
    }

    public function destroy(ServiceOrder $order)
    {
        // Validasi hapus: hanya bisa hapus jika status belum diproses lebih jauh
        $allowedStatus = ['pending_confirmation', 'pending_payment', 'cancelled', 'rejected'];

        if (!in_array($order->status, $allowedStatus)) {
            return back()->with('error', 'Gagal! Pesanan yang sedang dikerjakan atau sudah selesai tidak dapat dihapus.');
        }

        if ($order->billing_id) {
            return back()->with('error', 'Gagal! Pesanan ini sudah masuk ke tagihan bulanan.');
        }

        $order->delete();
        return back()->with('success', 'Data pesanan berhasil dihapus permanen.');
    }
}
