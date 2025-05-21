<?php

namespace App\Http\Controllers;

use App\Mail\VendorInviteMail;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\VendorInviteTemporaryToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        
        // Different users see different sets of vendors
        if ($user->isAdmin() || $user->isFounder()) {
            // Admin and founder see all vendors
            $vendors = Vendor::with('user')->paginate(10);
        } elseif ($user->isHod()) {
            // HOD sees vendors in their department
            $department = $user->department;
            $vendors = Vendor::whereHas('requirements', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })->with('user')->paginate(10);
        } elseif ($user->isPoc()) {
            // POC sees vendors they're responsible for
            $vendors = Vendor::where('internal_poc_id', $user->id)
                ->with('user')
                ->paginate(10);
        } else {
            // Other users only see their own vendor profile if they have one
            $vendors = Vendor::where('user_id', $user->id)->with('user')->paginate(10);
        }
        
        return view('vendors.index', compact('vendors'));
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
        $this->authorize('create', Vendor::class);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8|confirmed',
            'type' => 'required|in:company,freelancer',
            'contact_number' => 'required|string|max:20',
            'skype' => 'nullable|string|max:255',
            'slack' => 'nullable|string|max:255',
            'poc_name' => 'required|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
            'budget_3_years' => 'required|numeric|min:0',
            'budget_5_years' => 'required|numeric|min:0',
            'budget_7_years' => 'required|numeric|min:0',
            'budget_10_years' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendors.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Create or find user
        if ($request->filled('existing_user_id')) {
            $user = User::findOrFail($request->existing_user_id);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? Str::random(12)),
                'role' => 'vendor',
            ]);
        }

        // Create vendor profile
        $vendor = Vendor::create([
            'vendor_type' => $request->type,
            'company_name' => $request->name, // Using name as company_name
            'contact_person' => $request->poc_name,
            'email' => $request->email,
            'phone' => $request->contact_number,
            'skype_id' => $request->skype,
            'slack_id' => $request->slack,
            'internal_poc_id' => $request->internal_poc_id,
            'budget_3_years' => $request->budget_3_years,
            'budget_5_years' => $request->budget_5_years,
            'budget_7_years' => $request->budget_7_years,
            'budget_10_years' => $request->budget_10_years,
            'status' => $request->status,
            'client_ready' => false, // Default value
        ]);

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
        $this->authorize('update', $vendor);
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:company,freelancer',
            'contact_number' => 'required|string|max:20',
            'skype' => 'nullable|string|max:255',
            'slack' => 'nullable|string|max:255',
            'poc_name' => 'required|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
            'budget_3_years' => 'required|numeric|min:0',
            'budget_5_years' => 'required|numeric|min:0',
            'budget_7_years' => 'required|numeric|min:0',
            'budget_10_years' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendors.edit', $vendor)
                ->withErrors($validator)
                ->withInput();
        }

        // Update user if necessary
        if ($request->filled('name') || $request->filled('email')) {
            $user = $vendor->user;
            $user->name = $request->name ?? $user->name;
            
            if ($request->filled('email') && $request->email !== $user->email) {
                $request->validate([
                    'email' => 'unique:users,email,' . $user->id,
                ]);
                $user->email = $request->email;
            }
            
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'string|min:8|confirmed',
                ]);
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
        }

        // Update vendor
        $vendor->update([
            'vendor_type' => $request->type,
            'company_name' => $request->name ?? $vendor->company_name,
            'contact_person' => $request->poc_name,
            'email' => $request->email ?? $vendor->email,
            'phone' => $request->contact_number,
            'skype_id' => $request->skype,
            'slack_id' => $request->slack,
            'internal_poc_id' => $request->internal_poc_id,
            'budget_3_years' => $request->budget_3_years,
            'budget_5_years' => $request->budget_5_years,
            'budget_7_years' => $request->budget_7_years,
            'budget_10_years' => $request->budget_10_years,
            'status' => $request->status,
        ]);

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

    public function vendorInvite(Request $request,$id)
    {
        $now = Carbon::now();
        $token_vendor_invite = VendorInviteTemporaryToken::where('invite_token','like',$id)->where('expiry_time','>',$now)->orderBy('id','desc')->first();
//        print_r($token_vendor_invite); exit();
        if(empty($token_vendor_invite)){
            return redirect()->route('login')
                ->with('error', 'Vendor invite expired.');
        }
        return view('vendor_invite.vendor_invite',compact('id'));
    }

    public function vendorEmailInvite(Request $request)
    {
            $email = $request->email;
            if(!empty($email)){
                $data = base64_encode($email);
                Mail::to($email)->send(new VendorInviteMail($data));

                $invite = new VendorInviteTemporaryToken();
                $invite->invite_token = $data;
                $invite->user_id = Auth::id();
                $invite->expiry_time = Carbon::now()->addDays(1);
                $invite->save();

                return redirect()->route('vendors.index')
                    ->with('success', 'Vendor invite sent successfully.');
            }
    }
    public function vendorPostInvite(Request $request)
    {
//print_r($request->all()); exit();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'account_owner_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'pan' => 'nullable|string|max:255',
            'teams_id' => 'nullable|string|max:255',
            'invite_token' => 'required|string|max:255',


        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create or find user
        if ($request->filled('existing_user_id')) {
            $user = User::findOrFail($request->existing_user_id);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? Str::random(12)),
                'role' => 'vendor',
            ]);
        }

        $vendor_invite = VendorInviteTemporaryToken::with('user_detail')->where('invite_token','like',$request->invite_token)->first();

        // Create vendor profile
        $vendor = Vendor::create([

            'company_name' => $request->name, // Using name as company_name
//            'contact_person' => $request->poc_name,
            'email' => $request->email,
            'address' => $request->address,
            'website' => $request->website,
            'account_owner_name' => $request->account_owner_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'gst_number' => $request->gst_number,
            'pan' => $request->pan,
            'teams_id' => $request->teams_id,
//            'email' => $request->email

            'contact_person' => !empty($vendor_invite) ? !empty($vendor_invite->user_detail) ? $vendor_invite->user_detail->name : 0 : 0,
//            'email' => $request->email ?? null,
            'phone' => $request->contact_number??rand(100000000,999999999),
            'skype_id' => $request->skype??1,
            'slack_id' => $request->slack??1,
            'internal_poc_id' => $request->internal_poc_id??1,
            'budget_3_years' => $request->budget_3_years??1,
            'budget_5_years' => $request->budget_5_years??1,
            'budget_7_years' => $request->budget_7_years??1,
            'budget_10_years' => $request->budget_10_years??1,
            'status' => $request->status??'pending',
        ]);

        VendorInviteTemporaryToken::where('invite_token','like',$request->invite_token)->delete();

        return redirect()->back()
            ->with('success', 'Vendor registered successfully.');
    }
    

}