<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ExtraService;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityTransaction; // 💡 Gunakan model ini

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
    | ALUR 1: PILIH SISWA (Langkah Awal)
    |--------------------------------------------------------------------------
    */
    public function selectStudent(Request $request)
    {
        // 💡 PERBAIKAN: Query ke ActivityTransaction, bukan Student langsung
        // Ambil transaksi aktivitas yang status siswanya aktif
        $query = ActivityTransaction::with('student') // Eager load relasi student
            ->where('student_status', true);

        // Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            // Cari berdasarkan nama siswa atau nomor induk di tabel students yang berelasi
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('student_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('student_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Urutkan berdasarkan nama siswa (memerlukan join atau pengurutan koleksi setelah get,
        // tapi untuk pagination efisien kita bisa urutkan berdasarkan ID transaksi atau join manual.
        // Di sini kita urutkan berdasarkan ID transaksi terbaru untuk simplifikasi, atau join jika perlu sort by name)

        // Opsi Simple: Urutkan by ID desc (Siswa terbaru)
        $activityTransactions = $query->orderBy('id', 'desc')->paginate(12);

        return view('admin.extra-service.extra-service-list.index', compact('activityTransactions'));
    }

    // ... (Method lain: catalog, checkout, store, index, show, updateStatus, destroy TETAP SAMA) ...
    // Hanya perlu penyesuaian di method 'catalog' agar menerima parameter Student ID

    public function catalog(Student $student)
    {
        // Tampilkan semua layanan aktif
        $services = ExtraService::where('is_active', true)->orderBy('name')->get();

        return view('admin.extra-service.extra-service-katalog.index', compact('student', 'services'));
    }

    public function checkout(Student $student, ExtraService $service)
    {
        return view('admin.extra-service.extra-service-order.index', compact('student', 'service'));
    }

    public function store(Request $request)
    {
        $isStaff = $this->isStaff();
        $isStudent = $this->isStudent();

        // 1. Cek Login
        if (!$isStaff && !$isStudent) {
            return redirect()->route('login')->with('error', 'Harap login terlebih dahulu.');
        }

        // 2. Validasi Dasar
        $rules = [
            'student_id' => 'required|exists:students,id',
            'extra_service_id' => 'required|exists:extra_services,id',
            'order_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:pay_now,bill_later',
            'discount_note' => 'nullable|string',
        ];

        // 3. Validasi Khusus 'is_free'
        if ($isStaff) {
            // Jika Staff, boleh isi is_free (boolean/nullable)
            $rules['is_free'] = 'nullable|boolean';
        } else {
            // Jika Student, is_free TIDAK BOLEH ADA atau HARUS NULL/FALSE
            // Kita hapus dari request untuk keamanan atau paksa validasi prohibited
            // Opsi aman: Abaikan input is_free dari student di logic penyimpanan
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $service = ExtraService::findOrFail($validated['extra_service_id']);
            $basePrice = $service->base_price;

            // 4. Logika Harga (Hanya staff yang bisa gratiskan)
            // Pastikan check $isStaff bernilai true sebelum menerima request is_free
            $isFree = $isStaff && ($request->has('is_free') && $request->is_free == '1');

            $finalPricePerUnit = $isFree ? 0 : $basePrice;
            $totalFinalPrice = $finalPricePerUnit * $validated['quantity'];

            $note = $validated['discount_note'];
            if ($isFree && empty($note)) {
                $note = 'Digratiskan oleh Admin/Pengajar';
            }

            // 5. Status Awal
            $initialStatus = $isStaff ? 'pending_process' : 'pending_confirmation';

            // 6. Processed By
            $processedBy = $isStaff ? Auth::guard('web')->id() : null;

            ServiceOrder::create([
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

            if ($isStaff) {
                return redirect()->route('orders.select-student')->with('success', 'Pesanan berhasil dibuat.');
            } else {
                return redirect()->route('student.service-orders.history')->with('success', 'Pesanan berhasil dikirim! Menunggu konfirmasi.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Error: ' . $e->getMessage());

            // Tampilkan pesan error spesifik untuk debugging (bisa dihapus saat production)
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage())->withInput();
        }
    }

    public function index(Request $request)
    {
        $query = ServiceOrder::with(['student', 'extraService', 'creator'])->orderBy('created_at', 'desc');
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        $orders = $query->paginate(10);
        return view('admin.service-orders.index', compact('orders'));
    }

    public function show(ServiceOrder $order)
    {
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
        if (!in_array($order->status, ['pending_confirmation', 'cancelled', 'rejected'])) return back()->with('error', 'Tidak bisa dihapus.');
        if ($order->billing_id) return back()->with('error', 'Sudah ditagih.');
        $order->delete();
        return back()->with('success', 'Dihapus.');
    }
}
