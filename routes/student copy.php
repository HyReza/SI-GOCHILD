<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentDailyReportController;
use App\Http\Controllers\StudentDevelopmentController;
use App\Http\Controllers\StudentMeasurementController;

Route::group(['middleware' => ['auth:student']], function () {

    // Riwayat Absensi Siswa
    Route::get('/my-attendance', [StudentAttendanceController::class, 'index'])
        ->name('student.attendance.index');

    // Riwayat Pesanan Saya (Existing)
    Route::get('/my-orders', [App\Http\Controllers\ServiceOrderController::class, 'history'])
        ->name('service-orders.history');

    // 1. List Riwayat Laporan
    Route::get('student-daily-report', [StudentDailyReportController::class, 'index'])
        ->name('student.daily-report.index');

    // 2. Detail Laporan
    Route::get('student-daily-report/{dailyReport}', [StudentDailyReportController::class, 'show'])
        ->name('student.daily-report.show');

    // 3. Proses Tanda Tangan (Simpan Gambar Signature)
    Route::post('student-daily-report/{dailyReport}/sign', [StudentDailyReportController::class, 'sign'])
        ->name('student.daily-report.sign');

    // 4. Download PDF
    Route::get('student-daily-report/{dailyReport}/pdf', [StudentDailyReportController::class, 'downloadPdf'])
        ->name('student.daily-report.pdf');

    // 1. Halaman Index (Daftar Riwayat)
    Route::get('/measurements', [StudentMeasurementController::class, 'index'])
        ->name('student.measurement.index');

    // 2. Halaman Grafik KMS
    // PENTING: Letakkan sebelum route {measurement} agar tidak dianggap sebagai ID
    Route::get('/measurements/chart', [StudentMeasurementController::class, 'chart'])
        ->name('student.measurement.chart');

    // 3. Halaman Detail Pengukuran
    Route::get('/measurements/{measurement}', [StudentMeasurementController::class, 'show'])
        ->name('student.measurement.show');

    Route::get('/development-reports', [StudentDevelopmentController::class, 'index'])
        ->name('student.development.index');

    Route::get('/development-reports/{report}', [StudentDevelopmentController::class, 'show'])
        ->name('student.development.show');


    // ==========================================================
    // LAPORAN PERKEMBANGAN AKHIR (RAPOR SEMESTER/TAHUN)
    // Menggunakan StudentReportController (Nama Baru)
    // ==========================================================

    // 1. List Riwayat Laporan (URI: /reports)
    Route::get('/report', [StudentReportController::class, 'history'])
        ->name('student.report.history');

    // 2. Detail Laporan (URI: //{report})
    Route::get('/report/{report}', [StudentReportController::class, 'show'])
        ->name('student.report.show');

    // 3. Proses Tanda Tangan Orang Tua (URI: /reports/{report}/sign)
    Route::post('/report/{report}/sign', [StudentReportController::class, 'signReport'])
        ->name('student.report.sign');

    // 4. Download PDF Laporan Akhir (URI: /reports/{report}/pdf)
    Route::get('/report/{report}/pdf', [StudentReportController::class, 'downloadPdf'])
        ->name('student.report.pdf');
});
