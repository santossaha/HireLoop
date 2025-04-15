<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Requirement;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InterviewController extends Controller
{
    /**
     * Display a listing of the interviews
     */
    public function index(Request $request)
    {
        $query = Interview::query();
        
        // Filter by vendor if provided
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // Filter by interview type if provided
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }
        
        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter by result if provided
        if ($request->has('result') && !empty($request->result)) {
            $query->where('result', $request->result);
        }
        
        // Filter by interviewer if user is an interviewer
        if (Auth::user()->role === 'poc') {
            $query->where('interviewer_id', Auth::id());
        }
        
        $interviews = $query->with(['vendor', 'requirement', 'interviewer'])->paginate(10);
        
        return view('interview.index', compact('interviews'));
    }

    /**
     * Show the form for creating a new interview
     */
    public function create()
    {
        $vendors = Vendor::all();
        $requirements = Requirement::where('hod_approved', true)
                                  ->where('founder_approved', true)
                                  ->get();
        $interviewers = User::where('role', 'poc')->get();
        
        return view('interview.create', compact('vendors', 'requirements', 'interviewers'));
    }

    /**
     * Store a newly created interview
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'requirement_id' => 'required|exists:requirements,id',
            'interviewer_id' => 'required|exists:users,id',
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
            'interviewer_id' => $request->interviewer_id,
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->status,
        ]);
        
        return redirect()->route('interviews.index')
            ->with('success', 'Interview scheduled successfully.');
    }

    /**
     * Display the specified interview
     */
    public function show(Interview $interview)
    {
        $interview->load(['vendor', 'requirement', 'interviewer']);
        
        return view('interview.show', compact('interview'));
    }

    /**
     * Show the form for editing the specified interview
     */
    public function edit(Interview $interview)
    {
        // Prevent editing completed interviews
        if ($interview->status === 'completed') {
            return redirect()->route('interviews.show', $interview->id)
                ->with('error', 'Cannot edit a completed interview.');
        }
        
        $vendors = Vendor::all();
        $requirements = Requirement::where('hod_approved', true)
                                  ->where('founder_approved', true)
                                  ->get();
        $interviewers = User::where('role', 'poc')->get();
        
        return view('interview.edit', compact('interview', 'vendors', 'requirements', 'interviewers'));
    }

    /**
     * Update the specified interview
     */
    public function update(Request $request, Interview $interview)
    {
        // Prevent updating completed interviews
        if ($interview->status === 'completed') {
            return redirect()->route('interviews.show', $interview->id)
                ->with('error', 'Cannot update a completed interview.');
        }
        
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'requirement_id' => 'required|exists:requirements,id',
            'interviewer_id' => 'required|exists:users,id',
            'type' => 'required|in:mock,internal,client',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update the interview
        $interview->update($request->all());
        
        return redirect()->route('interviews.index')
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
        // Verify that the user is the assigned interviewer or has permission
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $interview->interviewer_id) {
            return redirect()->route('interviews.show', $interview->id)
                ->with('error', 'You are not authorized to submit feedback for this interview.');
        }
        
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
        
        return redirect()->route('interviews.show', $interview->id)
            ->with('success', 'Interview feedback submitted successfully.');
    }
}
