<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentDailyReportController;
use App\Http\Controllers\StudentDevelopmentController;
use App\Http\Controllers\StudentMeasurementController;
use App\Http\Controllers\ServiceOrderController;

Route::group(['middleware' => ['auth:student']], function () {

    // ==========================================================
    // 1. ABSENSI & ORDERS
    // ==========================================================
    Route::get('/my-attendance', [StudentAttendanceController::class, 'index'])
        ->name('student.attendance.index');

    Route::get('/my-orders', [ServiceOrderController::class, 'history'])
        ->name('service-orders.history');


    // ==========================================================
    // 2. DAILY REPORT (Laporan Harian)
    // ==========================================================
    Route::get('student-daily-report', [StudentDailyReportController::class, 'index'])
        ->name('student.daily-report.index');

    Route::get('student-daily-report/{dailyReport}', [StudentDailyReportController::class, 'show'])
        ->name('student.daily-report.show');

    Route::post('student-daily-report/{dailyReport}/sign', [StudentDailyReportController::class, 'sign'])
        ->name('student.daily-report.sign');

    Route::get('student-daily-report/{dailyReport}/pdf', [StudentDailyReportController::class, 'downloadPdf'])
        ->name('student.daily-report.pdf');


    // ==========================================================
    // 3. MEASUREMENT (Pengukuran Fisik & KMS)
    // ==========================================================
    // List Riwayat
    Route::get('/measurements', [StudentMeasurementController::class, 'index'])
        ->name('student.measurement.index');

    // Halaman Grafik KMS (WAJIB sebelum route /{measurement} agar tidak dianggap ID)
    Route::get('/measurements/chart', [StudentMeasurementController::class, 'chart'])
        ->name('student.measurement.chart');

    // Detail Pengukuran
    Route::get('/measurements/{measurement}', [StudentMeasurementController::class, 'show'])
        ->name('student.measurement.show');


    // ==========================================================
    // 4. DEVELOPMENT REPORTS (Laporan Perkembangan - Versi Lama/Raw Data)
    // ==========================================================
    // Route ini JANGAN DIHAPUS/DIGANGGU sesuai instruksi
    Route::get('/development-reports', [StudentDevelopmentController::class, 'index'])
        ->name('student.development.index');

    Route::get('/development-reports/{report}', [StudentDevelopmentController::class, 'show'])
        ->name('student.development.show');


    // ==========================================================
    // 5. SISTEM RAPOR TERPADU (StudentReportController)
    // ==========================================================

    // A. Halaman Utama Riwayat Rapor (Gabungan Kurikulum & Tumbuh Kembang)
    Route::get('/report', [StudentReportController::class, 'history'])
        ->name('student.report.history');


    // B. RAPORT TUMBUH KEMBANG (Versi Resmi/Final untuk Orang Tua)
    // Menggunakan URL & Name berbeda agar tidak konflik dengan poin no 4

    // Detail & Tanda Tangan
    Route::get('/report-development/{id}', [StudentReportController::class, 'showDevelopment'])
        ->name('student.report.development.show');

    // Proses Simpan Tanda Tangan
    Route::post('/report-development/{id}/sign', [StudentReportController::class, 'signDevelopment'])
        ->name('student.report.development.sign');

    // Download PDF
    Route::get('/report-development/{id}/pdf', [StudentReportController::class, 'downloadDevelopmentPdf'])
        ->name('student.report.development.pdf');


    // C. RAPORT KURIKULUM (Laporan Pembelajaran)

    // Detail Raport Kurikulum
    Route::get('/report/{report}', [StudentReportController::class, 'show'])
        ->name('student.report.show');

    // Proses Tanda Tangan Raport Kurikulum
    Route::post('/report/{report}/sign', [StudentReportController::class, 'signReport'])
        ->name('student.report.sign');

    // Download PDF Raport Kurikulum
    Route::get('/report/{report}/pdf', [StudentReportController::class, 'downloadPdf'])
        ->name('student.report.pdf');
});
