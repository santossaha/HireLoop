<?php

namespace App\Http\Controllers;

use App\Models\ClientPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientPaymentController extends Controller
{
    /**
     * Display a listing of the client payments.
     */
    public function index()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view client payments.');
        }

        $clientPayments = ClientPayment::with('confirmedBy')->orderBy('payment_date', 'desc')->get();
        
        return view('client-payments.index', compact('clientPayments'));
    }

    /**
     * Show the form for creating a new client payment.
     */
    public function create()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('client-payments.index')->with('error', 'You do not have permission to create client payments.');
        }
        
        return view('client-payments.create');
    }

    /**
     * Store a newly created client payment in storage.
     */
    public function store(Request $request)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('client-payments.index')->with('error', 'You do not have permission to create client payments.');
        }
        
        // Validate request data
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_status' => 'required|in:received,pending,delayed',
            'invoice_number' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Add confirmed_by and confirmed_at if status is received
        if ($validated['payment_status'] === 'received') {
            $validated['confirmed_by'] = Auth::id();
            $validated['confirmed_at'] = now();
        }
        
        ClientPayment::create($validated);
        
        return redirect()->route('client-payments.index')->with('success', 'Client payment added successfully.');
    }

    /**
     * Display the specified client payment.
     */
    public function show(ClientPayment $clientPayment)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view client payments.');
        }
        
        $clientPayment->load('confirmedBy', 'vendorPayments.vendor');
        
        return view('client-payments.show', compact('clientPayment'));
    }

    /**
     * Show the form for editing the specified client payment.
     */
    public function edit(ClientPayment $clientPayment)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('client-payments.index')->with('error', 'You do not have permission to edit client payments.');
        }
        
        return view('client-payments.edit', compact('clientPayment'));
    }

    /**
     * Update the specified client payment in storage.
     */
    public function update(Request $request, ClientPayment $clientPayment)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('client-payments.index')->with('error', 'You do not have permission to update client payments.');
        }
        
        // Validate request data
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_status' => 'required|in:received,pending,delayed',
            'invoice_number' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Handle payment status change to received
        if ($validated['payment_status'] === 'received' && $clientPayment->payment_status !== 'received') {
            $validated['confirmed_by'] = Auth::id();
            $validated['confirmed_at'] = now();
        }
        
        // Handle payment status change from received to something else
        if ($validated['payment_status'] !== 'received' && $clientPayment->payment_status === 'received') {
            $validated['confirmed_by'] = null;
            $validated['confirmed_at'] = null;
        }
        
        $clientPayment->update($validated);
        
        return redirect()->route('client-payments.index')->with('success', 'Client payment updated successfully.');
    }

    /**
     * Remove the specified client payment from storage.
     */
    public function destroy(ClientPayment $clientPayment)
    {
        // Access control check - only admin can delete client payments
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('client-payments.index')->with('error', 'You do not have permission to delete client payments.');
        }
        
        // Check if the client payment is associated with any vendor payments
        if ($clientPayment->vendorPayments()->count() > 0) {
            return redirect()->route('client-payments.index')->with('error', 'Cannot delete client payment that is associated with vendor payments.');
        }
        
        $clientPayment->delete();
        
        return redirect()->route('client-payments.index')->with('success', 'Client payment deleted successfully.');
    }

    /**
     * Update the status of a client payment to received.
     */
    public function markAsReceived(ClientPayment $clientPayment)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('client-payments.index')->with('error', 'You do not have permission to update client payments.');
        }
        
        $clientPayment->update([
            'payment_status' => 'received',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);
        
        return redirect()->route('client-payments.index')->with('success', 'Client payment marked as received successfully.');
    }

    /**
     * Display a dashboard of client payment status.
     */
    public function dashboard()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view client payment dashboard.');
        }
        
        $receivedPayments = ClientPayment::where('payment_status', 'received')->orderBy('payment_date', 'desc')->get();
        $pendingPayments = ClientPayment::where('payment_status', 'pending')->orderBy('payment_date', 'desc')->get();
        $delayedPayments = ClientPayment::where('payment_status', 'delayed')->orderBy('payment_date', 'desc')->get();
        
        $totalReceived = $receivedPayments->sum('payment_amount');
        $totalPending = $pendingPayments->sum('payment_amount');
        $totalDelayed = $delayedPayments->sum('payment_amount');
        
        return view('client-payments.dashboard', compact(
            'receivedPayments', 
            'pendingPayments', 
            'delayedPayments', 
            'totalReceived', 
            'totalPending', 
            'totalDelayed'
        ));
    }
}