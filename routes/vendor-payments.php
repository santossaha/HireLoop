<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorPaymentController;

/*
|--------------------------------------------------------------------------
| Vendor Payment Management Routes
|--------------------------------------------------------------------------
|
| These routes handle all vendor payment management functionality, including
| creating, displaying, approving, and marking payments as paid.
|
| Available Permissions:

view-vendor-payments
create-vendor-payments
edit-vendor-payments
delete-vendor-payments
approve-vendor-payments
reject-vendor-payments
mark-vendor-payments-paid
generate-vendor-payments
view-vendor-payment-approval
view-vendor-payment-processing
view-vendor-payment-reports
export-vendor-payments
view-vendor-payment-api
|
*/

// Basic CRUD routes for vendor payments
Route::middleware(['auth', 'permission:view-vendor-payments'])->group(function () {
    Route::get('/vendor-payments', [VendorPaymentController::class, 'index'])->name('vendor-payments.index');
    Route::get('/vendor-payments/{vendorPayment}', [VendorPaymentController::class, 'show'])->name('vendor-payments.show');
});

Route::middleware(['auth', 'permission:create-vendor-payments'])->group(function () {
    Route::get('/vendor-payments/create', [VendorPaymentController::class, 'create'])->name('vendor-payments.create');
    Route::post('/vendor-payments', [VendorPaymentController::class, 'store'])->name('vendor-payments.store');
});

Route::middleware(['auth', 'permission:edit-vendor-payments'])->group(function () {
    Route::get('/vendor-payments/{vendorPayment}/edit', [VendorPaymentController::class, 'edit'])->name('vendor-payments.edit');
    Route::put('/vendor-payments/{vendorPayment}', [VendorPaymentController::class, 'update'])->name('vendor-payments.update');
});

// Specialized payment management routes
Route::middleware(['auth', 'permission:approve-vendor-payments'])->group(function () {
    Route::patch('/vendor-payments/{vendorPayment}/approve', [VendorPaymentController::class, 'approve'])->name('vendor-payments.approve');
});

Route::middleware(['auth', 'permission:reject-vendor-payments'])->group(function () {
    Route::patch('/vendor-payments/{vendorPayment}/reject', [VendorPaymentController::class, 'reject'])->name('vendor-payments.reject');
});

Route::middleware(['auth', 'permission:mark-vendor-payments-paid'])->group(function () {
    Route::patch('/vendor-payments/{vendorPayment}/mark-as-paid', [VendorPaymentController::class, 'markAsPaid'])->name('vendor-payments.mark-as-paid');
});

// Payment generation from attendance
Route::middleware(['auth', 'permission:generate-vendor-payments'])->group(function () {
    Route::post('/vendor-payments/generate-from-attendance', [VendorPaymentController::class, 'generateFromAttendance'])->name('vendor-payments.generate-from-attendance');
});

// Dashboard and reporting routes
Route::middleware(['auth', 'permission:view-vendor-payment-approval'])->group(function () {
    Route::get('/vendor-payment-approval-dashboard', [VendorPaymentController::class, 'approvalDashboard'])->name('vendor-payments.approval-dashboard');
});

Route::middleware(['auth', 'permission:view-vendor-payment-processing'])->group(function () {
    Route::get('/vendor-payment-processing-dashboard', [VendorPaymentController::class, 'processingDashboard'])->name('vendor-payments.processing-dashboard');
});

Route::middleware(['auth', 'permission:view-vendor-payment-reports'])->group(function () {
    Route::get('/vendor-payment-monthly-report', [VendorPaymentController::class, 'monthlyReport'])->name('vendor-payments.monthly-report');
});

Route::middleware(['auth', 'permission:export-vendor-payments'])->group(function () {
    Route::get('/vendor-payment-export', [VendorPaymentController::class, 'export'])->name('vendor-payments.export');
});

// API endpoints for AJAX
Route::middleware(['auth', 'permission:view-vendor-payment-api'])->group(function () {
    Route::get('/api/vendor-payments/by-vendor/{vendor}', [VendorPaymentController::class, 'getByVendor'])->name('api.vendor-payments.by-vendor');
    Route::get('/api/vendor-payments/client-payments/{clientPayment}', [VendorPaymentController::class, 'getClientPaymentDetails'])->name('api.vendor-payments.client-payment');
    Route::get('/api/vendor-payments/attendance/{vendor}', [VendorPaymentController::class, 'getVendorAttendance'])->name('api.vendor-payments.attendance');
});