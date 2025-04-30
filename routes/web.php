<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\VendorAttendanceController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorPaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CandidateSourcingController;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('permission:view-dashboard');
    
    // Vendor routes
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index')
        ->middleware('permission:view-vendors');
    Route::get('/vendors/data', [VendorController::class, 'getVendorsData'])->name('vendors.data')
        ->middleware('permission:view-vendors');
    Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendors.create')
        ->middleware('permission:create-vendor');
    Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store')
        ->middleware('permission:create-vendor');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy')
        ->middleware('permission:delete-vendor');

    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show')
        ->middleware('permission:view-vendor-details');
    Route::get('/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit')
        ->middleware('permission:edit-vendor');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update')
        ->middleware('permission:edit-vendor');
    Route::patch('/vendors/{vendor}/status', [VendorController::class, 'updateStatus'])->name('vendors.update-status')
        ->middleware('permission:update-vendor-status');
    Route::get('/vendor-approvals', [VendorController::class, 'pendingApprovals'])->name('vendors.pending-approvals')
        ->middleware('permission:view-vendor-approvals');
    Route::patch('/vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve')
        ->middleware('permission:approve-vendor');
    
    // Client Payment routes
    Route::get('/client-payments', [ClientPaymentController::class, 'index'])->name('client-payments.index')
        ->middleware('permission:view-client-payments');
    Route::get('/client-payments/create', [ClientPaymentController::class, 'create'])->name('client-payments.create')
        ->middleware('permission:create-client-payment');
    Route::post('/client-payments', [ClientPaymentController::class, 'store'])->name('client-payments.store')
        ->middleware('permission:create-client-payment');
    Route::get('/client-payments/{clientPayment}', [ClientPaymentController::class, 'show'])->name('client-payments.show')
        ->middleware('permission:view-client-payment-details');
    Route::get('/client-payments/{clientPayment}/edit', [ClientPaymentController::class, 'edit'])->name('client-payments.edit')
        ->middleware('permission:edit-client-payment');
    Route::put('/client-payments/{clientPayment}', [ClientPaymentController::class, 'update'])->name('client-payments.update')
        ->middleware('permission:edit-client-payment');
    Route::delete('/client-payments/{clientPayment}', [ClientPaymentController::class, 'destroy'])->name('client-payments.destroy')
        ->middleware('permission:delete-client-payment');
    Route::patch('/client-payments/{clientPayment}/mark-as-received', [ClientPaymentController::class, 'markAsReceived'])->name('client-payments.mark-as-received')
        ->middleware('permission:mark-client-payment-received');
    Route::get('/client-payment-dashboard', [ClientPaymentController::class, 'dashboard'])->name('client-payments.dashboard')
        ->middleware('permission:view-client-payment-dashboard');
    
    // Vendor Attendance routes
    Route::get('/vendor-attendances', [VendorAttendanceController::class, 'index'])->name('vendor-attendances.index')
        ->middleware('permission:view-vendor-attendances');
    Route::get('/vendor-attendances/create', [VendorAttendanceController::class, 'create'])->name('vendor-attendances.create')
        ->middleware('permission:create-vendor-attendance');
    Route::post('/vendor-attendances', [VendorAttendanceController::class, 'store'])->name('vendor-attendances.store')
        ->middleware('permission:create-vendor-attendance');
    Route::get('/vendor-attendances/{vendorAttendance}', [VendorAttendanceController::class, 'show'])->name('vendor-attendances.show')
        ->middleware('permission:view-vendor-attendance-details');
    Route::get('/vendor-attendances/{vendorAttendance}/edit', [VendorAttendanceController::class, 'edit'])->name('vendor-attendances.edit')
        ->middleware('permission:edit-vendor-attendance');
    Route::put('/vendor-attendances/{vendorAttendance}', [VendorAttendanceController::class, 'update'])->name('vendor-attendances.update')
        ->middleware('permission:edit-vendor-attendance');
    Route::patch('/vendor-attendances/{vendorAttendance}/approve', [VendorAttendanceController::class, 'approve'])->name('vendor-attendances.approve')
        ->middleware('permission:approve-vendor-attendance');
    Route::patch('/vendor-attendances/{vendorAttendance}/reject', [VendorAttendanceController::class, 'reject'])->name('vendor-attendances.reject')
        ->middleware('permission:reject-vendor-attendance');
    Route::post('/vendor-attendances/send-reminders', [VendorAttendanceController::class, 'sendReminders'])->name('vendor-attendances.send-reminders')
        ->middleware('permission:send-vendor-attendance-reminders');
    Route::get('/vendor-attendance-summary', [VendorAttendanceController::class, 'summary'])->name('vendor-attendances.summary')
        ->middleware('permission:view-vendor-attendance-summary');
    
    // Invoice routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index')
        ->middleware('permission:view-invoices');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create')
        ->middleware('permission:create-invoice');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store')
        ->middleware('permission:create-invoice');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show')
        ->middleware('permission:view-invoice-details');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit')
        ->middleware('permission:edit-invoice');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update')
        ->middleware('permission:edit-invoice');
    Route::patch('/invoices/{invoice}/verify', [InvoiceController::class, 'verify'])->name('invoices.verify')
        ->middleware('permission:verify-invoice');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download')
        ->middleware('permission:download-invoice');
    Route::get('/invoice-pending-verification', [InvoiceController::class, 'pendingVerification'])->name('invoices.pending-verification')
        ->middleware('permission:view-pending-invoices');
    Route::get('/invoice-discrepancies', [InvoiceController::class, 'discrepancies'])->name('invoices.discrepancies')
        ->middleware('permission:view-invoice-discrepancies');
    Route::get('/invoice-summary', [InvoiceController::class, 'summary'])->name('invoices.summary')
        ->middleware('permission:view-invoice-summary');
    
    // Requirements routes
    Route::get('/requirements', [RequirementController::class, 'index'])->name('requirements.index')
        ->middleware('permission:view-requirements');
        
    Route::get('/requirements/create', [RequirementController::class, 'create'])->name('requirements.create')
        ->middleware('permission:create-requirement');

    Route::post('/requirements', [RequirementController::class, 'store'])->name('requirements.store')
        ->middleware('permission:create-requirement');

    Route::get('/requirements/{requirement}', [RequirementController::class, 'show'])->name('requirements.show')
        ->middleware('permission:view-requirement-details');

    Route::get('/requirements/{requirement}/edit', [RequirementController::class, 'edit'])->name('requirements.edit')
        ->middleware('permission:edit-requirement');

    Route::put('/requirements/{requirement}', [RequirementController::class, 'update'])->name('requirements.update')
        ->middleware('permission:edit-requirement');

    Route::delete('/requirements/{requirement}', [RequirementController::class, 'destroy'])->name('requirements.destroy')
        ->middleware('permission:delete-requirement');

    Route::post('/requirements/{requirement}/hod-approve', [RequirementController::class, 'hodApprove'])->name('requirements.hod-approve')
        ->middleware('permission:approve-requirement');

    Route::post('/requirements/{requirement}/founder-approve', [RequirementController::class, 'founderApprove'])->name('requirements.founder-approve')
        ->middleware('permission:approve-requirement');

    Route::get('/requirements/data', [RequirementController::class, 'index'])->name('requirements.data')
        ->middleware('permission:view-requirements');
        
    Route::get('/requirements/pending-counts', [RequirementController::class, 'getPendingCounts'])->name('requirements.pending-counts')
        ->middleware('permission:view-requirement-counts');
    
    Route::get('/requirements/next-id', [RequirementController::class, 'getNextRequirementId'])->name('requirements.next-id');
    
    // Interview routes
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index')
        ->middleware('permission:view-interviews');
    Route::get('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create')
        ->middleware('permission:create-interview');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store')
        ->middleware('permission:create-interview');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show')
        ->middleware('permission:view-interview-details');
    Route::get('/interviews/{interview}/edit', [InterviewController::class, 'edit'])->name('interviews.edit')
        ->middleware('permission:edit-interview');
    Route::put('/interviews/{interview}', [InterviewController::class, 'update'])->name('interviews.update')
        ->middleware('permission:edit-interview');
    Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy')
        ->middleware('permission:delete-interview');
    Route::post('/interviews/{interview}/feedback', [InterviewController::class, 'submitFeedback'])->name('interviews.feedback')
        ->middleware('permission:submit-interview-feedback');
    Route::get('/interviews/stats', [InterviewController::class, 'getStats'])->name('interviews.stats')
        ->middleware('permission:view-interview-stats');
    
    // Payment Management routes
    // Include payment management specific routes from separate file
    require __DIR__.'/vendor-payments.php';

    // User Management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index')
        ->middleware('permission:view-users');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')
        ->middleware('permission:create-user');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')
        ->middleware('permission:create-user');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')
        ->middleware('permission:edit-user');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')
        ->middleware('permission:edit-user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')
        ->middleware('permission:delete-user');

    // Role Management routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')
        ->middleware('permission:view-roles');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')
        ->middleware('permission:create-role');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')
        ->middleware('permission:create-role');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')
        ->middleware('permission:edit-role');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')
        ->middleware('permission:edit-role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')
        ->middleware('permission:delete-role');

    // Candidate Sourcing Routes
    Route::get('/candidate-sourcing', [CandidateSourcingController::class, 'index'])->name('candidate-sourcing.index');
    Route::get('/candidate-sourcing/{requirement}/create', [CandidateSourcingController::class, 'create'])->name('candidate-sourcing.create');
    Route::post('/candidate-sourcing/{requirement}', [CandidateSourcingController::class, 'store'])->name('candidate-sourcing.store');
    Route::get('/candidate-sourcing/{candidateSourcing}', [CandidateSourcingController::class, 'show'])->name('candidate-sourcing.show');
    Route::post('/candidate-sourcing/{candidateSourcing}/approve', [CandidateSourcingController::class, 'approve'])->name('candidate-sourcing.approve');
    Route::post('/candidate-sourcing/{candidateSourcing}/reject', [CandidateSourcingController::class, 'reject'])->name('candidate-sourcing.reject');
    Route::post('/candidate-sourcing/{candidateSourcing}/schedule-interview', [CandidateSourcingController::class, 'scheduleInterview'])->name('candidate-sourcing.schedule-interview');
    Route::post('/candidate-sourcing/{requirement}/upload-candidate', [CandidateSourcingController::class, 'uploadCandidate'])->name('candidate-sourcing.upload-candidate');

    // Company routes
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index')
        ->middleware('permission:company-list');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create')
        ->middleware('permission:company-create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store')
        ->middleware('permission:company-create');
    Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show')
        ->middleware('permission:company-list');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit')
        ->middleware('permission:company-edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update')
        ->middleware('permission:company-edit');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy')
        ->middleware('permission:company-delete');
});

/*
|--------------------------------------------------------------------------
| Required Permissions List
|--------------------------------------------------------------------------
|
| Below is a list of all permissions required by the application.
| These should be created in the database and assigned to appropriate roles.
|
| Dashboard:
| - view-dashboard
|
| Vendor Management:
| - view-vendors
| - create-vendor
| - edit-vendor
| - delete-vendor
| - view-vendor-details
| - update-vendor-status
| - view-vendor-approvals
| - approve-vendor
|
| Client Payment Management:
| - view-client-payments
| - create-client-payment
| - edit-client-payment
| - delete-client-payment
| - view-client-payment-details
| - mark-client-payment-received
| - view-client-payment-dashboard
|
| Vendor Attendance Management:
| - view-vendor-attendances
| - create-vendor-attendance
| - edit-vendor-attendance
| - view-vendor-attendance-details
| - approve-vendor-attendance
| - reject-vendor-attendance
| - send-vendor-attendance-reminders
| - view-vendor-attendance-summary
|
| Invoice Management:
| - view-invoices
| - create-invoice
| - edit-invoice
| - view-invoice-details
| - verify-invoice
| - download-invoice
| - view-pending-invoices
| - view-invoice-discrepancies
| - view-invoice-summary
|
| Requirement Management:
| - view-requirements
| - create-requirement
| - edit-requirement
| - delete-requirement
| - view-requirement-details
| - approve-requirement
| - view-requirement-counts
|
| Interview Management:
| - view-interviews
| - create-interview
| - edit-interview
| - delete-interview
| - view-interview-details
| - submit-interview-feedback
| - view-interview-stats
|
| User Management:
| - view-users
| - create-user
| - edit-user
| - delete-user
|
| Role Management:
| - view-roles
| - create-role
| - edit-role
| - delete-role
|
| Company Management:
| - company-list
| - company-create
| - company-edit
| - company-delete
|
*/