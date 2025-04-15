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
*/

// Basic CRUD routes for vendor payments
Route::get('/vendor-payments', [VendorPaymentController::class, 'index'])->name('vendor-payments.index');
Route::get('/vendor-payments/create', [VendorPaymentController::class, 'create'])->name('vendor-payments.create');
Route::post('/vendor-payments', [VendorPaymentController::class, 'store'])->name('vendor-payments.store');
Route::get('/vendor-payments/{vendorPayment}', [VendorPaymentController::class, 'show'])->name('vendor-payments.show');
Route::get('/vendor-payments/{vendorPayment}/edit', [VendorPaymentController::class, 'edit'])->name('vendor-payments.edit');
Route::put('/vendor-payments/{vendorPayment}', [VendorPaymentController::class, 'update'])->name('vendor-payments.update');

// Specialized payment management routes
Route::patch('/vendor-payments/{vendorPayment}/approve', [VendorPaymentController::class, 'approve'])->name('vendor-payments.approve');
Route::patch('/vendor-payments/{vendorPayment}/reject', [VendorPaymentController::class, 'reject'])->name('vendor-payments.reject');
Route::patch('/vendor-payments/{vendorPayment}/mark-as-paid', [VendorPaymentController::class, 'markAsPaid'])->name('vendor-payments.mark-as-paid');

// Payment generation from attendance
Route::post('/vendor-payments/generate-from-attendance', [VendorPaymentController::class, 'generateFromAttendance'])->name('vendor-payments.generate-from-attendance');

// Dashboard and reporting routes
Route::get('/vendor-payment-approval-dashboard', [VendorPaymentController::class, 'approvalDashboard'])->name('vendor-payments.approval-dashboard');
Route::get('/vendor-payment-processing-dashboard', [VendorPaymentController::class, 'processingDashboard'])->name('vendor-payments.processing-dashboard');
Route::get('/vendor-payment-monthly-report', [VendorPaymentController::class, 'monthlyReport'])->name('vendor-payments.monthly-report');
Route::get('/vendor-payment-export', [VendorPaymentController::class, 'export'])->name('vendor-payments.export');

// API endpoints for AJAX
Route::get('/api/vendor-payments/by-vendor/{vendor}', [VendorPaymentController::class, 'getByVendor'])->name('api.vendor-payments.by-vendor');
Route::get('/api/vendor-payments/client-payments/{clientPayment}', [VendorPaymentController::class, 'getClientPaymentDetails'])->name('api.vendor-payments.client-payment');
Route::get('/api/vendor-payments/attendance/{vendor}', [VendorPaymentController::class, 'getVendorAttendance'])->name('api.vendor-payments.attendance');