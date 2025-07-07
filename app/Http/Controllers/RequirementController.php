<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\CandidateSourcing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewRequirementNotification;
use App\Notifications\ApprovalRequiredNotification;
use App\Models\Company;
use App\Notifications\RequirementApprovalNotification;
use App\Events\RequirementApproved;
use App\Mail\HodApprovalConfirmation;
use Illuminate\Support\Facades\Mail;

class RequirementController extends Controller
{
    /**
     * Display a listing of the requirements
     */
    public function getTypeBadge($status,$is_closed) : string
    {
        $text = '';
        if($status){
            $text = '<span class="badge bg-success">Applied</span> <span class="badge bg-secondary">'.$status.' Resume</span>';
        }else{
            $text = '<span class="badge bg-info">Not Applied</span>';
        }
        if($is_closed == 1){
            $text.= '<span class="badge bg-danger">Closed</span>';
        }
        return $text;
    }

    public function index(Request $request)
    {
       
        if ($request->ajax()) {
            $query = Requirement::query();

            // Handle ordering from DataTables
            $columns = [
                0 => 'requirement_id',
                1 => 'company_id', // ya join karke companies.name
                2 => 'department_id', // ya join karke departments.name
                3 => 'title',
                4 => '', // skills (custom, skip)
                5 => 'bde_name',
                6 => 'client_budget',
                7 => 'final_budget',
                8 => 'created_at',
                9 => '', // created_by (custom, skip)
                10 => '', // candidate_count (custom, skip)
                11 => '', // actions (skip)
            ];

            if($request->has('order')){
                $orderCol = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                $orderBy = $columns[$orderCol] ?? 'created_at';
                if($orderBy){
                    $query->orderBy($orderBy,$orderDir);
                }else{
                    $query->orderBy('created_at', 'desc');
                }
            }


            
            // Filter by create_by only if user is a vendor
            if (Auth::user()->hasRole('bde')) {
                $query->withWhereHas('createBy', function($query) {
                    $query->where('create_by', Auth::user()->id);
                });
            }
            
            // Apply filters
            if ($request->has('company_id') && !empty($request->company_id)) {
                $query->where('company_id', $request->company_id);
            }

            if ($request->has('is_closed') && !empty($request->is_closed)) {
                $is_closed = $request->is_closed;
                if($is_closed == "1")
                    $query->where('is_closed', 1);
                else
                    $query->where('is_closed', '!=',1);
            }

            if ($request->has('department_id') && !empty($request->department_id)) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->has('create_by') && !empty($request->create_by)) {
                $query->where('create_by', $request->create_by);
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('requirement_id', 'like', "%{$search}%")
                      ->orWhereHas('company', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                        ->orWhereHas('department', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('create_by', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Get total records count
            $totalRecords = $query->count();

            // Apply pagination
            $requirements = $query->with(['company', 'department', 'createBy'])
                                ->skip($request->start)
                                ->take($request->length)
                                ->get();

            $data = [];
            foreach ($requirements as $requirement) {
                $userResumeCount = $requirement->candidateSourcing()
               // ->where('uploaded_by', Auth::id())
                ->count();
                $skills = [];
                if(!empty($requirement->keySkills)){
                    foreach ($requirement->keySkills as $keySkill) {
                        $skills[] = $keySkill->name;
                    }
                }

                $data[] = [
                    'id' => $requirement->id,
                    'title' => $requirement->title,
                    'skills' => implode(', ',$skills),
                    'bde_name' => $requirement->bde_name,
                    'client_budget' => $requirement->client_budget,
                    'final_budget' => $requirement->final_budget,
                    'company' => $requirement->company->name,
                    'requirement_id' => '<a href="'.route('requirements.show', $requirement->id).'" class="text-underline text-black">'.$requirement->requirement_id.'</a>',
                    'department' => $requirement->department->name ?? 'N/A',
                    'created_by' => $requirement->createBy->name ?? 'N/A',
                    'created_at' => $requirement->created_at->format('M d, Y  h:i a'),
                    'candidate_count' => $this->getTypeBadge($userResumeCount > 0 ? $userResumeCount : 0,$requirement->is_closed),
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

        $open_requirement_count = Requirement::where('is_closed',0)->count();
        $closed_requirement_count = Requirement::where('is_closed',1)->count();

        return view('requirement.index',compact('open_requirement_count','closed_requirement_count'));
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
        $companies = Company::all();
        $departments = Department::all();
        //dd($companies);
        
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
        
        return view('requirement.create', compact('companies', 'departments', 'requirement_id'));
    }

    /**
     * Store a newly created requirement
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'key_skills' => 'required|array|min:1',
            'key_skills.*' => 'exists:key_skills,id',
            'job_description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'client_budget' => 'required|numeric|min:0',
            'final_budget' => 'required|numeric|min:0',
            'show_budget_to_vendor' => 'nullable|boolean',
            'needs_hod_approval' => 'boolean',
            'custom_percentage_value' => 'nullable|numeric|min:0|max:100',
            'bde_name' => 'nullable|string|max:225'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate requirement ID...
        $requirement_id = $this->getNextRequirementId();

        // Create the requirement
        $requirement = Requirement::create([
            'company_id' => $request->company_id,
            'title' => $request->title,
            'requirement_id' => $requirement_id,
            'job_description' => $request->job_description,
            'department_id' => $request->department_id,
            'create_by' => Auth::user()->id,
            'needs_hod_approval' => $request->needs_hod_approval,
            'custom_percentage' => $request->custom_percentage_value,
            'show_budget_to_vendor' => $request->boolean('show_budget_to_vendor'),
            'client_budget' => $request->client_budget,
            'final_budget' => $request->final_budget,
            'is_approved' => 1, // Auto-approve if no HOD approval needed
            'bde_name' => $request->bde_name
        ]);

        // Sync key skills
        if ($request->has('key_skills')) {
            $requirement->keySkills()->sync($request->key_skills);
        }
        
        // If HOD approval is needed, send email
        if ($request->needs_hod_approval) {
            $department = Department::find($request->department_id);

            if ($department && $department->hod) {
                Mail::to($department->hod->email)->send(new HodApprovalConfirmation($requirement));
            }
        } else {
            // If no HOD approval needed, dispatch the event immediately
            event(new RequirementApproved($requirement));
        }
        
        return redirect()->route('requirements.index')
            ->with('success', 'Requirement created successfully.' . 
                ($request->needs_hod_approval ? ' Waiting for HOD approval.' : ''));
    }

    /**
     * Display the specified requirement
     */
    public function show($requirement)
    {
        $decryptedId = decrypt_id($requirement);
        $requirement = Requirement::findOrFail($decryptedId);
        $candidates = CandidateSourcing::with(['requirement', 'requirement.vendor', 'interviews'])
                    ->where('requirement_id', $requirement->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                   // dd($candidates);
      
        
        return view('requirement.show', compact('requirement', 'candidates'));
    }

    /**
     * Show the form for editing the specified requirement
     */
    public function edit($requirement)
    {
        $decryptedId = decrypt_id($requirement);
        $requirement = Requirement::findOrFail($decryptedId);
        // Prevent editing if already approved
        if ($requirement->isApproved()) {
            return redirect()->route('requirements.show', $requirement->id)
                ->with('error', 'Cannot edit an approved requirement.');
        }
        
        $companies = Company::all();
        $departments = Department::all();
        
        return view('requirement.edit', compact('requirement', 'companies', 'departments'));
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
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'key_skills' => 'required|array|min:1',
            'key_skills.*' => 'exists:key_skills,id',
            'requirement_id' => 'required|string|max:50|unique:requirements,requirement_id,' . $requirement->id,
            'job_description' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'client_budget' => 'required|numeric|min:0',
            'final_budget' => 'required|numeric|min:0',
            'show_budget_to_vendor' => 'nullable|boolean',
            'needs_hod_approval' => 'boolean',
            'custom_percentage_value' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update the requirement
        $requirement->update([
            'company_id' => $request->company_id,
            'title' => $request->title,
            'requirement_id' => $request->requirement_id,
            'job_description' => $request->job_description,
            'department_id' => $request->department_id,
            'create_by' => Auth::user()->id,
            'needs_hod_approval' => $request->needs_hod_approval,
            'custom_percentage' => $request->custom_percentage_value,
            'show_budget_to_vendor' => $request->boolean('show_budget_to_vendor'),
            'client_budget' => $request->client_budget,
            'final_budget' => $request->final_budget,
        ]);

        // Sync key skills
        if ($request->has('key_skills')) {
            $requirement->keySkills()->sync($request->key_skills);
        }
        
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
    // public function hodApprove(Request $request, Requirement $requirement)
    // {
    //     // Check if user is the HOD of the department
    //     if (!Auth::user()->isHod() || Auth::user()->department_id != $requirement->department_id) {
    //         return redirect()->route('requirements.show', $requirement->id)
    //             ->with('error', 'You are not authorized to approve this requirement.');
    //     }
        
    //     $validator = Validator::make($request->all(), [
    //         'approve' => 'required|boolean',
    //         'comments' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     if ($request->approve) {
    //         $requirement->hod_approved = true;
    //         $requirement->save();
            
    //         // Notify founders for final approval
    //         $founders = User::where('role', 'founder')->get();
            
    //         foreach ($founders as $founder) {
    //             $founder->notify(new ApprovalRequiredNotification(
    //                 'requirement',
    //                 $requirement->id,
    //                 'Founder Approval Required for CV/Budget',
    //                 "A requirement for vendor " . $requirement->vendor->company_name . " has been approved by HOD and requires your final approval."
    //             ));
    //         }
            
    //         return redirect()->route('requirements.show', $requirement->id)
    //             ->with('success', 'Requirement approved and sent for founder approval.');
    //     } else {
    //         $requirement->status = 'rejected';
    //         $requirement->save();
            
    //         return redirect()->route('requirements.index')
    //             ->with('success', 'Requirement has been rejected.');
    //     }
    // }
    
    /**
     * Founder approval for the requirement
     */
    // public function founderApprove(Request $request, Requirement $requirement)
    // {
    //     // Check if user is a founder
    //     if (!Auth::user()->isFounder()) {
    //         return redirect()->route('requirements.show', $requirement->id)
    //             ->with('error', 'You are not authorized to perform this action.');
    //     }
        
    //     $validator = Validator::make($request->all(), [
    //         'approve' => 'required|boolean',
    //         'comments' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     if ($request->approve) {
    //         $requirement->founder_approved = true;
    //         $requirement->status = 'approved';
    //         $requirement->approved_by = Auth::id();
    //         $requirement->approved_at = now();
    //         $requirement->save();
            
    //         // Notify the vendor's POC
    //         $vendor = $requirement->vendor;
    //         $poc = $vendor->internalPoc;
            
    //         if ($poc) {
    //             $poc->notify(new ApprovalRequiredNotification(
    //                 'requirement',
    //                 $requirement->id,
    //                 'CV/Budget Approved',
    //                 "The CV and budget for " . $vendor->company_name . " has been fully approved and can be shared with the client."
    //             ));
    //         }
            
    //         return redirect()->route('requirements.show', $requirement->id)
    //             ->with('success', 'Requirement has been fully approved.');
    //     } else {
    //         $requirement->status = 'rejected';
    //         $requirement->save();
            
    //         return redirect()->route('requirements.index')
    //             ->with('success', 'Requirement has been rejected.');
    //     }
    // }

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
        return sprintf("REQ-%s-%s-%03d", $year, $month, $sequence);
      
    }



    public function approve(Requirement $requirement)
    {
        // Check if user is HOD of the department
        if (!auth()->user()->hasRole('hod') || auth()->user()->id !== $requirement->department->hod_id) {
            abort(403, 'Unauthorized action.');
        }

        $requirement->update([
            'is_approved' => true
        ]);

        // Dispatch the event after HOD approval
        event(new RequirementApproved($requirement));

        return redirect()->route('requirements.show', $requirement)
            ->with('success', 'Requirement approved successfully.');
    }

    public function closeRequirement($id)
    {


        Requirement::find($id)->update([
            'is_closed' => true
        ]);

        return redirect()->route('requirements.show', $id)
            ->with('success', 'Requirement closed successfully.');
    }
}
