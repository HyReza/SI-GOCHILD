<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ExtraService;
use App\Models\ServiceOrder;
use App\Models\Payment; // 💡 Jangan lupa import model Payment
use App\Models\ActivityTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Untuk handle file upload

class ServiceOrderController extends Controller
{
    // Helper: Cek apakah user adalah Staff (Admin/Guru) via Guard Web
    private function isStaff()
    {
        return Auth::guard('web')->check();
    }

    // Helper: Cek apakah user adalah Siswa via Guard Student
    private function isStudent()
    {
        return Auth::guard('student')->check();
    }

    /*
    |--------------------------------------------------------------------------
    | ALUR 1: PILIH SISWA & KATALOG
    |--------------------------------------------------------------------------
    */
    public function selectStudent(Request $request)
    {
        $query = ActivityTransaction::with('student')
            ->where('student_status', true);

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
        // 1. Ambil layanan yang aktif saja
        $query = ExtraService::where('is_active', true);

        // 2. Logika Pencarian (Search)
        // Mencari berdasarkan 'name' ATAU 'description'
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // 3. Urutkan berdasarkan nama
        $services = $query->orderBy('name')->get();

        return view('admin.extra-service.extra-service-katalog.index', compact('student', 'services'));
    }

    public function checkout(Student $student, ExtraService $service)
    {
        return view('admin.extra-service.extra-service-order.index', compact('student', 'service'));
    }

    /*
    |--------------------------------------------------------------------------
    | ALUR 2: SIMPAN ORDER (Update Logika Pay Now)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $isStaff = $this->isStaff();
        $isStudent = $this->isStudent();

        if (!$isStaff && !$isStudent) {
            return redirect()->route('login')->with('error', 'Harap login terlebih dahulu.');
        }

        $rules = [
            'student_id' => 'required|exists:students,id',
            'extra_service_id' => 'required|exists:extra_services,id',
            'order_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:pay_now,bill_later',
            'discount_note' => 'nullable|string',
        ];

        if ($isStaff) {
            $rules['is_free'] = 'nullable|boolean';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $service = ExtraService::findOrFail($validated['extra_service_id']);
            $basePrice = $service->base_price;

            $isFree = $isStaff && ($request->has('is_free') && $request->is_free == '1');
            $finalPricePerUnit = $isFree ? 0 : $basePrice;
            $totalFinalPrice = $finalPricePerUnit * $validated['quantity'];

            $note = $validated['discount_note'];
            if ($isFree && empty($note)) {
                $note = 'Digratiskan oleh Admin/Pengajar';
            }

            // 💡 LOGIKA STATUS BERDASARKAN PEMBAYARAN
            if ($validated['payment_method'] === 'pay_now') {
                // Jika transfer sekarang, status pending_payment (menunggu upload bukti)
                $initialStatus = 'pending_payment';
            } else {
                // Jika tagihan nanti, langsung pending_process (siap dikerjakan)
                $initialStatus = 'pending_process';
            }

            $processedBy = $isStaff ? Auth::guard('web')->id() : null;

            $order = ServiceOrder::create([
                'student_id' => $validated['student_id'],
                'extra_service_id' => $validated['extra_service_id'],
                'order_date' => $validated['order_date'],
                'quantity' => $validated['quantity'],
                'base_price_at_order' => $basePrice,
                'final_price_per_unit' => $finalPricePerUnit,
                'total_final_price' => $totalFinalPrice,
                'discount_note' => $note,
                'payment_method' => $validated['payment_method'],
                'status' => $initialStatus,
                'processed_by' => $processedBy,
                'billing_id' => null,
            ]);

            DB::commit();

            // 💡 LOGIKA REDIRECT
            if ($validated['payment_method'] === 'pay_now') {
                // Redirect ke halaman Upload Bukti Pembayaran
                return redirect()->route('orders.payment', $order->id)
                    ->with('success', 'Pesanan dibuat. Silakan upload bukti pembayaran untuk diproses.');
            }

            // Redirect Standar (Bill Later)
            if ($isStaff) {
                return redirect()->route('orders.select-student')->with('success', 'Pesanan berhasil dibuat (Masuk Tagihan).');
            } else {
                return redirect()->route('student.service-orders.history')->with('success', 'Pesanan berhasil dikirim.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage())->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ALUR 3: HALAMAN & PROSES PEMBAYARAN (BARU)
    |--------------------------------------------------------------------------
    */

    // Tampilkan Form Upload Bukti
    public function payment(ServiceOrder $order)
    {
        // Pastikan order statusnya memang pending_payment
        if ($order->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'Order ini tidak membutuhkan pembayaran saat ini.');
        }

        return view('admin.extra-service.extra-service-payment-upload.index', compact('order'));
    }

    // Proses Upload Bukti ke Tabel Payments
    public function processPayment(Request $request, ServiceOrder $order)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'bank_destination' => 'nullable|string',
            'sender_name' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Upload Gambar
            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('payment-proofs', 'public');
            }

            // 2. Simpan ke Tabel Payments (Polymorphic)
            // Menggunakan relasi payments() yang harus ada di model ServiceOrder
            $order->payments()->create([
                'amount' => $order->total_final_price,
                'proof_image' => $path,
                'bank_destination' => $request->bank_destination,
                'sender_name' => $request->sender_name,
                'status' => 'pending', // Menunggu verifikasi admin
            ]);

            // 3. Update Status Order
            // Dari 'pending_payment' -> 'pending_confirmation' (Admin perlu cek)
            $order->update([
                'status' => 'pending_confirmation'
            ]);

            DB::commit();

            if ($this->isStaff()) {
                return redirect()->route('orders.index')->with('success', 'Bukti pembayaran berhasil diupload.');
            } else {
                return redirect()->route('student.service-orders.history')->with('success', 'Bukti pembayaran terkirim! Admin akan memverifikasi.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal upload bukti: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN MANAGEMENT
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        // HAPUS 'creator' dari array with()
        $query = ServiceOrder::with(['student', 'extraService', 'payments'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);
        return view('admin.service-orders.index', compact('orders'));
    }

    public function show(ServiceOrder $order)
    {
        // Load payments history untuk dilihat admin
        $order->load('payments');
        return view('admin.service-orders.show', compact('order'));
    }

    public function updateStatus(Request $request, ServiceOrder $order)
    {
        $validated = $request->validate(['status' => 'required|in:pending_process,completed,cancelled,rejected']);
        $order->update(['status' => $validated['status'], 'processed_by' => Auth::guard('web')->id()]);
        return back()->with('success', 'Status diperbarui.');
    }

    public function destroy(ServiceOrder $order)
    {
        if (!in_array($order->status, ['pending_confirmation', 'pending_payment', 'cancelled', 'rejected'])) {
            return back()->with('error', 'Order yang sedang diproses/selesai tidak bisa dihapus.');
        }
        if ($order->billing_id) return back()->with('error', 'Sudah masuk tagihan.');

        $order->delete();
        return back()->with('success', 'Dihapus.');
    }
}
