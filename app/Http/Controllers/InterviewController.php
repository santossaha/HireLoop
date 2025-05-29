<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Interview;
use App\Models\Requirement;
use Illuminate\Http\Request;
use App\Models\CandidateSourcing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Mail\MockInterviewScheduled;
use Illuminate\Support\Facades\Mail;
use App\Mail\InterviewFeedback;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    /**
     * Display a listing of the interviews
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Interview::query()->with(['vendor', 'requirement', 'interviewer', 'candidate'])->orderBy('created_at', 'desc');

            // Filter by vendor_id only if user is a vendor
            if (Auth::user()->hasRole('bde')) {
                $query->withWhereHas('requirement', function($query) {
                    $query->where('create_by', Auth::id());
                });
            }
            
            // Apply filters
            if ($request->has('vendor_id') && !empty($request->vendor_id)) {
                $query->where('vendor_id', $request->vendor_id);
            }

            if ($request->has('type') && !empty($request->type)) {
                $query->where('type', $request->type);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            if ($request->has('result') && !empty($request->result)) {
                $query->where('result', $request->result);
            }

            // Search functionality
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('vendor', function($q) use ($search) {
                          $q->where('company_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('requirement', function($q) use ($search) {
                          $q->where('requirement_id', 'like', "%{$search}%");
                      });
                });
            }

            // Get total records count
            $totalRecords = $query->count();

            // Apply pagination
            $interviews = $query->skip($request->start)
                              ->take($request->length)
                              ->get();

            $data = [];
            foreach ($interviews as $interview) {
                $data[] = [
                    'id' => $interview->id,
                    'vendor' => '<a href="' . route('vendors.show', $interview->vendor_id) . '">' . 
                               $interview->vendor->contact_person . '</a>',
                    'requirement' => $interview->requirement ? 
                                   '<a href="' . route('requirements.show', $interview->requirement_id) . '">' . 
                                   $interview->requirement->requirement_id . '</a>' : 'N/A',
                    'type' => $this->getTypeBadge($interview->type),
                    'scheduled_at' => $interview->scheduled_at->format('M d, Y H:i'),
                    'candidate' => $interview->candidate->candidate_name ?? 'N/A',
                    'status' => $this->getStatusBadge($interview->status),
                    'result' => $this->getResultBadge($interview->result),
                    'actions' => view('interview.partials.actions', ['interview' => $interview])->render()
                ];
            }

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }

        // Get statistics for the dashboard cards
        $stats = [
            'upcoming' => Interview::upcoming()->count(),
            'pass_rate' => $this->calculatePassRate(),
            'this_week' => Interview::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'client_interviews' => Interview::ofType('client')->count()
        ];

        return view('interview.index', compact('stats'));
    }

    private function getTypeBadge($type)
    {
        $badges = [
            'mock' => '<span class="badge bg-secondary">Mock</span>',
            'internal' => '<span class="badge bg-info">Internal</span>',
            'client' => '<span class="badge bg-warning">Client</span>'
        ];
        return $badges[$type] ?? '';
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'scheduled' => '<span class="badge bg-primary">Scheduled</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>'
        ];
        return $badges[$status] ?? '';
    }

    private function getResultBadge($result)
    {
        $badges = [
            'pass' => '<span class="badge bg-success">Pass</span>',
            'fail' => '<span class="badge bg-danger">Fail</span>'
        ];
        return $badges[$result] ?? '<span class="badge bg-secondary">Pending</span>';
    }

    private function calculatePassRate()
    {
        $totalCompleted = Interview::withStatus('completed')->count();
        $totalPassed = Interview::withResult('pass')->count();
        return $totalCompleted > 0 ? round(($totalPassed / $totalCompleted) * 100) : 0;
    }

    /**
     * Show the form for creating a new interview
     */
    public function create(Request $request)
    {
        $candidateId = $request->input('candidate_id');
        $requirementId = $request->input('requirement_id');

        $requirement = Requirement::where('id', $requirementId)->with('vendor:id,user_id,email')->first();
        $candidate = CandidateSourcing::where('id', $candidateId)->with('requirement:id,requirement_id')->first();
        $uploadedBy = User::where('id', $candidate->uploaded_by)->first();
      
        $interviewers = User::where('role', 'poc')->get();

        return view('interview.create', compact('requirement', 'interviewers', 'candidate','uploadedBy'));
    }

    /**
     * Store a newly created interview
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'requirement_id' => 'required|exists:requirements,id',
            'candidate_id' => 'required|exists:candidate_sourcings,id',
            //'interviewer_id' => 'required|exists:users,id',
            'type' => 'required|in:mock,internal,client',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create the interview
        $interview = Interview::create([
            'vendor_id' => $request->vendor_id,
            'requirement_id' => $request->requirement_id,
            'candidate_id' => $request->candidate_id,
            'interviewer_id' => $request->interviewer_id,
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->status,
        ]);

        // Send email notification if it's a mock interview
        if ($request->type === 'mock') {    
            try {
                $interview->load(['vendor', 'candidate', 'requirement']);
                Mail::to($interview->vendor->email)->send(new MockInterviewScheduled($interview));
            } catch (\Exception $e) {
                \Log::error('Failed to send mock interview email: ' . $e->getMessage());
                // Continue execution even if email fails
            }
        }
        
        return redirect()->route('interviews.index')
            ->with('success', 'Interview scheduled successfully.');
    }

    /**
     * Display the specified interview
     */
    public function show(Interview $interview)
    {
        $interview->load(['vendor', 'requirement', 'candidate']);
        //dd($interview);
       
        
        return view('interview.show', compact('interview'));
    }

    /**
     * Show the form for editing the specified interview
     */
    public function edit(Interview $interview)
    {

        $requirement = Requirement::where('id', $interview->requirement_id)->with('vendor:id,user_id,email')->first();
        $vendor = Vendor::where('id', $interview->vendor_id)->first();
        $candidate = CandidateSourcing::where('id', $interview->candidate_id)->with('requirement:id,requirement_id')->first();

       
        // Prevent editing completed interviews
        if ($interview->status === 'completed') {
            return redirect()->route('interviews.show', $interview->id)
                ->with('error', 'Cannot edit a completed interview.');
        }
        
        
        return view('interview.edit', compact('candidate', 'vendor', 'requirement', 'interview'));
    }

    /**
     * Update the specified interview
     */
    public function update(Request $request, Interview $interview)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'requirement_id' => 'required|exists:requirements,id',
            'candidate_id' => 'required|exists:candidate_sourcings,id',
            'type' => 'required|in:mock,internal,client',
            'scheduled_at' => 'nullable|date',
            'status' => 'required|in:scheduled,completed,cancelled',
            'result' => 'nullable|in:pass,fail',
            'mock_feedback' => 'nullable|string',
            'client_interview_date_time' => 'nullable|date',
            'result' => 'nullable'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
       // Set result to null if empty or blank
       if (empty($request->result) || $request->result === ' ') {
           $request->merge(['result' => null]);
       }
     

        // Update the interview
        $interview->update($request->all());

        // If this is a mock interview being completed, send email notification
        if ($interview->type === 'mock' && $interview->status === 'completed') {
            $interview->load(['vendor', 'candidate', 'requirement']);
            try {
                Mail::to($interview->vendor->email)->queue(new MockInterviewScheduled($interview));
            } catch (\Exception $e) {
                \Log::error('Failed to send interview email: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('interviews.show', $interview->id)
            ->with('success', 'Interview updated successfully.');
    }

    /**
     * Remove the specified interview
     */
    public function destroy(Interview $interview)
    {
        // Prevent deleting completed interviews
        if ($interview->status === 'completed') {
            return redirect()->route('interviews.index')
                ->with('error', 'Cannot delete a completed interview.');
        }
        
        $interview->delete();
        
        return redirect()->route('interviews.index')
            ->with('success', 'Interview deleted successfully.');
    }

    /**
     * Submit feedback for an interview
     */
    public function submitFeedback(Request $request, Interview $interview)
    {
        $validator = Validator::make($request->all(), [
            'result' => 'required|in:pass,fail',
            'feedback' => 'required|string',
            'communication_rating' => 'required|in:excellent,good,average,bad',
            'technical_rating' => 'required|in:excellent,good,average,bad',
            'client_interview_ready' => 'required|boolean',
            'previously_worked_with_client' => 'nullable|boolean',
            'selected_in_internal' => 'nullable|boolean',
            'selected_in_client' => 'nullable|boolean',
            'last_approved_budget' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update interview with feedback
        $interview->result = $request->result;
        $interview->feedback = $request->feedback;
        $interview->communication_rating = $request->communication_rating;
        $interview->technical_rating = $request->technical_rating;
        $interview->client_interview_ready = $request->client_interview_ready;
        
        if ($request->has('previously_worked_with_client')) {
            $interview->previously_worked_with_client = $request->previously_worked_with_client;
        }
        
        if ($request->has('selected_in_internal')) {
            $interview->selected_in_internal = $request->selected_in_internal;
        }
        
        if ($request->has('selected_in_client')) {
            $interview->selected_in_client = $request->selected_in_client;
        }
        
        if ($request->has('last_approved_budget')) {
            $interview->last_approved_budget = $request->last_approved_budget;
        }
        
        $interview->status = 'completed';
        $interview->save();
        
        // Update vendor ratings based on the interview feedback
        $vendor = $interview->vendor;
        $vendor->communication_rating = $request->communication_rating;
        $vendor->technical_rating = $request->technical_rating;
        $vendor->client_ready = $request->client_interview_ready;
        $vendor->save();

        // Send email notification with feedback
        try {
            $interview->load(['vendor', 'candidate', 'requirement']);
            Mail::to($interview->vendor->email)->queue(new InterviewFeedback($interview));
        } catch (\Exception $e) {
            Log::error('Failed to send interview feedback email: ' . $e->getMessage());
        }
        
        return redirect()->route('interviews.show', $interview->id)
            ->with('success', 'Interview feedback submitted successfully.');
    }

    public function getStats(Request $request)
    {
        $query = Interview::query();

        // Apply filters
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('result') && !empty($request->result)) {
            $query->where('result', $request->result);
        }

        // Calculate statistics
        $stats = [
            'upcoming' => (clone $query)->upcoming()->count(),
            'pass_rate' => $this->calculatePassRateWithQuery($query),
            'this_week' => (clone $query)->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'client_interviews' => (clone $query)->ofType('client')->count()
        ];

        return response()->json($stats);
    }

    private function calculatePassRateWithQuery($query)
    {
        $totalCompleted = (clone $query)->withStatus('completed')->count();
        $totalPassed = (clone $query)->withResult('pass')->count();
        return $totalCompleted > 0 ? round(($totalPassed / $totalCompleted) * 100) : 0;
    }
}
