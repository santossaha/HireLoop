<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Interview;
use App\Models\Onboarding;
use App\Models\EndReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $onboardings = Onboarding::with(['requirement', 'vendor', 'candidate'])
                ->select('onboardings.*');

            return DataTables::of($onboardings)
                ->addColumn('requirement_id', function($row) {
                    return $row->requirement->requirement_id ?? $row->requirement_id ?? 'N/A';
                })
                ->addColumn('vendor_name', function($row) {
                    return $row->vendor->company_name ?? 'N/A';
                })
                ->addColumn('candidate_name', function($row) {
                    return $row->candidate->candidate_name ??  $row->candidate_id ?? 'N/A';
                })
                ->addColumn('start_date', function($row) {
                    return $row->created_at->format('M d, Y  h:i a');
                })
                ->addColumn('status', function($row) {
                   return $this->getStatusBadge($row->status);
                })
                ->rawColumns(['status', 'actions'])

                ->addColumn('actions', function ($row) {
                    $actions = [];
                    
                    if (auth()->user()->can('view-onboarding-details')) {
                        $actions['show_url'] = route('onboardings.show', $row->id);
                    }
                    
                    if (auth()->user()->can('edit-onboarding')) {
                        $actions['edit_url'] = route('onboardings.edit', $row->id);
                    }
                    
                    if (auth()->user()->can('delete-onboarding')) {
                        $actions['delete_url'] = route('onboardings.destroy', $row->id);
                    }
                    
                    return $actions;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->input('search.value'))) {
                        $searchValue = $request->input('search.value');
                        
                        $query->where(function($q) use ($searchValue) {
                            $q->where(function($subQuery) use ($searchValue) {
                                $subQuery->whereHas('requirement', function($q) use ($searchValue) {
                                    $q->where('requirement_id', 'like', "%{$searchValue}%");
                                })
                                ->orWhere('requirement_id', 'like', "%{$searchValue}%");
                            })
                            ->orWhereHas('vendor', function($q) use ($searchValue) {
                                $q->where('company_name', 'like', "%{$searchValue}%");
                            })
                            ->orWhere(function($candidateQuery) use ($searchValue) {
                                $candidateQuery->whereHas('candidate', function($q) use ($searchValue) {
                                    $q->where('candidate_name', 'like', "%{$searchValue}%");
                                })
                                ->orWhere('candidate_id', 'like', "%{$searchValue}%");
                            });
                        });
                    }
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('onboarding.index');
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'Yet to Start' => '<span class="badge bg-secondary">Yet to Start</span>',
            'Running' => '<span class="badge bg-success">Running</span>',
            'Hold' => '<span class="badge bg-warning">Hold</span>',
            'Stopped' => '<span class="badge bg-danger">Stopped</span>'
        ];
        return $badges[$status] ?? '';
    }

    public function create(Interview $interview)
    {
        $vendors = Vendor::all();
        $delivery_managers = User::where('role', ['pm', 'dm'])->get();
       
        return view('onboarding.create', compact('interview', 'vendors', 'delivery_managers'));
    }

    public function store(Request $request)
    {

       // dd($request->all());
        $validator = Validator::make($request->all(), [
            //'requirement_id' => 'required',
            //'vendor_id' => 'required',
            //'candidate_id' => 'required',
            'client_budget' => 'required|numeric|min:0',
            'final_budget' => 'required|numeric|min:0',
            'timesheet_link' => 'nullable|url',
            'delivery_manager_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'billing_term' => 'required|string|max:255',
            'cycle_date' => 'required|date',
            'project_type' => 'required|in:hourly,monthly',
            'status' => 'required|in:Yet to Start,Running,Hold,Stopped'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $onboarding = Onboarding::create($request->all());

        return redirect()
            ->route('onboardings.index')
            ->with('success', 'Onboarding created successfully.');
    }

    public function show(Onboarding $onboarding)
    {
        $endReasons = EndReason::get();
        return view('onboarding.show', compact('onboarding', 'endReasons'));
    }

    public function edit(Onboarding $onboarding)
    {
        $vendors = Vendor::all();
        $delivery_managers = User::where('role', ['pm', 'dm'])->get();
        return view('onboarding.edit', compact('onboarding', 'vendors', 'delivery_managers'));
    }

    public function update(Request $request, Onboarding $onboarding)
    {
        $validator = Validator::make($request->all(), [
            'requirement_id' => 'required',
            'vendor_id' => 'required',
            'candidate_id' => 'required',
            'client_budget' => 'required|numeric|min:0',
            'final_budget' => 'required|numeric|min:0',
            'timesheet_link' => 'nullable|url',
            'delivery_manager_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'billing_term' => 'required|string|max:255',
            'cycle_date' => 'required|date',
            'project_type' => 'required|in:hourly,monthly',
            'status' => 'required|in:Yet to Start,Running,Hold,Stopped'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $onboarding->update($request->all());

        return redirect()
            ->route('onboardings.index')
            ->with('success', 'Onboarding updated successfully.');
    }

    public function destroy(Onboarding $onboarding){
        $onboarding->delete();
        return redirect()->route('onboardings.index')
            ->with('success', 'Onboarding deleted successfully.');  
    }

    public function endHiring(Request $request, Onboarding $onboarding)
    {
        $validator = Validator::make($request->all(), [
            'end_date' => 'required|date',
            'end_reason_id' => 'required|exists:end_reasons,id',
            'end_comments' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        

        $onboarding->update([
            'end_date' => $request->end_date,
            'end_reason_id' => $request->end_reason_id,
            'end_comments' => $request->end_comments,
            //'status' => 'Stopped'
        ]);

        return redirect()
            ->route('onboardings.show', $onboarding->id)
            ->with('success', 'Hiring ended successfully.');
    }
} 