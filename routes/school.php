<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\School\DashboardController;

use App\Http\Controllers\School\AcademicSessionController;
use App\Http\Controllers\School\EnrollmentController;
use App\Http\Controllers\School\ClassController;

Route::middleware(['auth', 'role:administration,admin,institute', 'mobile.verified', \App\Http\Middleware\EnsureOrganizationExists::class, 'enforce.subscription'])->prefix('school')->name('school.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Academic Sessions Management
    Route::get('/sessions', [AcademicSessionController::class, 'index'])->name('sessions.index');
    Route::post('/sessions', [AcademicSessionController::class, 'store'])->name('sessions.store');
    Route::put('/sessions/{id}', [AcademicSessionController::class, 'update'])->name('sessions.update');
    // Renamed the parameter to 'id' to match controller
    Route::delete('/sessions/{id}', [AcademicSessionController::class, 'destroy'])->name('sessions.destroy');
    Route::post('/sessions/{id}/set-active', [AcademicSessionController::class, 'setActive'])->name('sessions.set-active');

    // Enrollment Mapping
    Route::get('/enrollment', [EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::post('/enrollment/map', [EnrollmentController::class, 'map'])->name('enrollment.map');
    Route::get('/enrollment/mapped', [EnrollmentController::class, 'mapped'])->name('enrollment.mapped');

    // Class & Section Management
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [ClassController::class, 'storeClass'])->name('classes.store');
    Route::put('/classes/{id}', [ClassController::class, 'updateClass'])->name('classes.update');
    Route::delete('/classes/{id}', [ClassController::class, 'destroyClass'])->name('classes.destroy');
    Route::post('/sections', [ClassController::class, 'storeSection'])->name('sections.store');
    Route::delete('/sections/{id}', [ClassController::class, 'destroySection'])->name('sections.destroy');
    
    // Student Management
    Route::resource('students', \App\Http\Controllers\School\StudentController::class)->only(['create', 'store', 'update']);

    // Staff Management
    Route::resource('staff', \App\Http\Controllers\School\StaffController::class);
    Route::post('staff/{staff}/map', [\App\Http\Controllers\School\StaffController::class, 'assignMapping'])->name('staff.map');
    Route::delete('staff/mapping/{mapping}', [\App\Http\Controllers\School\StaffController::class, 'removeMapping'])->name('staff.mapping.destroy');

    // Attendance Module
    Route::get('attendance', [\App\Http\Controllers\School\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/staff', [\App\Http\Controllers\School\AttendanceController::class, 'staffIndex'])->name('attendance.staff');
    Route::post('attendance/staff', [\App\Http\Controllers\School\AttendanceController::class, 'staffStore'])->name('attendance.staff.store');
    Route::get('attendance/students', [\App\Http\Controllers\School\AttendanceController::class, 'studentIndex'])->name('attendance.students');
    Route::post('attendance/students', [\App\Http\Controllers\School\AttendanceController::class, 'studentStore'])->name('attendance.students.store');
    
    Route::get('attendance/report/staff', [\App\Http\Controllers\School\AttendanceController::class, 'staffReport'])->name('attendance.report.staff');
    Route::get('attendance/report/students', [\App\Http\Controllers\School\AttendanceController::class, 'studentReport'])->name('attendance.report.students');
    Route::get('attendance/report/percentage', [\App\Http\Controllers\School\AttendanceController::class, 'studentPercentageReport'])->name('attendance.report.percentage');
    Route::get('attendance/report/percentage/export', [\App\Http\Controllers\School\AttendanceController::class, 'studentPercentageReportExport'])->name('attendance.report.percentage.export');
    Route::get('attendance/report/staff/export', [\App\Http\Controllers\School\AttendanceController::class, 'staffReportExport'])->name('attendance.report.staff.export');
    Route::get('attendance/report/students/export', [\App\Http\Controllers\School\AttendanceController::class, 'studentReportExport'])->name('attendance.report.students.export');

    // Timetable Module
    Route::get('timetable', [\App\Http\Controllers\School\TimetableController::class, 'index'])->name('timetable.index');
    Route::get('timetable/view', [\App\Http\Controllers\School\TimetableController::class, 'view'])->name('timetable.view');
    Route::get('timetable/pdf', [\App\Http\Controllers\School\TimetableController::class, 'generatePdf'])->name('timetable.pdf');
    Route::post('timetable', [\App\Http\Controllers\School\TimetableController::class, 'store'])->name('timetable.store');
    Route::put('timetable/{timetable}', [\App\Http\Controllers\School\TimetableController::class, 'update'])->name('timetable.update');
    Route::delete('timetable/{timetable}', [\App\Http\Controllers\School\TimetableController::class, 'destroy'])->name('timetable.destroy');

    // Fee Management Module
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\FeeController::class, 'index'])->name('index');
        Route::get('/config', [\App\Http\Controllers\School\FeeController::class, 'config'])->name('config');
        Route::post('/config', [\App\Http\Controllers\School\FeeController::class, 'storeConfig'])->name('config.store');
        Route::get('/collect/{enrollment}', [\App\Http\Controllers\School\FeeController::class, 'collect'])->name('collect');
        Route::post('/payment', [\App\Http\Controllers\School\FeeController::class, 'storePayment'])->name('payment.store');
        
        // Razorpay Integration
        Route::post('/razorpay/order', [\App\Http\Controllers\School\FeeController::class, 'createRazorpayOrder'])->name('payment.razorpay.order');
        Route::post('/razorpay/verify', [\App\Http\Controllers\School\FeeController::class, 'verifyRazorpayPayment'])->name('payment.razorpay.verify');
    });

    // Notification Management Module
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\NotificationController::class, 'index'])->name('index');
        Route::get('/compose', [\App\Http\Controllers\School\NotificationController::class, 'compose'])->name('compose');
        Route::post('/send', [\App\Http\Controllers\School\NotificationController::class, 'send'])->name('send');
        Route::get('/logs', [\App\Http\Controllers\School\NotificationController::class, 'logs'])->name('logs');
        
        // Template Management
        Route::resource('templates', \App\Http\Controllers\School\NotificationTemplateController::class)->except(['show']);
    });

    // Leave Management Module
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\LeaveController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\School\LeaveController::class, 'create'])->name('create');
        Route::get('/{id}', [\App\Http\Controllers\School\LeaveController::class, 'show'])->name('show');
        Route::post('/', [\App\Http\Controllers\School\LeaveController::class, 'store'])->name('store');
        Route::patch('/{id}/status', [\App\Http\Controllers\School\LeaveController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [\App\Http\Controllers\School\LeaveController::class, 'destroy'])->name('destroy');
    });

    // School Feed Module
    Route::prefix('feed')->name('feed.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\SchoolFeedController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\School\SchoolFeedController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\School\SchoolFeedController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/comment', [\App\Http\Controllers\School\SchoolFeedController::class, 'storeComment'])->name('comment');
    });

    // Homework Management Module
    Route::resource('homework', \App\Http\Controllers\School\HomeworkController::class);
    Route::get('homework/submissions/{submission}/evaluate', [\App\Http\Controllers\School\HomeworkController::class, 'showEvaluate'])->name('homework.evaluate.show');
    Route::post('homework/submissions/{submission}/evaluate', [\App\Http\Controllers\School\HomeworkController::class, 'evaluate'])->name('homework.evaluate');
});

// Shared School Features (Accessible by both Administration and Students)
Route::middleware(['auth', 'mobile.verified', 'role:administration,admin,institute,student', \App\Http\Middleware\EnsureOrganizationExists::class, 'enforce.subscription'])->prefix('school')->name('school.')->group(function () {
    Route::prefix('live-classes')->name('live-classes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\LiveClassController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\School\LiveClassController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\School\LiveClassController::class, 'update'])->name('update');
        Route::get('/join/{id}', [\App\Http\Controllers\School\LiveClassController::class, 'join'])->name('join');
        Route::delete('/{id}', [\App\Http\Controllers\School\LiveClassController::class, 'destroy'])->name('destroy');
    });

    // Personal Leave Management
    Route::prefix('my-leaves')->name('leaves.personal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\LeaveController::class, 'personalIndex'])->name('index');
        Route::get('/apply', [\App\Http\Controllers\School\LeaveController::class, 'personalCreate'])->name('create');
        Route::get('/{id}', [\App\Http\Controllers\School\LeaveController::class, 'personalShow'])->name('show');
        Route::post('/', [\App\Http\Controllers\School\LeaveController::class, 'personalStore'])->name('store');
    });
});
