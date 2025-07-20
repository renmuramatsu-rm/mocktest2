<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AttendanceCorrectionRequestController;
use App\Http\Controllers\ExportController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// 一般ユーザー

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/',                                       [AttendanceController::class, 'index']);
    Route::post('/attendance/{attendance}/change-status', [AttendanceController::class, 'index'])->name('index');
    Route::get('/attendance/list',                        [AttendanceController::class, 'list'])->name('attendanceList');
    Route::get('/attendance/list/lastMonth',              [AttendanceController::class, 'listLastMonth'])->name('listLastMonth');
    Route::get('/attendance/list/nextMonth',              [AttendanceController::class, 'listNextMonth'])->name('listNextMonth');
    Route::post('/attendance/clock-in',                   [AttendanceController::class, 'clockIn'])->name('clockIn');
    Route::post('/attendance/clock-out',                  [AttendanceController::class, 'clockOut'])->name('clockOut');
    Route::post('/attendance/restIn',                     [AttendanceController::class, 'restIn'])->name('restIn');
    Route::post('/attendance/restOut',                    [AttendanceController::class, 'restOut'])->name('restOut');
    Route::post('/attendance/edit/{id}',                  [AttendanceController::class, 'edit'])->name('edit');
});

// 管理者ユーザー
Route::get('admin/login',   [AdminLoginController::class, 'admin_index'])->name('admin.index');
Route::post('admin/login',  [AdminLoginController::class, 'admin_login'])->name('admin.login');
Route::post('admin/logout', [AdminLoginController::class, 'admin_logout'])->name('admin.logout');
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/attendance/list',                 [AdminLoginController::class, 'adminAttendanceList'])->name('admin.attendanceList');
    Route::get('/admin/attendance/list/yesterday',       [AdminLoginController::class, 'listYesterday'])->name('listYesterday');
    Route::get('/admin/attendance/list/tomorrow',        [AdminLoginController::class, 'listTomorrow'])->name('listTomorrow');
    Route::post('/admin/attendance/{id}',                 [AdminAttendanceController::class, 'adminDetailEdit'])->name('admin.detailEdit');
    Route::get('/admin/staff/list',                      [AdminAttendanceController::class, 'staffList'])->name('admin.staffList');
    Route::get('/admin/attendance/staff/{id}',           [AdminAttendanceController::class, 'adminAttendanceStaff'])->name('admin.attendanceStaff');
    Route::get('/admin/attendance/staff/lastMonth/{id}', [AdminAttendanceController::class, 'staffLastMonth'])->name('admin.staffLastMonth');
    Route::get('/admin/attendance/staff/nextMonth/{id}', [AdminAttendanceController::class, 'staffNextMonth'])->name('admin.staffNextMonth');
    Route::post('/admin/export/{id}', [ExportController::class, 'export'])->name('admin.export');
});

Route::middleware(['web'])->group(function () {
    Route::get('/attendance/{id}',               [AttendanceController::class, 'detail'])                      ->name('detail');
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionRequestController::class, 'requestList'])->name('requestList');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AttendanceCorrectionRequestController::class, 'requestApprove'])->name('requestApprove');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [AttendanceCorrectionRequestController::class, 'requestApproved'])->name('requestApproved');
});

// メール認証
Route::get('/email/verify', function () {
    return view('registerMail');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
