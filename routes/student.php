<?php

use Illuminate\Support\Facades\Route;
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
});
