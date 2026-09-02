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
use App\Http\Controllers\ApiGeminiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ExtraServiceController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\StudentMmdstController;
use App\Http\Controllers\GrowthStandardController;
use App\Http\Controllers\MmdstParameterController;
use App\Http\Controllers\MmdstAssessmentController;
use App\Http\Controllers\CategoryParameterController;
use App\Http\Controllers\StudentDailyReportController;
use App\Http\Controllers\ActivityTransactionController;
use App\Http\Controllers\StudentDevelopmentReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ================= PUBLIC ROUTES =================
Route::get('/', [QuestController::class, 'index'])->name('quest.index');
Route::get('/tentang-kami', [QuestController::class, 'about'])->name('quest.about');
Route::get('/layanan-kami', [QuestController::class, 'services'])->name('quest.service');
Route::get('/blogs', [QuestController::class, 'blogs'])->name('blogs.index');
Route::get('/blogs/show/{slug}', [QuestController::class, 'blogsShow'])->name('blogs.show');
Route::get('/signature', function () {
    return view('signature');
});

// ================= AUTH ROUTES =================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // --- PROFILE MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- CORE RESOURCES ---
    Route::resource('siswa', StudentController::class);
    Route::resource('pengajar', TeacherController::class);
    Route::resource('admin', AdminController::class);


    // ==========================================
    // FITUR DAILY REPORT (Laporan Harian)
    // ==========================================
    Route::get('/daily-report', [DailyReportController::class, 'index'])->name('daily-report.index');

    // Create & Store
    Route::get('/daily-report/create/{activityTransaction}', [DailyReportController::class, 'create'])
        ->whereNumber('activityTransaction')
        ->name('daily-report.create');
    Route::post('/daily-report', [DailyReportController::class, 'store'])->name('daily-report.store');

    // Show, History, Edit, Update, Delete
    Route::get('/daily-report/{dailyReport}', [DailyReportController::class, 'show'])
        ->whereNumber('dailyReport')
        ->name('daily-report.show');
    Route::get('/daily-report/history/{activityTransaction}', [DailyReportController::class, 'history'])
        ->whereNumber('activityTransaction')
        ->name('daily-report.history');
    Route::delete('/daily-report/{dailyReport}', [DailyReportController::class, 'destroy'])
        ->whereNumber('dailyReport')
        ->name('daily-report.destroy');
    Route::get('/daily-report/{dailyReport}/edit', [DailyReportController::class, 'edit'])
        ->whereNumber('dailyReport')
        ->name('daily-report.edit');
    Route::put('/daily-report/{dailyReport}', [DailyReportController::class, 'update'])
        ->whereNumber('dailyReport')
        ->name('daily-report.update');
    Route::get('daily-report/{dailyReport}/pdf', [DailyReportController::class, 'downloadPdf'])
        ->name('daily-report.pdf');

    // Helpers / AJAX for Daily Report
    Route::get('/check-attendance/{student}/{date}', [DailyReportController::class, 'checkAttendance'])
        ->whereNumber('student')
        ->name('daily-report.check-attendance');
    Route::get('/get-subthemes/{date}', [DailyReportController::class, 'getSubthemes'])
        ->name('daily-report.get-subthemes');
    Route::get('/stimulation/suggest/{activityTransaction}/{date?}', [DailyReportController::class, 'suggestStimulation'])
        ->whereNumber('activityTransaction')
        ->name('daily-report.stimulation.suggest');


    // ==========================================
    // FITUR KURIKULUM (Themes & Materials)
    // ==========================================
    Route::resource('themes', ThemeController::class);
    Route::resource('subthemes', SubThemeController::class);
    Route::get('/generate-sub-theme-code/{themeId}', [SubThemeController::class, 'generateSubThemeCode']);

    Route::resource('material', MaterialController::class);
    Route::get('/generate-material-code/{subThemeId}', [MaterialController::class, 'generateMaterialCode']);


    // ==========================================
    // FITUR ABSENSI
    // ==========================================
    Route::get('/attendance/list', [AttendanceController::class, 'getAttendanceList'])->name('attendance.list');
    Route::get('/attendance/export', [AttendanceController::class, 'exportExcel'])->name('attendance.export');
    Route::resource('attendance', AttendanceController::class);


    // ==========================================
    // FITUR BLOGS & GALLERY
    // ==========================================
    Route::resource('gallery-activity', GalleryController::class);
    Route::resource('articles', ArticleController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{category}/check-articles', [CategoryController::class, 'checkArticles']);


    // ==========================================
    // FITUR POSYANDU & PERTUMBUHAN (Measurement)
    // ==========================================
    // CRUD Measurement
    Route::resource('measurement', MeasurementController::class)->except(['create', 'store']);
    Route::get('measurement/create/{id}', [MeasurementController::class, 'create'])->name('measurement.create');
    Route::post('measurement/create', [MeasurementController::class, 'store'])->name('measurement.store');

    // History & Search
    Route::get('measurement/history/{id}', [MeasurementController::class, 'historyMeasurement'])->name('measurement.history');
    Route::get('/measurements/search', [MeasurementController::class, 'search'])->name('measurement.search');

    // Growth Standards & Charts
    Route::get('growth-standard', [MeasurementController::class, 'getGrowthStandard'])->name('growth-standard.get');
    Route::get('/measurement/{activityTransaction}/kms-chart', [MeasurementController::class, 'showKmsChart'])->name('measurement.kmsChart');
    Route::get('/api/growth-standards', [MeasurementController::class, 'getGrowthStandards'])->name('api.growth-standards');

    // Management Growth Standard (Admin)
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
        Route::get('/search', [GrowthStandardController::class, 'search'])->name('growth-standards.search');
    });


    // ==========================================
    // FITUR KATALOG (Program & Service)
    // ==========================================
    Route::get('/catalog-programs', [ProgramController::class, 'index'])->name('catalog-programs.index');
    Route::get('/catalog-programs/edit/{id}', [ProgramController::class, 'edit'])->name('catalog-programs.edit');
    Route::put('/catalog-programs/update/{id}', [ProgramController::class, 'update'])->name('catalog-programs.update');

    Route::get('/catalog-service', [ServiceController::class, 'index'])->name('catalog-service.index');
    Route::get('/catalog-service/edit/{id}', [ServiceController::class, 'edit'])->name('catalog-service.edit');
    Route::put('/catalog-service/update/{id}', [ServiceController::class, 'update'])->name('catalog-service.update');


    // ==========================================
    // FITUR MMDST (Screening Tumbuh Kembang)
    // ==========================================
    Route::resource('category-parameter', CategoryParameterController::class);
    Route::resource('mmdst-parameter', MmdstParameterController::class);
    Route::post('mmdst-parameter/import', [MmdstParameterController::class, 'import'])->name('mmdst-parameter.import');

    // MMDST Student & Report
    Route::get('/mmdst', [StudentMmdstController::class, 'index'])->name('mmdst.index');
    Route::get('/mmdst/search', [StudentMmdstController::class, 'search'])->name('mmdst.search');
    Route::get('/mmdst/{student}/history', [StudentMmdstController::class, 'history'])->name('mmdst.history');
    Route::get('/mmdst/{student}/history/data', [StudentMmdstController::class, 'historyData'])->name('mmdst.history.data');
    Route::get('/mmdst/parameter-status', [StudentMmdstController::class, 'parameterStatus'])->name('mmdst.parameter-status');

    // Actions
    Route::post('/mmdst/{student}/start-report', [MmdstAssessmentController::class, 'startReport'])->name('mmdst.start-report');
    Route::get('/mmdst/filter-params', [MmdstAssessmentController::class, 'filterParams'])->name('mmdst.filter-params');
    Route::get('/mmdst/{student}/last-results', [MmdstAssessmentController::class, 'lastResults'])->name('mmdst.last-results');

    // Resource Assessment
    Route::resource('mmdst-assessments', MmdstAssessmentController::class);


    // ==========================================
    // FITUR RAPOR (Report Semester/Periodik)
    // ==========================================
    // Index Utama (List Siswa)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Fitur Cari (Jika menggunakan controller yang sama dengan MMDST untuk search siswa)
    Route::get('/reports/search', [StudentMmdstController::class, 'search'])->name('reports.search');

    // History Raport
    Route::get('/reports/{student}/history', [ReportController::class, 'history'])->name('reports.history');

    // Flow Pembuatan Raport
    // Step 1: Pilih Periode
    Route::get('/reports/{student}/create-period', [ReportController::class, 'selectPeriod'])->name('reports.selectPeriod');
    // Step 2: Form Penilaian
    Route::get('/reports/{student}/create', [ReportController::class, 'create'])->name('reports.create');

    // Store & CRUD
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    // Fitur Tambahan Raport (Print & AI)
    Route::get('/reports/{report}/print', [ReportController::class, 'printPdf'])->name('reports.print');
    Route::post('/reports/generate-ai', [ReportController::class, 'generateAiText'])->name('reports.generate.ai');
    Route::post('/reports/generate-ai', [ReportController::class, 'generateNarrative'])->name('generate.narrative');


    // ==========================================
    // FITUR DEVELOPMENT REPORT (Raport Tumbuh Kembang)
    // ==========================================


    // 1. Halaman Utama: Daftar Siswa (Langkah Awal)
    Route::get('/development-and-growth-report', [StudentDevelopmentReportController::class, 'index'])
        ->name('development-reports.index');

    // 2. Halaman Riwayat Raport per Siswa
    Route::get('/development-and-growth-report/student/{student}/history', [StudentDevelopmentReportController::class, 'history'])
        ->name('development-reports.history');

    // 3. STEP 1: Halaman Pilih Periode (Sebelum masuk form create)
    Route::get('/development-and-growth-report/student/{student}/select-period', [StudentDevelopmentReportController::class, 'selectPeriod'])
        ->name('development-reports.select-period');

    // 4. STEP 2: Form Buat Raport (Otomatis ambil data MMDST & Pengukuran sesuai periode)
    Route::get('/development-and-growth-report/student/{student}/create', [StudentDevelopmentReportController::class, 'create'])
        ->name('development-reports.create');

    // 5. Simpan Data Raport (Snapshot Data)
    Route::post('/development-and-growth-report/store', [StudentDevelopmentReportController::class, 'store'])
        ->name('development-reports.store');

    // 6. Lihat Detail Raport
    Route::get('/development-and-growth-report/view/{id}', [StudentDevelopmentReportController::class, 'show'])
        ->name('development-reports.show');

    // 7. Form Edit Raport (Hanya Narasi/Teks)
    Route::get('/development-and-growth-report/edit/{id}', [StudentDevelopmentReportController::class, 'edit'])
        ->name('development-reports.edit');

    // 8. Update Raport
    Route::put('/development-and-growth-report/update/{id}', [StudentDevelopmentReportController::class, 'update'])
        ->name('development-reports.update');

    // 9. Hapus Raport
    Route::delete('/development-and-growth-report/delete/{id}', [StudentDevelopmentReportController::class, 'destroy'])
        ->name('development-reports.destroy');

    // 10. Cetak PDF
    Route::get('/development-and-growth-report/print/{id}', [StudentDevelopmentReportController::class, 'print'])
        ->name('development-reports.print');

    // 10b. Cetak PDF By Student ID (Otomatis ambil report terbaru / generate langsung)
    Route::get('/development-and-growth-report/print-student/{student}', [StudentDevelopmentReportController::class, 'printByStudent'])
        ->name('development-reports.print-by-student');

    // 11. API AI Narrative Generator (AJAX)
    Route::post('/development-and-growth-report/generate-ai', [StudentDevelopmentReportController::class, 'generateAiNarrative'])
        ->name('development-reports.generate-ai');

    // ==========================================
    // FITUR PEMESANAN & EXTRA SERVICES
    // ==========================================

    // Master Data Extra Services (CRUD)
    Route::resource('extra-services', ExtraServiceController::class);

    // Flow Pemesanan
    // 1. Pilih Siswa
    Route::get('/orders/select-student', [ServiceOrderController::class, 'selectStudent'])->name('orders.select-student');
    // 2. Katalog Layanan per Siswa
    Route::get('/orders/catalog/{student}', [ServiceOrderController::class, 'catalog'])->name('orders.catalog');
    // 3. Checkout
    Route::get('/orders/checkout/{student}/{service}', [ServiceOrderController::class, 'checkout'])->name('orders.checkout');
    // 4. Store
    Route::post('/orders/store', [ServiceOrderController::class, 'store'])->name('orders.store');

    // Manajemen Pesanan
    Route::get('/orders', [ServiceOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ServiceOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [ServiceOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::delete('/orders/{order}', [ServiceOrderController::class, 'destroy'])->name('orders.destroy');

    // Pembayaran
    Route::get('/orders/{order}/payment', [ServiceOrderController::class, 'payment'])->name('orders.payment');
    Route::post('/orders/{order}/payment', [ServiceOrderController::class, 'processPayment'])->name('orders.process-payment');

    // Penyelesaian & Upload Bukti
    Route::get('/orders/{order}/complete', [ServiceOrderController::class, 'completion'])->name('orders.completion');
    Route::post('/orders/{order}/complete', [ServiceOrderController::class, 'storeCompletion'])->name('orders.store-completion');


    // CRUD Api Gemini
    Route::resource('api-gemini', ApiGeminiController::class);

    // Route khusus untuk tombol aktivasi
    Route::post('api-gemini/{id}/activate', [ApiGeminiController::class, 'activate'])->name('api-gemini.activate');
});

// ================= UTILITY ROUTES =================
Route::get('/test/env', function () {
    dd(env('DB_DATABASE'));
});

Route::get('/test', function () {
    return view('hehe');
});

Route::get('/coming-soon', function () {
    return view('error-notification.coming-soon.index');
});

// ================= AUTH IMPORTS =================
require __DIR__ . '/auth.php';
require __DIR__ . '/auth-student.php';
require __DIR__ . '/student.php';
