<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Interview;
use App\Models\Requirement;
use Illuminate\Http\Request;
use App\Mail\NewResumeUploaded;
use App\Models\CandidateSourcing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CandidateSourcingController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */

     public function getTypeBadge($status) : string
    {
        if($status){
            return '<span class="badge bg-success">Applied</span> <span class="badge bg-secondary">'.$status.' Resume</span>';
        }else{
            return '<span class="badge bg-info">Not Applied</span>';
        }
    }

    
    public function index(Request $request)
    {
      
      
        if ($request->ajax()) {
            $query = Requirement::query()->where('is_approved', 1)->where('is_closed', 0)->orderBy('created_at', 'desc');
            
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
                      ->orWhereHas('createBy', function($q) use ($search) {
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
                    ->where('uploaded_by', Auth::id())
                    ->count();
                
                $data[] = [
                    'id' => $requirement->id,
                    'company' => $requirement->company->name,
                    'requirement_id' => $requirement->requirement_id,
                    'department' => $requirement->department->name ?? 'N/A',
                    'created_by' => $requirement->createBy->name ?? 'N/A',
                    'created_at' => $requirement->created_at->format('M d, Y  h:i a'),
                    'status' => $this->getTypeBadge($userResumeCount > 0 ? $userResumeCount : 0),
                    'actions' => view('candidate-sourcing.partials.actions', compact('requirement'))->render()
                ];
            }

            

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }

        return view('candidate-sourcing.index');
    }





    /**
     * Display the specified resource.
     */
    public function show(Requirement $requirement, $id)
    {
       
        $requirement = Requirement::where('is_approved', 1)->find($id);
       
       
        $candidates = CandidateSourcing::with('interviews')->where('uploaded_by', Auth::id())->where('requirement_id', $id)->get();
        //$interviews = Interview::where('id', 25)->first();
       
       
      
        return view('candidate-sourcing.show', compact('requirement', 'candidates'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function approve(Request $request, CandidateSourcing $candidateSourcing)
    {
       

        $request->validate([
            'review_notes' => 'required|string'
        ]);

        $candidateSourcing->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes
        ]);

        return redirect()->route('candidate-sourcing.index')
            ->with('success', 'Candidate approved successfully.');
    }

    public function reject(Request $request, CandidateSourcing $candidateSourcing)
    {
        

        $request->validate([
            'review_notes' => 'required|string'
        ]);

        $candidateSourcing->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes
        ]);

        return redirect()->route('candidate-sourcing.index')
            ->with('success', 'Candidate rejected successfully.');
    }

    public function scheduleInterview(Request $request, CandidateSourcing $candidateSourcing)
    {
        
        $request->validate([
            'interview_date' => 'required|date|after:today',
            'interview_time' => 'required'
        ]);

        $interviewDateTime = $request->interview_date . ' ' . $request->interview_time;

        $candidateSourcing->update([
            'interview_scheduled_at' => $interviewDateTime
        ]);

        return redirect()->route('candidate-sourcing.index')
            ->with('success', 'Interview scheduled successfully.');
    }

    public function uploadCandidate(Request $request, Requirement $requirement)
    {
        $request->validate([
            'candidate_name' => 'required|string|max:255',
            //'email' => 'email|max:255',
            //'phone' => 'string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        // Store the resume file
        $resumePath = $request->file('resume')->store('resumes', 'public');

        // Create the candidate record
        $candidate = CandidateSourcing::create([
            'requirement_id' => $requirement->id,
            'uploaded_by' => Auth::id(),
            'candidate_name' => $request->candidate_name,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'resume_path' => $resumePath,
            'budget' => $request->budget,
            'candidate_details' => $request->notes ?? null,
            'status' => 'pending'
        ]);

        // Send email notification
        $pocUsers = User::role('bde')->get();
        foreach ($pocUsers as $pocUser) {
            try {
                Mail::to($pocUser->email)->queue(new NewResumeUploaded($candidate));
            } catch (\Exception $e) {
                // Silently handle email sending failure
                \Log::error('Failed to send interview email: ' . $e->getMessage());
            }
           
        }

       
        

        return redirect()->back()->with('success', 'Candidate details uploaded successfully.');
    }

    private function getStatusBadge($requirement)
    {
        if ($requirement->status == 'rejected') {
            return '<span class="badge bg-danger">Rejected</span>';
        } elseif ($requirement->founder_approved && $requirement->hod_approved) {
            return '<span class="badge bg-success">Approved</span>';
        } elseif ($requirement->hod_approved) {
            return '<span class="badge bg-warning">HOD Approved</span>';
        } else {
            return '<span class="badge bg-secondary">Pending HOD</span>';
        }
    }
}
