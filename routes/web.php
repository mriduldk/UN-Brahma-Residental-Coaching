<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $applicationForms = \App\Models\ApplicationForm::with('institution')->where('status', 'active')->orderBy('created_at', 'asc')->get();
    $notices = \App\Models\PublicNotice::where('is_active', true)->orderBy('created_at', 'desc')->get();
    return view('welcome', compact('applicationForms', 'notices'));
})->name('welcome');

// Public Form Share Route
Route::get('/f/{code}', [App\Http\Controllers\PublicFormController::class, 'show'])->name('forms.public.show');

// Static Pages
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/help', 'pages.help')->name('help');
Route::post('/contact-inquiry', [App\Http\Controllers\ContactController::class, 'sendInquiry'])->name('contact.inquiry');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/about', 'pages.about')->name('about');

// Institutional Demo Suite Routes
Route::prefix('demo')->name('demo.')->group(function () {
    Route::get('/admin/dashboard', function () { return view('demo.admin.dashboard'); })->name('admin.dashboard');
    Route::get('/admin/institutes', function () { return view('demo.admin.institutes'); })->name('admin.institutes');
    Route::get('/admin/forms', function () { return view('demo.admin.forms'); })->name('admin.forms');
    Route::get('/admin/applications', function () { return view('demo.admin.applications'); })->name('admin.applications');
    
    Route::get('/student/dashboard', function () { return view('demo.student.dashboard'); })->name('student.dashboard');
    Route::get('/student/application-form', function () { return view('demo.student.application-form'); })->name('student.application-form');
    Route::get('/student/application-view', function () { return view('demo.student.application-view'); })->name('student.application-view');
    Route::get('/student/payment', function () { return view('demo.student.payment'); })->name('student.payment');
});

// Public Discover Routes
Route::get('/discover', [\App\Http\Controllers\DiscoverController::class, 'index'])->name('discover.index');
Route::get('/discover/institute/{id}', [\App\Http\Controllers\DiscoverController::class, 'institute'])->name('discover.institute');

// Public Payment Callback for Razorpay Hosted Checkout
Route::post('/student/payment/process', [\App\Http\Controllers\StudentController::class, 'processPayment'])
    ->name('student.application.payment.process')
    ->withoutMiddleware('web');

Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    return match($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'administration' => redirect()->route('administration.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        'institute' => redirect()->route('institute.dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'mobile.verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin', 'mobile.verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/lead-tracking', [\App\Http\Controllers\AdminController::class, 'leadTracking'])->name('lead-tracking');
    Route::post('/organizations/tracking/update', [\App\Http\Controllers\AdminController::class, 'updateTracking'])->name('organizations.tracking.update');
    Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class);
    
    // Impersonation Routes
    Route::get('/impersonate', [\App\Http\Controllers\AdminController::class, 'impersonateUsers'])->name('impersonate.index');
    Route::post('/impersonate/{id}', [\App\Http\Controllers\AdminController::class, 'impersonate'])->name('impersonate.login');
    
    // Drill-down views for Super Admin
    Route::get('institutions/{id}', [\App\Http\Controllers\Admin\InstitutionController::class, 'show'])->name('institutions.show');
    Route::get('forms/{id}', [\App\Http\Controllers\Admin\ApplicationFormController::class, 'show'])->name('forms.show');
    Route::get('student-applications/{id}', [\App\Http\Controllers\Admin\StudentApplicationController::class, 'show'])->name('student-applications.show');

    // Student Application Management for Super Admin
    Route::get('student-applications/{id}', [\App\Http\Controllers\Admin\StudentApplicationController::class, 'show'])->name('student-applications.show');
});

// Impersonation Action Routes
Route::post('/impersonate/leave', [\App\Http\Controllers\AdminController::class, 'leaveImpersonation'])->name('impersonate.leave')->middleware('auth');



Route::middleware(['auth', 'role:institute', 'mobile.verified'])->prefix('institute')->name('institute.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Institute\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('institutions', \App\Http\Controllers\Institute\InstitutionController::class);
    
    // Institution User Management for Institute Role
    Route::get('/institutions/{id}/users', [\App\Http\Controllers\InstitutionUserController::class, 'index'])->name('institutions.users');
    Route::post('/institutions/{id}/users', [\App\Http\Controllers\InstitutionUserController::class, 'store'])->name('institutions.users.store');
    Route::put('/institutions/{id}/users/{userId}', [\App\Http\Controllers\InstitutionUserController::class, 'update'])->name('institutions.users.update');
    Route::delete('/institutions/{id}/users/{userId}', [\App\Http\Controllers\InstitutionUserController::class, 'destroy'])->name('institutions.users.destroy');

    // Application Form Management for Institute Role
    Route::get('/forms', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'create'])->name('forms.create');
    Route::post('/forms/store', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'store'])->name('forms.store');
    Route::get('/forms/{id}', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'show'])->name('forms.show');
    Route::get('/forms/{id}/edit', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'edit'])->name('forms.edit');
    Route::put('/forms/{id}', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'update'])->name('forms.update');
    Route::delete('/forms/{id}', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'destroy'])->name('forms.destroy');
    Route::get('/forms/applications', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'applications'])->name('forms.applications');
    Route::get('/forms/applications/{id}', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'applicationDetail'])->name('forms.applications.detail');
    Route::post('/forms/applications/{id}/status', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'updateApplicationStatus'])->name('forms.applications.status');
    Route::post('/forms/applications/{id}/approval', [\App\Http\Controllers\Institute\ApplicationFormController::class, 'updateApprovalStatus'])->name('forms.applications.approval');
    
    // Admission Routes for Institute Role
    Route::get('/admissions', [\App\Http\Controllers\Institute\AdmissionsController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{id}', [\App\Http\Controllers\Institute\AdmissionsController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{id}/payment', [\App\Http\Controllers\Institute\AdmissionsController::class, 'storePayment'])->name('admissions.payment.store');
    Route::get('/admissions/{id}/acknowledgment', [\App\Http\Controllers\Institute\AdmissionsController::class, 'downloadAcknowledgment'])->name('admissions.acknowledgment.download');
    Route::post('/admissions/{id}/payment/callback', [\App\Http\Controllers\Institute\AdmissionsController::class, 'handlePaymentCallback'])->name('admissions.payment.callback');
    Route::post('/admissions/{id}/payment/failure', [\App\Http\Controllers\Institute\AdmissionsController::class, 'paymentFailure'])->name('admissions.payment.failure');
    Route::post('/admissions/{id}/payment/{paymentId}/settle', [\App\Http\Controllers\Institute\AdmissionsController::class, 'settlePayment'])->name('admissions.payment.settle');
    Route::get('/enrolled-students', [\App\Http\Controllers\Institute\AdmissionsController::class, 'enrolled'])->name('admissions.enrolled');

    // Admission Configuration
    Route::resource('admission-configs', \App\Http\Controllers\Institute\AdmissionConfigController::class);

    // Finance Reports
    Route::get('/finance', [\App\Http\Controllers\Institute\FinanceController::class, 'index'])->name('finance');
    Route::get('/finance/{type}/{id}', [\App\Http\Controllers\Institute\FinanceController::class, 'show'])->name('finance.show');
    Route::post('/finance/refresh', [\App\Http\Controllers\Institute\FinanceController::class, 'refresh'])->name('finance.refresh');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::prefix('student')->name('student.')->middleware(['auth', 'mobile.verified'])->group(function () {
    // Onboarding Routes (Excluded from profile check to avoid loop, handled inside middleware)
    Route::middleware(['ensure_student_profile'])->group(function () {
        Route::get('/onboarding', [\App\Http\Controllers\StudentController::class, 'onboarding'])->name('onboarding'); // This route is actually checked in middleware to redirect AWAY if profile exists
        Route::post('/onboarding', [\App\Http\Controllers\StudentController::class, 'storeOnboarding'])->name('onboarding.store');
    });

    // Core Student Routes (Require Profile)
    Route::middleware(['ensure_student_profile'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/applications', [\App\Http\Controllers\StudentController::class, 'allApplications'])->name('applications.index');
        Route::get('/applications/institute/{id}', [\App\Http\Controllers\StudentController::class, 'instituteApplications'])->name('applications.institute');
        Route::get('/my-applications', [\App\Http\Controllers\StudentController::class, 'myApplications'])->name('applications.history');
        Route::get('/admin-cards', [\App\Http\Controllers\StudentController::class, 'adminCards'])->name('admin-cards.index');
        Route::get('/profile', [\App\Http\Controllers\StudentController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [\App\Http\Controllers\StudentController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile/update', [\App\Http\Controllers\StudentController::class, 'updateProfile'])->name('profile.update')->middleware('throttle:profile-update');
        Route::get('/application-form', [\App\Http\Controllers\StudentController::class, 'applicationForm'])->name('application.form');
        Route::get('/application/{id}', [\App\Http\Controllers\StudentController::class, 'showApplication'])->name('application.show');
        Route::delete('/application/{id}', [\App\Http\Controllers\StudentController::class, 'destroyApplication'])->name('application.destroy');
        Route::get('/application/{id}/download', [\App\Http\Controllers\StudentController::class, 'downloadApplicationPdf'])->name('application.download');
        Route::get('/application/{id}/acknowledgment', [\App\Http\Controllers\StudentController::class, 'downloadAcknowledgment'])->name('application.acknowledgment');
        Route::get('/application/{id}/admin-card', [\App\Http\Controllers\StudentController::class, 'downloadAdminCard'])->name('application.admin-card');
        Route::get('/application/{id}/admin-card/print', [\App\Http\Controllers\StudentController::class, 'printAdminCard'])->name('application.admin-card.print');
        Route::get('/application-preview', [\App\Http\Controllers\StudentController::class, 'applicationPreview'])->name('application.preview.get');
        Route::post('/application-preview', [\App\Http\Controllers\StudentController::class, 'applicationPreview'])->name('application.preview')->middleware('throttle:application-submission');
        Route::post('/preview', [\App\Http\Controllers\StudentController::class, 'applicationPreview'])->middleware('throttle:application-submission');
        Route::post('/store', [\App\Http\Controllers\StudentController::class, 'storeApplication'])->name('application.store')->middleware('throttle:application-submission');
        Route::post('/save-step', [\App\Http\Controllers\StudentController::class, 'saveStep'])->name('application.save-step');
        Route::get('/payment', [\App\Http\Controllers\StudentController::class, 'applicationPayment'])->name('application.payment');
        Route::post('/payment/failure', [\App\Http\Controllers\StudentController::class, 'paymentFailure'])->name('application.payment.failure')->middleware('throttle:payment');
        Route::post('/payment/refresh', [\App\Http\Controllers\StudentController::class, 'refreshPaymentStatus'])->name('application.payment.refresh')->middleware('throttle:payment');
        
        // EMI Installment Payments
        Route::post('/application/{id}/payment/emi', [\App\Http\Controllers\StudentController::class, 'initiateEmiPayment'])->name('application.payment.emi')->middleware('throttle:payment');
        Route::post('/application/{id}/payment/emi/callback', [\App\Http\Controllers\StudentController::class, 'handleEmiPaymentCallback'])->name('application.payment.emi.callback')->middleware('throttle:payment');
        Route::post('/application/{id}/payment/emi/failure', [\App\Http\Controllers\StudentController::class, 'emiPaymentFailure'])->name('application.payment.emi.failure')->middleware('throttle:payment');
        Route::get('/documents', [\App\Http\Controllers\StudentController::class, 'documents'])->name('documents');
        Route::post('/documents', [\App\Http\Controllers\StudentController::class, 'storeDocument'])->name('documents.store')->middleware('throttle:file-upload');
        Route::delete('/documents/{id}', [\App\Http\Controllers\StudentController::class, 'deleteDocument'])->name('documents.destroy');
        Route::post('/documents/other', [\App\Http\Controllers\StudentController::class, 'storeOtherDocument'])->name('documents.other.store')->middleware('throttle:file-upload');
        Route::delete('/documents/other/{id}', [\App\Http\Controllers\StudentController::class, 'deleteOtherDocument'])->name('documents.other.destroy');
        Route::get('/tools', [\App\Http\Controllers\StudentController::class, 'tools'])->name('tools');
        
        // Student Finance
        Route::get('/finance', [\App\Http\Controllers\Student\FinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/{type}/{id}', [\App\Http\Controllers\Student\FinanceController::class, 'show'])->name('finance.show');
        Route::post('/finance/refresh', [\App\Http\Controllers\Student\FinanceController::class, 'refresh'])->name('finance.refresh');

        // Student Academic Fees
        Route::get('/academic-fees', [\App\Http\Controllers\Student\StudentFeeController::class, 'index'])->name('fees.index');
        Route::post('/academic-fees/order', [\App\Http\Controllers\Student\StudentFeeController::class, 'createRazorpayOrder'])->name('fees.order');
        Route::post('/academic-fees/verify', [\App\Http\Controllers\Student\StudentFeeController::class, 'verifyRazorpayPayment'])->name('fees.verify');

        // Student Feed
        Route::get('/feed', [\App\Http\Controllers\Student\StudentFeedController::class, 'index'])->name('feed.index');
        Route::post('/feed/{id}/like', [\App\Http\Controllers\Student\StudentFeedController::class, 'like'])->name('feed.like');
        Route::post('/feed/{id}/vote', [\App\Http\Controllers\Student\StudentFeedController::class, 'vote'])->name('feed.vote');
        Route::post('/feed/{id}/comment', [\App\Http\Controllers\Student\StudentFeedController::class, 'comment'])->name('feed.comment');

        // Student Homework
        Route::prefix('homework')->name('homework.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\StudentHomeworkController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Student\StudentHomeworkController::class, 'show'])->name('show');
            Route::post('/{id}/submit', [\App\Http\Controllers\Student\StudentHomeworkController::class, 'submit'])->name('submit');
        });

        // Student Notifications
        Route::get('/notifications', [\App\Http\Controllers\Student\StudentNotificationController::class, 'index'])->name('notifications.index');

        // Student Timetable
        Route::get('/timetable', [\App\Http\Controllers\Student\StudentTimetableController::class, 'index'])->name('timetable.index');

        // Exam Routes for Students
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\AttemptController::class, 'index'])->name('index');
            Route::get('/{exam}', [\App\Http\Controllers\Student\AttemptController::class, 'show'])->name('show');
            Route::post('/{exam}/start', [\App\Http\Controllers\Student\AttemptController::class, 'start'])->name('start');
            Route::get('/attempt/{attempt}', [\App\Http\Controllers\Student\AttemptController::class, 'attempt'])->name('attempt');
            Route::post('/attempt/{attempt}/submit', [\App\Http\Controllers\Student\AttemptController::class, 'submit'])->name('submit');
            Route::get('/attempt/{attempt}/results', [\App\Http\Controllers\Student\AttemptController::class, 'results'])->name('results');
        });
    });
});

Route::middleware(['auth', 'mobile.verified', \App\Http\Middleware\EnsureOrganizationExists::class, 'enforce.subscription'])->prefix('administration')->name('administration.')->group(function () {
    // Subscription & Billing
    Route::get('/billing', [\App\Http\Controllers\Administration\SubscriptionController::class, 'index'])->name('subscription.billing');
    Route::post('/billing/order', [\App\Http\Controllers\Administration\SubscriptionController::class, 'createOrder'])->name('subscription.order');
    Route::post('/billing/verify', [\App\Http\Controllers\Administration\SubscriptionController::class, 'verifyPayment'])->name('subscription.verify');
    Route::post('/billing/refresh/{orderId}', [\App\Http\Controllers\Administration\SubscriptionController::class, 'refreshPaymentStatus'])->name('subscription.refresh');

    // Onboarding Routes
    Route::get('/onboarding', [\App\Http\Controllers\AdministrationController::class, 'onboarding'])->name('onboarding')->withoutMiddleware([\App\Http\Middleware\EnsureOrganizationExists::class, 'enforce.subscription']); 
    Route::post('/onboarding', [\App\Http\Controllers\AdministrationController::class, 'storeOnboarding'])->name('onboarding.store')->withoutMiddleware([\App\Http\Middleware\EnsureOrganizationExists::class, 'enforce.subscription']);
    Route::post('/onboarding/business-type', [\App\Http\Controllers\AdministrationController::class, 'selectBusinessType'])->name('onboarding.business-type.select')->withoutMiddleware([\App\Http\Middleware\EnsureOrganizationExists::class, 'enforce.subscription']);


    Route::get('/dashboard', [\App\Http\Controllers\AdministrationController::class, 'dashboard'])->name('dashboard');
    Route::resource('notices', \App\Http\Controllers\Administration\PublicNoticeController::class)->except(['show']);
    Route::get('/forms', [\App\Http\Controllers\AdministrationController::class, 'forms'])->name('forms');
    Route::get('/forms/create', [\App\Http\Controllers\AdministrationController::class, 'formBuilder'])->name('forms.create');
    Route::post('/forms/store', [\App\Http\Controllers\AdministrationController::class, 'storeForm'])->name('forms.store');
    Route::get('/forms/{id}', [\App\Http\Controllers\AdministrationController::class, 'showForm'])->name('forms.show');
    Route::get('/forms/{id}/edit', [\App\Http\Controllers\AdministrationController::class, 'editForm'])->name('forms.edit');
    Route::put('/forms/{id}', [\App\Http\Controllers\AdministrationController::class, 'updateForm'])->name('forms.update');
    Route::delete('/forms/{id}', [\App\Http\Controllers\AdministrationController::class, 'destroyForm'])->name('forms.destroy');


    
    // Institution User Management
    Route::get('/institutions/{id}/users', [\App\Http\Controllers\InstitutionUserController::class, 'index'])->name('institutions.users');
    Route::post('/institutions/{id}/users', [\App\Http\Controllers\InstitutionUserController::class, 'store'])->name('institutions.users.store');
    Route::put('/institutions/{id}/users/{userId}', [\App\Http\Controllers\InstitutionUserController::class, 'update'])->name('institutions.users.update');
    Route::delete('/institutions/{id}/users/{userId}', [\App\Http\Controllers\InstitutionUserController::class, 'destroy'])->name('institutions.users.destroy');
    Route::get('/forms/applications', [\App\Http\Controllers\AdministrationController::class, 'formApplications'])->name('forms.applications'); // Simplified for now
    Route::get('/applications/export', [\App\Http\Controllers\AdministrationController::class, 'exportApplications'])->name('applications.export');
    
    // User Management
    Route::get('/users', [\App\Http\Controllers\AdministrationController::class, 'manageUsers'])->name('users');
    Route::post('/users', [\App\Http\Controllers\AdministrationController::class, 'addUser'])->name('users.add');
    Route::put('/users/{id}', [\App\Http\Controllers\AdministrationController::class, 'updateUserRole'])->name('users.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\AdministrationController::class, 'removeUser'])->name('users.remove');

    Route::get('/applications', [\App\Http\Controllers\AdministrationController::class, 'applications'])->name('applications');
    Route::get('/reports', [\App\Http\Controllers\AdministrationController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\AdministrationController::class, 'exportReports'])->name('reports.export');
    Route::get('/applications/{id}', [\App\Http\Controllers\AdministrationController::class, 'applicationDetail'])->name('applications.detail');
    Route::post('/applications/{id}/status', [\App\Http\Controllers\AdministrationController::class, 'updateApplicationStatus'])->name('applications.status');
    Route::post('/applications/{id}/approval', [\App\Http\Controllers\AdministrationController::class, 'updateApprovalStatus'])->name('applications.approval');

    // Admin Card Generation (Dedicated for Submitted Applications)
    Route::get('/admin-cards', [\App\Http\Controllers\AdministrationController::class, 'adminCards'])->name('admin-cards.index');
    Route::get('/admin-cards/logs', [\App\Http\Controllers\AdministrationController::class, 'adminCardLogs'])->name('admin-cards.logs');
    Route::get('/admin-cards/{id}/download', [\App\Http\Controllers\AdministrationController::class, 'downloadAdminCard'])->name('admin-cards.download');
    Route::get('/admin-cards/{id}/print', [\App\Http\Controllers\AdministrationController::class, 'printAdminCard'])->name('admin-cards.print');
    Route::post('/admin-cards/bulk-download', [\App\Http\Controllers\AdministrationController::class, 'bulkDownloadAdminCards'])->name('admin-cards.bulk-download');
    Route::get('/admin-cards/export', [\App\Http\Controllers\AdministrationController::class, 'exportAdminCards'])->name('admin-cards.export');
    Route::post('/admin-cards/generate-roll-numbers', [\App\Http\Controllers\AdministrationController::class, 'generateRollNumbers'])->name('admin-cards.generate-roll-numbers');

    // Attendance Sheet Generation (Examination Center Wise)
    Route::get('/attendance-sheets', [\App\Http\Controllers\AdministrationController::class, 'attendanceSheets'])->name('attendance-sheets.index');
    Route::get('/attendance-sheet', fn() => redirect()->route('administration.attendance-sheets.index'));
    Route::get('/attendance-sheets/pdf', [\App\Http\Controllers\AdministrationController::class, 'attendanceSheetPdf'])->name('attendance-sheets.pdf');
    Route::get('/attendance-sheets/roll-numbers-pdf', [\App\Http\Controllers\AdministrationController::class, 'attendanceSheetRollNumbersPdf'])->name('attendance-sheets.roll-numbers-pdf');
    Route::get('/attendance-sheets/excel', [\App\Http\Controllers\AdministrationController::class, 'attendanceSheetExcel'])->name('attendance-sheets.excel');
    Route::get('/attendance-sheets/csv', [\App\Http\Controllers\AdministrationController::class, 'attendanceSheetCsv'])->name('attendance-sheets.csv');
    Route::get('/attendance-sheets/print', [\App\Http\Controllers\AdministrationController::class, 'attendanceSheetPrint'])->name('attendance-sheets.print');
    Route::get('/finance', [\App\Http\Controllers\AdministrationController::class, 'finance'])->name('finance');
    Route::get('/finance/{type}/{id}', [\App\Http\Controllers\AdministrationController::class, 'financeDetail'])->name('finance.show');
    Route::post('/finance/refresh', [\App\Http\Controllers\AdministrationController::class, 'refreshFinanceStatus'])->name('finance.refresh');
    Route::get('/configuration', [\App\Http\Controllers\AdministrationController::class, 'configuration'])->name('configuration');
    Route::get('/configuration/quota/pay', [\App\Http\Controllers\AdministrationController::class, 'initiateQuotaPayment'])->name('configuration.quota.pay');
    Route::post('/configuration/quota/verify', [\App\Http\Controllers\AdministrationController::class, 'verifyQuotaPayment'])->name('configuration.quota.verify');
    Route::post('/configuration/quota/fail', [\App\Http\Controllers\AdministrationController::class, 'handleQuotaPaymentFailure'])->name('configuration.quota.fail');
    Route::post('/configuration/quota/refresh/{orderId}', [\App\Http\Controllers\AdministrationController::class, 'refreshQuotaPayment'])->name('configuration.quota.refresh');
    Route::post('/forms/pay-and-store', [\App\Http\Controllers\AdministrationController::class, 'initiateIntegratedPayment'])->name('forms.payAndStore');
    Route::get('/configuration/quota/transactions', [\App\Http\Controllers\AdministrationController::class, 'quotaTransactions'])->name('configuration.quota.transactions');
    Route::get('/configuration/quota/invoice/{transactionId}', [\App\Http\Controllers\AdministrationController::class, 'quotaInvoice'])->name('configuration.quota.invoice');
    Route::get('/configuration/quota/invoice/{transactionId}/download', [\App\Http\Controllers\AdministrationController::class, 'downloadQuotaInvoice'])->name('configuration.quota.invoice.download');
    Route::get('/profile', [\App\Http\Controllers\AdministrationController::class, 'profile'])->name('profile');
    Route::put('/organization/update', [\App\Http\Controllers\AdministrationController::class, 'updateOrganization'])->name('organization.update');

    // New Form Link Management Routes
    Route::get('/forms/{id}/links', [\App\Http\Controllers\AdministrationController::class, 'getFormLinks'])->name('forms.links.get');
    Route::post('/forms/links', [\App\Http\Controllers\AdministrationController::class, 'addFormLink'])->name('forms.links.add');
    Route::get('/forms/links/{id}/toggle', [\App\Http\Controllers\AdministrationController::class, 'toggleFormLink'])->name('forms.links.toggle'); // This should be POST but I'll keep it as POST in the controller, wait
    Route::post('/forms/links/{id}/toggle', [\App\Http\Controllers\AdministrationController::class, 'toggleFormLink'])->name('forms.links.toggle');
    Route::delete('/forms/links/{id}', [\App\Http\Controllers\AdministrationController::class, 'deleteFormLink'])->name('forms.links.delete');
    Route::get('/forms/links/{id}/qrcode', [\App\Http\Controllers\AdministrationController::class, 'generateQrCode'])->name('forms.links.qrcode');
    
    // Institution Management
    Route::resource('institutions', \App\Http\Controllers\InstitutionController::class);
    
    // Admissions Management
    Route::get('/admissions', [\App\Http\Controllers\AdmissionsController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{id}', [\App\Http\Controllers\AdmissionsController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{id}/payment', [\App\Http\Controllers\AdmissionsController::class, 'storePayment'])->name('admissions.payment.store');
    Route::get('/admissions/{id}/acknowledgment', [\App\Http\Controllers\AdmissionsController::class, 'downloadAcknowledgment'])->name('admissions.acknowledgment.download');
    Route::post('/admissions/{id}/payment/callback', [\App\Http\Controllers\AdmissionsController::class, 'handlePaymentCallback'])->name('admissions.payment.callback');
    Route::post('/admissions/{id}/payment/failure', [\App\Http\Controllers\AdmissionsController::class, 'paymentFailure'])->name('admissions.payment.failure');
    Route::post('/admissions/{id}/payment/{paymentId}/settle', [\App\Http\Controllers\AdmissionsController::class, 'settlePayment'])->name('admissions.payment.settle');
    Route::get('/enrolled-students', [\App\Http\Controllers\AdmissionsController::class, 'enrolled'])->name('admissions.enrolled');
    Route::get('/enrolled-students/export', [\App\Http\Controllers\AdmissionsController::class, 'exportEnrolled'])->name('admissions.enrolled.export');

    // Admission Configuration
    Route::resource('admission-configs', \App\Http\Controllers\Administration\AdmissionConfigController::class);

    // Exam Management Routes (Moved for Organization Protection)
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    Route::get('exams/{exam}/results', [\App\Http\Controllers\Admin\ExamController::class, 'results'])->name('exams.results');
    Route::get('exams/attempt/{attempt}', [\App\Http\Controllers\Admin\ExamController::class, 'showAttempt'])->name('exams.attempts.show');
    Route::post('exams/{exam}/questions', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('exams.questions.store');
    Route::post('exams/{exam}/link-questions', [\App\Http\Controllers\Admin\ExamController::class, 'linkQuestions'])->name('exams.questions.link');
    Route::delete('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');

    // Question Bank Routes (Moved for Organization Protection)
    Route::resource('question-banks', \App\Http\Controllers\Admin\QuestionBankController::class);
    Route::get('question-banks/{question_bank}/questions-json', [\App\Http\Controllers\Admin\QuestionBankController::class, 'getQuestions'])->name('question-banks.questions.json');
    Route::post('question-banks/{question_bank}/questions', [\App\Http\Controllers\Admin\QuestionBankController::class, 'storeQuestion'])->name('question-banks.questions.store');
    Route::post('question-banks/{question_bank}/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'importQuestions'])->name('question-banks.questions.import');
});

require __DIR__.'/auth.php';
require __DIR__.'/school.php';
