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
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('vendorInvite','vendorPostInvite');
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
        if($request->ajax()){
            $query = Vendor::query()->orderBy('created_at', 'desc');
            $query->with('user');

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
                        ->orWhereHas('user', function($q) use ($search) {
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
                    'name' => !empty($vendor->user) ? $vendor->user->name : '-',
                    'vendor_type' => ucfirst($vendor->vendor_type),
                    'contact_person' => $vendor->contact_person,
                    'contact_info' => [
                        'email' => $vendor->email,
                        'phone' => $vendor->phone
                    ],
                    'internal_poc' => $vendor->internalPoc ? $vendor->internalPoc->name : 'N/A',
//                    'status' => $vendor->status,
//                    'client_ready' => $vendor->client_ready,
                    'actions' => view('vendors.partials.actions', compact('vendor'))->render()
                ];
            }
            $draw = $request->get('draw');
            $datas = array(
                'draw' => $draw,
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data,
            );

            echo json_encode($datas);
        }
        else{

            return view('vendors.index');
        }

        

    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $this->authorize('create', Vendor::class);
        
        $internalPocs = User::whereIn('role', [ 'poc', 'hod', 'bde'])->get();
        
        return view('vendors.create', compact('internalPocs'));
    }

    /**
     * Helper function to handle vendor creation with rollback
     */
    private function createVendorWithUser(array $data)
    {

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'vendor',
                'status' => 'approved',
            ]);

            // Assign vendor role and permissions
            $role = Role::where('name', 'vendor')
                ->where('guard_name', 'web')
                ->firstOrFail();

            $user->assignRole($role);

            // Sync permissions based on the role
            $permissions = $role->permissions()
                ->where('guard_name', 'web')
                ->pluck('name')
                ->toArray();

            $user->syncPermissions($permissions);

            // Create vendor profile
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'company_name' => $data['name'],
                'vendor_type' => $data['vendor_type'],
                'contact_person' => $data['name'],
                'email' => $data['company_email'],
                'phone' => $data['phone'],
//                'skype_id' => $data['skype_id'],
                'internal_poc_id' => $data['internal_poc_id'],
                'company_name' => $data['company_name'],
                'address' => $data['address'],
                'website' => $data['website'],
                'account_owner_name' => $data['account_owner_name'],
                'account_number' => $data['account_number'],
                'bank_name' => $data['bank_name'],
                'ifsc_code' => $data['ifsc_code'],
                'gst_number' => $data['gst_number'],
                'pan' => $data['pan'],
                'teams_id' => $data['teams_id'],
                'year_of_experience' => $data['year_of_experience']??0,
                'budget' => $data['budget']??0,
//                'internal_poc_id' => $data['internal_poc_id'],
               'budget_3_years' => $data['budget_3_years']??0,
               'budget_5_years' => $data['budget_5_years']??0,
               'budget_7_years' => $data['budget_7_years']??0,
               'budget_10_years' => $data['budget_10_years']??0,
                'status' => 'approved',

            ]);

            if (isset($data['key_skills'])) {
                $vendor->keySkills()->sync($data['key_skills']);
            }

            DB::commit();
            return ['success' => true, 'vendor' => $vendor];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error creating vendor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'string|max:255',
            'vendor_type' => 'required|in:company,individual',
//            'poc_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|unique:vendors',
            'phone' => 'required|string|max:20',
//            'skype_id' => 'nullable|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
//            'budget_3_years' => 'required|numeric|min:0',
//            'budget_5_years' => 'required|numeric|min:0',
//            'budget_7_years' => 'required|numeric|min:0',
//            'budget_10_years' => 'required|numeric|min:0',
            //'status' => 'required|in:pending,approved,rejected',
//            'key_skills' => 'array',
//            'key_skills.*' => 'exists:key_skills,id',
            'password' => 'nullable|string|min:8|confirmed',
             'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'account_owner_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'pan' => 'nullable|string|max:255',
            'teams_id' => 'nullable|string|max:255'

        ]);

        $result = $this->createVendorWithUser($validated);

        if ($result['success']) {
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor created successfully.');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
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
        
        $internalPocs = User::whereIn('role', [ 'poc', 'hod', 'bde'])->get();
        
        return view('vendors.edit', compact('vendor', 'internalPocs'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([

            'name' => 'string|max:255',
            'vendor_type' => 'required|in:company,individual',
//            'poc_name' => 'required|string|max:255',
//            'email' => 'required|email|unique:users|unique:vendors',
            'phone' => 'required|string|max:20',
//            'skype_id' => 'nullable|string|max:255',
            'internal_poc_id' => 'required|exists:users,id',
//            'budget_3_years' => 'required|numeric|min:0',
//            'budget_5_years' => 'required|numeric|min:0',
//            'budget_7_years' => 'required|numeric|min:0',
//            'budget_10_years' => 'required|numeric|min:0',
            //'status' => 'required|in:pending,approved,rejected',
//            'key_skills' => 'array',
//            'key_skills.*' => 'exists:key_skills,id',
            'password' => 'nullable|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|email|max:255',
//            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'account_owner_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'pan' => 'nullable|string|max:255',
            'teams_id' => 'nullable|string|max:255',

//            'vendor_type' => 'required|in:company,freelancer',
            //'company_name' => 'required|string|max:255',
//            'poc_name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
//            'contact_number' => 'required|string|max:20',
//            'skype_id' => 'nullable|string|max:255',
//            'slack_id' => 'nullable|string|max:255',
//            'internal_poc_id' => 'required|exists:users,id',
//            'budget_3_years' => 'required|numeric|min:0',
//            'budget_5_years' => 'required|numeric|min:0',
//            'budget_7_years' => 'required|numeric|min:0',
//            'budget_10_years' => 'required|numeric|min:0',
//            'status' => 'required|in:pending,approved,rejected',
//            'key_skills' => 'array',
//            'key_skills.*' => 'exists:key_skills,id'
        ]);

        $vendor->update($request->all());

        // if (isset($validated['key_skills'])) {
        //     $vendor->keySkills()->sync($validated['key_skills']);
        // }

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
        // Get the vendor's email
        $vendorEmail = $vendor->email;

        // Find and delete associated user with same email
        $user = User::where('email', $vendorEmail)->first();
        if ($user) {
            $user->forceDelete();
        }

        // Force delete the vendor
        $vendor->forceDelete();

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

    public function vendorInvite(Request $request, $id)
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

            'user_id' => $user->id, // Using name as company_name
            'company_name' => $request->company_name, // Using name as company_name
            'vendor_type' => $request->vendor_type, // Using name as company_name
//            'contact_person' => $request->poc_name,
            'email' => $request->company_email,
            'address' => $request->address,
            'website' => $request->website,
            'account_owner_name' => $request->account_owner_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'gst_number' => $request->gst_number,
            'pan' => $request->pan,
            'teams_id' => $request->teams_id,
            'internal_poc_id' => !empty($vendor_invite) ? !empty($vendor_invite->user_detail) ? $vendor_invite->user_detail->id : 0 : 0,
//            'email' => $request->email

            'contact_person' => !empty($vendor_invite) ? !empty($vendor_invite->user_detail) ? $vendor_invite->user_detail->name : 0 : 0,
//            'email' => $request->email ?? null,
            'phone' => $request->phone??rand(100000000,999999999),
            'skype_id' => $request->skype??1,
            'slack_id' => $request->slack??1,
//            'internal_poc_id' => $request->internal_poc_id??1,
            'budget_3_years' => $request->budget_3_years??0,
            'budget_5_years' => $request->budget_5_years??0,
            'budget_7_years' => $request->budget_7_years??0,
            'budget_10_years' => $request->budget_10_years??0,

            'year_of_experience' => $request->year_of_experience??0,
            'budget' => $request->budget??0,

            'status' => $request->status??'pending',
        ]);

        VendorInviteTemporaryToken::where('invite_token','like',$request->invite_token)->delete();

        return redirect()->back()
            ->with('success', 'Vendor registered successfully.');
    }


}