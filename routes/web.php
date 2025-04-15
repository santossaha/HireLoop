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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Vendor routes
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');

    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
    Route::get('/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
    Route::patch('/vendors/{vendor}/status', [VendorController::class, 'updateStatus'])->name('vendors.update-status');
    Route::get('/vendor-approvals', [VendorController::class, 'pendingApprovals'])->name('vendors.pending-approvals');
    
    // Client Payment routes
    Route::get('/client-payments', [ClientPaymentController::class, 'index'])->name('client-payments.index');
    Route::get('/client-payments/create', [ClientPaymentController::class, 'create'])->name('client-payments.create');
    Route::post('/client-payments', [ClientPaymentController::class, 'store'])->name('client-payments.store');
    Route::get('/client-payments/{clientPayment}', [ClientPaymentController::class, 'show'])->name('client-payments.show');
    Route::get('/client-payments/{clientPayment}/edit', [ClientPaymentController::class, 'edit'])->name('client-payments.edit');
    Route::put('/client-payments/{clientPayment}', [ClientPaymentController::class, 'update'])->name('client-payments.update');
    Route::delete('/client-payments/{clientPayment}', [ClientPaymentController::class, 'destroy'])->name('client-payments.destroy');
    Route::patch('/client-payments/{clientPayment}/mark-as-received', [ClientPaymentController::class, 'markAsReceived'])->name('client-payments.mark-as-received');
    Route::get('/client-payment-dashboard', [ClientPaymentController::class, 'dashboard'])->name('client-payments.dashboard');
    
    // Vendor Attendance routes
    Route::get('/vendor-attendances', [VendorAttendanceController::class, 'index'])->name('vendor-attendances.index');
    Route::get('/vendor-attendances/create', [VendorAttendanceController::class, 'create'])->name('vendor-attendances.create');
    Route::post('/vendor-attendances', [VendorAttendanceController::class, 'store'])->name('vendor-attendances.store');
    Route::get('/vendor-attendances/{vendorAttendance}', [VendorAttendanceController::class, 'show'])->name('vendor-attendances.show');
    Route::get('/vendor-attendances/{vendorAttendance}/edit', [VendorAttendanceController::class, 'edit'])->name('vendor-attendances.edit');
    Route::put('/vendor-attendances/{vendorAttendance}', [VendorAttendanceController::class, 'update'])->name('vendor-attendances.update');
    Route::patch('/vendor-attendances/{vendorAttendance}/approve', [VendorAttendanceController::class, 'approve'])->name('vendor-attendances.approve');
    Route::patch('/vendor-attendances/{vendorAttendance}/reject', [VendorAttendanceController::class, 'reject'])->name('vendor-attendances.reject');
    Route::post('/vendor-attendances/send-reminders', [VendorAttendanceController::class, 'sendReminders'])->name('vendor-attendances.send-reminders');
    Route::get('/vendor-attendance-summary', [VendorAttendanceController::class, 'summary'])->name('vendor-attendances.summary');
    
    // Invoice routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::patch('/invoices/{invoice}/verify', [InvoiceController::class, 'verify'])->name('invoices.verify');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/invoice-pending-verification', [InvoiceController::class, 'pendingVerification'])->name('invoices.pending-verification');
    Route::get('/invoice-discrepancies', [InvoiceController::class, 'discrepancies'])->name('invoices.discrepancies');
    Route::get('/invoice-summary', [InvoiceController::class, 'summary'])->name('invoices.summary');
    
    // Requirements routes
    Route::get('/requirements', [RequirementController::class, 'index'])->name('requirements.index');
    Route::get('/requirements/create', [RequirementController::class, 'create'])->name('requirements.create');
    Route::post('/requirements', [RequirementController::class, 'store'])->name('requirements.store');
    Route::get('/requirements/{requirement}', [RequirementController::class, 'show'])->name('requirements.show');
    Route::get('/requirements/{requirement}/edit', [RequirementController::class, 'edit'])->name('requirements.edit');
    Route::put('/requirements/{requirement}', [RequirementController::class, 'update'])->name('requirements.update');
    Route::delete('/requirements/{requirement}', [RequirementController::class, 'destroy'])->name('requirements.destroy');
    Route::post('/requirements/{requirement}/hod-approve', [RequirementController::class, 'hodApprove'])->name('requirements.hod-approve');
    Route::post('/requirements/{requirement}/founder-approve', [RequirementController::class, 'founderApprove'])->name('requirements.founder-approve');
    
    // Interview routes
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');
    Route::get('/interviews/{interview}/edit', [InterviewController::class, 'edit'])->name('interviews.edit');
    Route::put('/interviews/{interview}', [InterviewController::class, 'update'])->name('interviews.update');
    Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy');
    Route::post('/interviews/{interview}/feedback', [InterviewController::class, 'submitFeedback'])->name('interviews.feedback');
    
    // Payment Management routes
    // Include payment management specific routes from separate file
    require __DIR__.'/vendor-payments.php';
});