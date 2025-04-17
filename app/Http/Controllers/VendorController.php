<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the vendors.
     */
    public function index()
    {
        $user = Auth::user();
        return view('vendors.index');
    }

    /**
     * Get vendors data for DataTables
     */
    public function getVendorsData(Request $request)
    {
        $user = Auth::user();
        $query = Vendor::query();

        // Different users see different sets of vendors
        if ($user->isAdmin() || $user->isFounder()) {
            // Admin and founder see all vendors
            $query->with('user');
        } elseif ($user->isHod()) {
            // HOD sees vendors in their department
            $department = $user->department;
            $query->whereHas('requirements', function ($q) use ($department) {
                $q->where('department_id', $department->id);
            })->with('user');
        } elseif ($user->isPoc()) {
            // POC sees vendors they're responsible for
            $query->where('internal_poc_id', $user->id)->with('user');
        } else {
            // Other users only see their own vendor profile if they have one
            $query->where('user_id', $user->id)->with('user');
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('internalPoc', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get total records count
        $totalRecords = $query->count();

        // Apply pagination
        $vendors = $query->skip($request->start)
                        ->take($request->length)
                        ->get();

        $data = [];
        foreach ($vendors as $vendor) {
            $data[] = [
                'id' => $vendor->id,
                'company_name' => $vendor->company_name,
                'vendor_type' => ucfirst($vendor->vendor_type),
                'contact_person' => $vendor->contact_person,
                'contact_info' => [
                    'email' => $vendor->email,
                    'phone' => $vendor->phone
                ],
                'internal_poc' => $vendor->internalPoc ? $vendor->internalPoc->name : 'N/A',
                'status' => $vendor->status,
                'client_ready' => $vendor->client_ready,
                'actions' => view('vendors.partials.actions', compact('vendor'))->render()
            ];
        }

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $this->authorize('create', Vendor::class);
        
        $internalPocs = User::whereIn('role', ['admin', 'poc', 'hod'])->get();
        
        return view('vendors.create', compact('internalPocs'));
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_type' => 'required|in:company,freelancer',
            //'company_name' => 'required|string|max:255',
            'poc_name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors',
            'contact_number' => 'required|string|max:20',
            'skype_id' => 'nullable|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
            'budget_3_years' => 'required|numeric|min:0',
            'budget_5_years' => 'required|numeric|min:0',
            'budget_7_years' => 'required|numeric|min:0',
            'budget_10_years' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'key_skills' => 'array',
            'key_skills.*' => 'exists:key_skills,id'
        ]);

        $vendor = Vendor::create($validated);
        
        if (isset($validated['key_skills'])) {
            $vendor->keySkills()->sync($validated['key_skills']);
        }

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        $this->authorize('view', $vendor);
        
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $this->authorize('update', $vendor);
        
        $internalPocs = User::whereIn('role', ['admin', 'poc', 'hod'])->get();
        
        return view('vendors.edit', compact('vendor', 'internalPocs'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'vendor_type' => 'required|in:company,freelancer',
            //'company_name' => 'required|string|max:255',
            'poc_name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
            'contact_number' => 'required|string|max:20',
            'skype_id' => 'nullable|string|max:255',
            'slack_id' => 'nullable|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
            'budget_3_years' => 'required|numeric|min:0',
            'budget_5_years' => 'required|numeric|min:0',
            'budget_7_years' => 'required|numeric|min:0',
            'budget_10_years' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'key_skills' => 'array',
            'key_skills.*' => 'exists:key_skills,id'
        ]);

        $vendor->update($validated);
        
        if (isset($validated['key_skills'])) {
            $vendor->keySkills()->sync($validated['key_skills']);
        }

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Update the status of the vendor.
     */
    public function updateStatus(Request $request, Vendor $vendor)
    {
        $this->authorize('updateStatus', $vendor);
        
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'comment' => 'nullable|string',
        ]);

        $vendor->status = $request->status;
        $vendor->save();

        // Add comment if provided (will implement later)

        return redirect()->back()
            ->with('success', 'Vendor status updated successfully.');
    }

    /**
     * Display pending vendors that need approval.
     */
    public function pendingApprovals()
    {
        $user = Auth::user();
        if ($user->isAdmin() || $user->isFounder()) {
            // Admin and founder see all pending vendors
            $pendingVendors = Vendor::where('status', 'pending')->with('user')->paginate(10);
        } elseif ($user->isHod()) {
            // HOD sees pending vendors in their department
            $department = $user->department;
            $pendingVendors = Vendor::where('status', 'pending')
                ->whereHas('requirements', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })
                ->with('user')
                ->paginate(10);
        } else {
            // Other users don't see pending approvals
            abort(403, 'Unauthorized action.');
        }
        
        return view('vendors.pending-approvals', compact('pendingVendors'));
    }

     /**
     * Remove the specified client payment from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Approve the vendor's client readiness status.
     */
    public function approve(Request $request, Vendor $vendor)
    {
        $this->authorize('update', $vendor);
        
        $request->validate([
            'client_ready' => 'required|boolean',
            'communication_rating' => 'required|in:excellent,good,average,bad',
            'technical_rating' => 'required|in:excellent,good,average,bad',
            'notes' => 'nullable|string',
        ]);

        $vendor->update([
            'client_ready' => $request->client_ready,
            'communication_rating' => $request->communication_rating,
            'technical_rating' => $request->technical_rating,
        ]);

        // Add notes if provided
        if ($request->filled('notes')) {
            // You can implement a notes/comment system here
            // For now, we'll just update the vendor
        }

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Vendor status updated successfully.');
    }
}