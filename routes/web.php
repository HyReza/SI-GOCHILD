<?php

use App\Models\Absence;
use App\Models\Measurement;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SubThemeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\StudentMmdstController;
use App\Http\Controllers\GrowthStandardController;
use App\Http\Controllers\MmdstParameterController;
use App\Http\Controllers\MmdstAssessmentController;
use App\Http\Controllers\CategoryParameterController;
use App\Http\Controllers\StudentDailyReportController;
use App\Http\Controllers\ActivityTransactionController;
use App\Http\Controllers\ExtraServiceController;


Route::get('/', [QuestController::class, 'index'])->name('quest.index');

Route::get('/tentang-kami', [QuestController::class, 'about'])->name('quest.about');

Route::get('/layanan-kami', [QuestController::class, 'services'])->name('quest.service');

Route::get('/blogs', [QuestController::class, 'blogs'])->name('blogs.index');

// Display a specific article by slug
Route::get('/blogs/show/{slug}', [QuestController::class, 'blogsShow'])->name('blogs.show');

Route::get('/signature', function () {
    return view('signature');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('siswa', StudentController::class);
    Route::resource('pengajar', TeacherController::class);
    Route::resource('admin', AdminController::class);

    // ====== DAILY REPORT (INDEX & LIST) ======
    // Tabel daftar ActivityTransaction + filter (nama siswa / layanan)
    Route::get('/daily-report', [DailyReportController::class, 'index'])
        ->name('daily-report.index');

    // ====== DAILY REPORT (CREATE) ======
    // Buka form pembuatan laporan harian untuk 1 activity_transaction tertentu
    // Controller otomatis memilih form Baby vs Children berdasarkan service_id
    Route::get('/daily-report/create/{activityTransaction}', [DailyReportController::class, 'create'])
        ->whereNumber('activityTransaction')
        ->name('daily-report.create');

    // Submit form (controller akan menyimpan ke: daily_reports + detail_baby atau detail_children)
    Route::post('/daily-report', [DailyReportController::class, 'store'])
        ->name('daily-report.store');

    // ====== DAILY REPORT (SHOW & HISTORY) ======
    // Lihat 1 laporan harian lengkap (beserta detail baby/children jika ada)
    Route::get('/daily-report/{dailyReport}', [DailyReportController::class, 'show'])
        ->whereNumber('dailyReport')
        ->name('daily-report.show');

    // Riwayat laporan harian per activity_transaction_id (paginate + filter tanggal)
    Route::get('/daily-report/history/{activityTransaction}', [DailyReportController::class, 'history'])
        ->whereNumber('activityTransaction')
        ->name('daily-report.history');

    // (Opsional) hapus laporan
    Route::delete('/daily-report/{dailyReport}', [DailyReportController::class, 'destroy'])
        ->whereNumber('dailyReport')
        ->name('daily-report.destroy');

    // ====== ENDPOINT BANTUAN (AJAX) ======
    // Cek status absensi untuk isian jam datang/pulang otomatis
    Route::get('/check-attendance/{student}/{date}', [DailyReportController::class, 'checkAttendance'])
        ->whereNumber('student')
        ->name('daily-report.check-attendance');

    // Ambil daftar subtheme/material yang aktif berdasar tanggal periode
    Route::get('/get-subthemes/{date}', [DailyReportController::class, 'getSubthemes'])
        ->name('daily-report.get-subthemes');

    // Auto-generate teks “Stimulasi” dari data MMDST (rentang usia & item belum lulus)
    // date param opsional; default = hari ini
    Route::get('/stimulation/suggest/{activityTransaction}/{date?}', [DailyReportController::class, 'suggestStimulation'])
        ->whereNumber('activityTransaction')
        ->name('daily-report.stimulation.suggest');

    Route::get('/daily-report/{dailyReport}/edit', [DailyReportController::class, 'edit'])
        ->whereNumber('dailyReport')
        ->name('daily-report.edit');

    Route::put('/daily-report/{dailyReport}', [DailyReportController::class, 'update'])
        ->whereNumber('dailyReport')
        ->name('daily-report.update');

    Route::get('daily-report/{dailyReport}/pdf', [DailyReportController::class, 'downloadPdf'])
        ->name('daily-report.pdf');



    // FITUR TEMA
    Route::resource('themes', ThemeController::class);
    Route::resource('subthemes', SubThemeController::class);
    // Route untuk generate kode sub-tema secara otomatis
    Route::get('/generate-sub-theme-code/{themeId}', [SubThemeController::class, 'generateSubThemeCode']);
    Route::resource('material', MaterialController::class);
    Route::get('/generate-material-code/{subThemeId}', [MaterialController::class, 'generateMaterialCode']);


    // FITUR ABSENSI
    Route::get('/attendance/list', [AttendanceController::class, 'getAttendanceList'])->name('attendance.list');
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance/export', [AttendanceController::class, 'exportExcel'])->name('attendance.export');

    // FITUR GALLERY ACTIVITY
    Route::resource('gallery-activity', GalleryController::class);

    // FIRUR BLOGS
    Route::resource('articles', ArticleController::class);

    // FITUR CATEGORY
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{category}/check-articles', [CategoryController::class, 'checkArticles']);

    // FITUR POSYANDU
    Route::resource('measurement', MeasurementController::class)->except(['create', 'store']);
    Route::get('measurement/create/{id}', [MeasurementController::class, 'create'])->name('measurement.create');
    Route::post('measurement/create', [MeasurementController::class, 'store'])->name('measurement.store');
    Route::get('measurement/history/{id}', [MeasurementController::class, 'historyMeasurement'])->name('measurement.history');
    Route::get('growth-standard', [MeasurementController::class, 'getGrowthStandard'])->name('growth-standard.get');

    Route::get('/measurements/search', [MeasurementController::class, 'search'])->name('measurement.search');

    // GRAFIK PERTUMBUHAN
    Route::get('/measurement/{activityTransaction}/kms-chart', [MeasurementController::class, 'showKmsChart'])->name('measurement.kmsChart');

    // Endpoint API untuk mendapatkan data standar pertumbuhan
    Route::get('/api/growth-standards', [MeasurementController::class, 'getGrowthStandards'])->name('api.growth-standards');


    // KATALOG PROGRAM
    Route::get('/catalog-programs', [ProgramController::class, 'index'])->name('catalog-programs.index');
    Route::get('/catalog-programs/edit/{id}', [ProgramController::class, 'edit'])->name('catalog-programs.edit');
    Route::put('/catalog-programs/update/{id}', [ProgramController::class, 'update'])->name('catalog-programs.update');

    // KATALOG SERVICE
    Route::get('/catalog-service', [ServiceController::class, 'index'])->name('catalog-service.index');
    Route::get('/catalog-service/edit/{id}', [ServiceController::class, 'edit'])->name('catalog-service.edit');
    Route::put('/catalog-service/update/{id}', [ServiceController::class, 'update'])->name('catalog-service.update');

    Route::resource('category-parameter', CategoryParameterController::class);

    Route::resource('mmdst-parameter', MmdstParameterController::class);

    // Import Excel
    Route::post('mmdst-parameter/import', [MmdstParameterController::class, 'import'])
        ->name('mmdst-parameter.import');

    // MMDST (daftar siswa)
    Route::get('/mmdst', [StudentMmdstController::class, 'index'])->name('mmdst.index');
    Route::get('/mmdst/search', [StudentMmdstController::class, 'search'])->name('mmdst.search');
    Route::get('/mmdst/{student}/history', [StudentMmdstController::class, 'history'])->name('mmdst.history');
    Route::get('/mmdst/{student}/history/data', [StudentMmdstController::class, 'historyData'])->name('mmdst.history.data');
    Route::get('/mmdst/parameter-status', [StudentMmdstController::class, 'parameterStatus'])->name('mmdst.parameter-status');

    // ⬇️ Ubah: tombol "Buat Laporan" → panggil ini, balas URL ke CREATE (bukan edit)
    Route::post('/mmdst/{student}/start-report', [MmdstAssessmentController::class, 'startReport'])
        ->name('mmdst.start-report');

    // AJAX: filter parameter berdasarkan student_id & assessment_date
    Route::get('/mmdst/filter-params', [MmdstAssessmentController::class, 'filterParams'])
        ->name('mmdst.filter-params');

    // (Opsional) AJAX: ambil hasil assessment terakhir siswa untuk prefill
    Route::get('/mmdst/{student}/last-results', [MmdstAssessmentController::class, 'lastResults'])
        ->name('mmdst.last-results');

    // ================= Resource utama assessment =================
    Route::resource('mmdst-assessments', MmdstAssessmentController::class);

    // FITUR RAPORT
    // --- RUTE UTAMA FITUR RAPOR ---

    // 1. Halaman utama: Daftar anak dengan tombol aksi.
    // GET /reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // 2. Halaman riwayat (history) untuk satu anak.
    // GET /reports/history/7
    Route::get('/reports/history/{activity_transaction}', [ReportController::class, 'history'])->name('reports.history');


    // --- ALUR PEMBUATAN RAPOR (Tetap sama) ---

    // 3. Halaman pilih periode.
    // GET /reports/select-period/7
    Route::get('/reports/select-period/{activity_transaction}', [ReportController::class, 'selectPeriod'])->name('reports.selectPeriod');

    // 4. Halaman form pembuatan rapor.
    // GET /reports/create/7?start_date=...
    Route::get('/reports/create/{activity_transaction}', [ReportController::class, 'create'])->name('reports.create');

    // 5. Proses penyimpanan rapor baru.
    // POST /reports/7
    Route::post('/reports/{activity_transaction}', [ReportController::class, 'store'])->name('reports.store');

    //Fitur Cari
    Route::get('/reports/search', [StudentMmdstController::class, 'search'])->name('reports.search');


    // --- PENGELOLAAN RAPOR SPESIFIK (Tetap sama) ---

    Route::prefix('reports/{report}')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'show'])->name('show');
        Route::get('/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/', [ReportController::class, 'update'])->name('update');
        Route::delete('/', [ReportController::class, 'destroy'])->name('destroy');
        Route::get('/download-pdf', [ReportController::class, 'downloadPdf'])->name('downloadPdf');
    });


    // ================= Fitur Growth Standard =================
    Route::prefix('growth-standards')->group(function () {
        Route::get('/', [GrowthStandardController::class, 'index'])->name('growth-standards.index');
        Route::get('/create', [GrowthStandardController::class, 'create'])->name('growth-standards.create');
        Route::post('/', [GrowthStandardController::class, 'store'])->name('growth-standards.store');
        Route::get('/{id}/edit', [GrowthStandardController::class, 'edit'])->name('growth-standards.edit');
        Route::put('/{id}', [GrowthStandardController::class, 'update'])->name('growth-standards.update');
        Route::delete('/{id}', [GrowthStandardController::class, 'destroy'])->name('growth-standards.destroy');

        Route::post('/{id}/toggle-active', [GrowthStandardController::class, 'toggleActive'])->name('growth-standards.toggle-active');

        Route::get('/import', [GrowthStandardController::class, 'importForm'])->name('growth-standards.import.form');
        Route::post('/import', [GrowthStandardController::class, 'import'])->name('growth-standards.import');
        Route::get('/template', [GrowthStandardController::class, 'template'])->name('growth-standards.template');

        // 🔎 Live search JSON
        Route::get('/search', [GrowthStandardController::class, 'search'])->name('growth-standards.search');
    });
    // TEST FITUR
    Route::get('/test', function () {
        return view('hehe');
    });

    // Route::resource('extra-services', App\Http\Controllers\ExtraServiceController::class)->except(['destroy']);

    // // Rute DELETE khusus untuk menggunakan form DELETE/POST dengan method PUT/DELETE
    // Route::delete('extra-services/{extraService}', [App\Http\Controllers\ExtraServiceController::class, 'destroy'])
    //     ->name('extra-services.destroy');

    // // ====================================================
    // // 1. KATALOG & PEMESANAN (Akses: Semua User Login)
    // // ====================================================

    // // Halaman Katalog Layanan (Etalase)
    // Route::get('/service-catalog', [App\Http\Controllers\ServiceOrderController::class, 'catalog'])
    //     ->name('service-orders.catalog');

    // // Halaman Riwayat Pesanan Saya (Khusus User/Siswa melihat punya sendiri)
    // Route::get('/my-orders', [App\Http\Controllers\ServiceOrderController::class, 'history'])
    //     ->name('service-orders.history');

    // // Route Resource Standar (Create & Store bisa diakses Student juga)
    // // Kita gunakan 'only' untuk membatasi apa yang terbuka umum, sisanya di group admin
    // Route::resource('service-orders', App\Http\Controllers\ServiceOrderController::class)
    //     ->only(['create', 'store', 'show']);


    // // ====================================================
    // // 2. MANAJEMEN PESANAN (Akses: Admin & Guru Saja)
    // // ====================================================


    // // Halaman Manajemen Utama (Tabel Admin)
    // Route::get('/manage-orders', [App\Http\Controllers\ServiceOrderController::class, 'index'])
    //     ->name('service-orders.index');

    // // Aksi Update Status (Terima/Tolak/Selesai)
    // Route::patch('/service-orders/{serviceOrder}/status', [App\Http\Controllers\ServiceOrderController::class, 'updateStatus'])
    //     ->name('service-orders.update-status');

    // // Edit & Delete Pesanan (Hanya Admin/Guru yang boleh edit pesanan orang lain/menghapus)
    // Route::resource('service-orders', App\Http\Controllers\ServiceOrderController::class)
    //     ->only(['edit', 'update', 'destroy']);


    // ====================================================
    // 3. MASTER KATALOG (Akses: Admin & Guru Saja)
    // ====================================================

    // Route::resource('extra-services', App\Http\Controllers\ExtraServiceController::class)
    //     ->except(['destroy']); // Destroy biasanya butuh method khusus jika pakai form delete

    // Route::delete('extra-services/{extraService}', [App\Http\Controllers\ExtraServiceController::class, 'destroy'])
    //     ->name('extra-services.destroy');





    // Langkah 1: Pilih Siswa (untuk memulai pesanan)
    Route::get('/orders/select-student', [ServiceOrderController::class, 'selectStudent'])
        ->name('orders.select-student');

    // Langkah 2: Katalog Layanan (Khusus untuk Siswa yang dipilih)
    // URL: /admin/orders/catalog/{student_id}
    Route::get('/orders/catalog/{student}', [ServiceOrderController::class, 'catalog'])
        ->name('orders.catalog');

    // Langkah 3: Form Checkout (Detail Pesanan & Harga)
    // URL: /admin/orders/checkout/{student_id}/{service_id}
    Route::get('/orders/checkout/{student}/{service}', [ServiceOrderController::class, 'checkout'])
        ->name('orders.checkout');

    // Langkah 4: Proses Simpan Pesanan (POST)
    Route::post('/orders/store', [ServiceOrderController::class, 'store'])
        ->name('orders.store');

    // Langkah 5: Daftar Pesanan Masuk (Manajemen / Inbox Pesanan)
    Route::get('/orders', [ServiceOrderController::class, 'index'])
        ->name('orders.index');

    // Detail Pesanan
    Route::get('/orders/{order}', [ServiceOrderController::class, 'show'])
        ->name('orders.show');

    // Aksi Konfirmasi / Update Status (Terima, Tolak, Proses, Selesai)
    Route::patch('/orders/{order}/status', [ServiceOrderController::class, 'updateStatus'])
        ->name('orders.update-status');

    // Hapus Pesanan (Hanya jika status masih awal)
    Route::delete('/orders/{order}', [ServiceOrderController::class, 'destroy'])
        ->name('orders.destroy');


    // Route untuk Form Pembayaran (Bisa diakses siswa/admin setelah order dibuat)
    Route::get('/orders/{order}/payment', [ServiceOrderController::class, 'payment'])->name('orders.payment');

    // Route untuk Proses Upload
    Route::post('/orders/{order}/payment', [ServiceOrderController::class, 'processPayment'])->name('orders.process-payment');


    // ====================================================
    // 2. MASTER DATA EXTRA SERVICES (CRUD Katalog Layanan)
    // ====================================================
    // Menggunakan resource controller untuk manajemen layanan (SPA, Catering, dll)
    Route::resource('extra-services', ExtraServiceController::class);
});

Route::get('/test/env', function () {
    dd(env('DB_DATABASE')); // Dump 'db' variable value one by one
});

Route::get('/coming-soon', function () {
    return view('error-notification.coming-soon.index');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/auth-student.php';
require __DIR__ . '/student.php';
