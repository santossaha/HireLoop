<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ApprovalRequiredNotification;
use App\Notifications\NewRequirementNotification;
use Yajra\DataTables\DataTables;

class RequirementController extends Controller
{
    /**
     * Display a listing of the requirements
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Requirement::query();
            
            // Apply filters
            if ($request->has('vendor_id') && !empty($request->vendor_id)) {
                $query->where('vendor_id', $request->vendor_id);
            }

            if ($request->has('department_id') && !empty($request->department_id)) {
                $query->where('department_id', $request->department_id);
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('requirement_id', 'like', "%{$search}%")
                      ->orWhereHas('vendor', function($q) use ($search) {
                          $q->where('company_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('department', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Get total records count
            $totalRecords = $query->count();

            // Apply pagination
            $requirements = $query->with(['vendor', 'department'])
                                ->skip($request->start)
                                ->take($request->length)
                                ->get();

            $data = [];
            foreach ($requirements as $requirement) {
                $data[] = [
                    'id' => $requirement->id,
                    'vendor' => $requirement->vendor->company_name,
                    'requirement_id' => $requirement->requirement_id,
                    'department' => $requirement->department->name ?? 'N/A',
                    'created_at' => $requirement->created_at->format('M d, Y'),
                    'actions' => view('requirement.partials.actions', compact('requirement'))->render()
                ];
            }

            

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }

        return view('requirement.index');
    }

    /**
     * Get status badge HTML
     */
    // private function getStatusBadge($requirement)
    // {
    //     if ($requirement->status == 'rejected') {
    //         return '<span class="badge bg-danger">Rejected</span>';
    //     } elseif ($requirement->founder_approved && $requirement->hod_approved) {
    //         return '<span class="badge bg-success">Approved</span>';
    //     } elseif ($requirement->hod_approved) {
    //         return '<span class="badge bg-warning">HOD Approved</span>';
    //     } else {
    //         return '<span class="badge bg-secondary">Pending HOD</span>';
    //     }
    // }

    /**
     * Show the form for creating a new requirement
     */
    public function create()
    {
        $vendors = Vendor::all();
        $departments = Department::all();
        
        // Generate the next Requirement ID
        $year = date('Y');
        $month = date('m');
        
        // Get the last requirement ID for this year and month
        $lastRequirement = Requirement::where('requirement_id', 'like', "REQ-{$year}-{$month}-%")
            ->orderBy('requirement_id', 'desc')
            ->first();

        if ($lastRequirement) {
            // Extract the sequence number and increment it
            $parts = explode('-', $lastRequirement->requirement_id);
            $sequence = (int) $parts[3] + 1;
        } else {
            // Start with sequence 1 if no requirements exist for this month
            $sequence = 1;
        }

        // Format: REQ-YYYY-MM-XXX (where XXX is a 3-digit sequence number)
        $requirement_id = sprintf("REQ-%s-%s-%03d", $year, $month, $sequence);
        
        return view('requirement.create', compact('vendors', 'departments', 'requirement_id'));
    }

    /**
     * Store a newly created requirement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'job_description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate the next Requirement ID
        $year = date('Y');
        $month = date('m');
        
        // Get the last requirement ID for this year and month
        $lastRequirement = Requirement::where('requirement_id', 'like', "REQ-{$year}-{$month}-%")
            ->orderBy('requirement_id', 'desc')
            ->first();

        if ($lastRequirement) {
            // Extract the sequence number and increment it
            $parts = explode('-', $lastRequirement->requirement_id);
            $sequence = (int) $parts[3] + 1;
        } else {
            // Start with sequence 1 if no requirements exist for this month
            $sequence = 1;
        }

        // Format: REQ-YYYY-MM-XXX (where XXX is a 3-digit sequence number)
        $requirement_id = sprintf("REQ-%s-%s-%03d", $year, $month, $sequence);

        // Create the requirement
        $requirement = Requirement::create([
            'vendor_id' => $request->vendor_id,
            'requirement_id' => $requirement_id,
            'job_description' => $request->job_description,
            'department_id' => $request->department_id,
            'status' => 'pending',
            'hod_approved' => false,
            'founder_approved' => false,
        ]);
        
        // Notify the HOD for approval
        $department = Department::find($request->department_id);
        $hod = $department->hod;
        
        if ($hod) {
            $hod->notify(new ApprovalRequiredNotification(
                'requirement',
                $requirement->id,
                'HOD Approval Required',
                "A new requirement has been submitted for vendor " . $requirement->vendor->company_name . " that requires your approval."
            ));
        }
        
        // Notify POC users in the same department
        $pocUsers = User::where('role', 'poc')
            ->where('department_id', $requirement->department_id)
            ->get();

        foreach ($pocUsers as $pocUser) {
            $pocUser->notify(new NewRequirementNotification($requirement));
        }
        
        return redirect()->route('requirements.index')
            ->with('success', 'Requirement created successfully and sent for HOD approval.');
    }

    /**
     * Display the specified requirement
     */
    public function show(Requirement $requirement)
    {
        $requirement->load(['vendor', 'department', 'approvedBy']);
        
        return view('requirement.show', compact('requirement'));
    }

    /**
     * Show the form for editing the specified requirement
     */
    public function edit(Requirement $requirement)
    {
        // Prevent editing if already approved
        if ($requirement->isApproved()) {
            return redirect()->route('requirements.show', $requirement->id)
                ->with('error', 'Cannot edit an approved requirement.');
        }
        
        $vendors = Vendor::all();
        $departments = Department::all();
        
        return view('requirement.edit', compact('requirement', 'vendors', 'departments'));
    }

    /**
     * Update the specified requirement
     */
    public function update(Request $request, Requirement $requirement)
    {
        // Prevent updating if already approved
        if ($requirement->isApproved()) {
            return redirect()->route('requirements.show', $requirement->id)
                ->with('error', 'Cannot update an approved requirement.');
        }
        
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'requirement_id' => 'required|string|max:50|unique:requirements,requirement_id,' . $requirement->id,
            'job_description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update the requirement
        $requirement->vendor_id = $request->vendor_id;
        $requirement->requirement_id = $request->requirement_id;
        $requirement->job_description = $request->job_description;
        $requirement->department_id = $request->department_id;
        $requirement->save();
        
        // If department changed, notify the new HOD
        if ($requirement->wasChanged('department_id')) {
            $department = Department::find($request->department_id);
            $hod = $department->hod;
            
            if ($hod) {
                $hod->notify(new ApprovalRequiredNotification(
                    'requirement',
                    $requirement->id,
                    'HOD Approval Required',
                    "A requirement has been updated for vendor " . $requirement->vendor->company_name . " that requires your approval."
                ));
            }
        }
        
        return redirect()->route('requirements.index')
            ->with('success', 'Requirement updated successfully.');
    }

    /**
     * Remove the specified requirement
     */
    public function destroy(Requirement $requirement)
    {
        // Prevent deletion if already approved
        if ($requirement->isApproved()) {
            return redirect()->route('requirements.index')
                ->with('error', 'Cannot delete an approved requirement.');
        }
        
        // Delete the CV file
        if ($requirement->cv_path) {
            Storage::delete($requirement->cv_path);
        }
        
        $requirement->delete();
        
        return redirect()->route('requirements.index')
            ->with('success', 'Requirement deleted successfully.');
    }
    
    /**
     * HOD approval for the requirement
     */
    public function hodApprove(Request $request, Requirement $requirement)
    {
        // Check if user is the HOD of the department
        if (!Auth::user()->isHod() || Auth::user()->department_id != $requirement->department_id) {
            return redirect()->route('requirements.show', $requirement->id)
                ->with('error', 'You are not authorized to approve this requirement.');
        }
        
        $validator = Validator::make($request->all(), [
            'approve' => 'required|boolean',
            'comments' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->approve) {
            $requirement->hod_approved = true;
            $requirement->save();
            
            // Notify founders for final approval
            $founders = User::where('role', 'founder')->get();
            
            foreach ($founders as $founder) {
                $founder->notify(new ApprovalRequiredNotification(
                    'requirement',
                    $requirement->id,
                    'Founder Approval Required for CV/Budget',
                    "A requirement for vendor " . $requirement->vendor->company_name . " has been approved by HOD and requires your final approval."
                ));
            }
            
            return redirect()->route('requirements.show', $requirement->id)
                ->with('success', 'Requirement approved and sent for founder approval.');
        } else {
            $requirement->status = 'rejected';
            $requirement->save();
            
            return redirect()->route('requirements.index')
                ->with('success', 'Requirement has been rejected.');
        }
    }
    
    /**
     * Founder approval for the requirement
     */
    public function founderApprove(Request $request, Requirement $requirement)
    {
        // Check if user is a founder
        if (!Auth::user()->isFounder()) {
            return redirect()->route('requirements.show', $requirement->id)
                ->with('error', 'You are not authorized to perform this action.');
        }
        
        $validator = Validator::make($request->all(), [
            'approve' => 'required|boolean',
            'comments' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->approve) {
            $requirement->founder_approved = true;
            $requirement->status = 'approved';
            $requirement->approved_by = Auth::id();
            $requirement->approved_at = now();
            $requirement->save();
            
            // Notify the vendor's POC
            $vendor = $requirement->vendor;
            $poc = $vendor->internalPoc;
            
            if ($poc) {
                $poc->notify(new ApprovalRequiredNotification(
                    'requirement',
                    $requirement->id,
                    'CV/Budget Approved',
                    "The CV and budget for " . $vendor->company_name . " has been fully approved and can be shared with the client."
                ));
            }
            
            return redirect()->route('requirements.show', $requirement->id)
                ->with('success', 'Requirement has been fully approved.');
        } else {
            $requirement->status = 'rejected';
            $requirement->save();
            
            return redirect()->route('requirements.index')
                ->with('success', 'Requirement has been rejected.');
        }
    }

    /**
     * Get pending counts for HOD and Founder
     */
    // public function getPendingCounts()
    // {
    //     $user = Auth::user();
    //     $response = [];

    //     if ($user->isHod()) {
    //         $response['pending_hod'] = Requirement::pendingHodApproval()
    //             ->forDepartment($user->department_id)
    //             ->count();
    //     } elseif ($user->isFounder()) {
    //         $response['pending_founder'] = Requirement::pendingFounderApproval()
    //             ->count();
    //     }

    //     return response()->json($response);
    // }

    /**
     * Get the next Requirement ID
     */
    public function getNextRequirementId()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last requirement ID for this year and month
        $lastRequirement = Requirement::where('requirement_id', 'like', "REQ-{$year}-{$month}-%")
            ->orderBy('requirement_id', 'desc')
            ->first();

        if ($lastRequirement) {
            // Extract the sequence number and increment it
            $parts = explode('-', $lastRequirement->requirement_id);
            $sequence = (int) $parts[3] + 1;
        } else {
            // Start with sequence 1 if no requirements exist for this month
            $sequence = 1;
        }

        // Format: REQ-YYYY-MM-XXX (where XXX is a 3-digit sequence number)
        return response()->json([
            'requirement_id' => sprintf("REQ-%s-%s-%03d", $year, $month, $sequence)
        ]);
    }
}
