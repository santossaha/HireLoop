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

class RequirementController extends Controller
{
    /**
     * Display a listing of the requirements
     */
    public function index(Request $request)
    {
        $query = Requirement::query();
        
        // Filter by department if user is an HOD
        if (Auth::user()->isHod()) {
            $query->where('department_id', Auth::user()->department_id);
        }
        
        // Filter by vendor if provided
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'pending_hod') {
                $query->where('hod_approved', false);
            } elseif ($request->status === 'pending_founder') {
                $query->where('hod_approved', true)
                      ->where('founder_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('hod_approved', true)
                      ->where('founder_approved', true);
            }
        }
        
        $requirements = $query->with(['vendor', 'department'])->paginate(10);
        
        return view('requirement.index', compact('requirements'));
    }

    /**
     * Show the form for creating a new requirement
     */
    public function create()
    {
        $vendors = Vendor::all();
        $departments = Department::all();
        
        return view('requirement.create', compact('vendors', 'departments'));
    }

    /**
     * Store a newly created requirement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'requirement_id' => 'required|string|max:50|unique:requirements',
            'job_description' => 'required|string',
            'client_budget' => 'required|numeric|min:0',
            'proposed_budget' => 'required|numeric|min:0',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store the CV file
        $cvPath = $request->file('cv_file')->store('cv_files');

        // Create the requirement
        $requirement = Requirement::create([
            'vendor_id' => $request->vendor_id,
            'requirement_id' => $request->requirement_id,
            'job_description' => $request->job_description,
            'client_budget' => $request->client_budget,
            'proposed_budget' => $request->proposed_budget,
            'cv_path' => $cvPath,
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
                'HOD Approval Required for CV/Budget',
                "A new requirement has been submitted for vendor " . $requirement->vendor->company_name . " that requires your approval."
            ));
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
            'client_budget' => 'required|numeric|min:0',
            'proposed_budget' => 'required|numeric|min:0',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle CV file update if provided
        if ($request->hasFile('cv_file')) {
            // Delete old file
            if ($requirement->cv_path) {
                Storage::delete($requirement->cv_path);
            }
            
            // Store new file
            $cvPath = $request->file('cv_file')->store('cv_files');
            $requirement->cv_path = $cvPath;
        }

        // Update the requirement
        $requirement->vendor_id = $request->vendor_id;
        $requirement->requirement_id = $request->requirement_id;
        $requirement->job_description = $request->job_description;
        $requirement->client_budget = $request->client_budget;
        $requirement->proposed_budget = $request->proposed_budget;
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
                    'HOD Approval Required for CV/Budget',
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
}
