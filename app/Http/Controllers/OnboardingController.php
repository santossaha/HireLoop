<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Interview;
use App\Models\Onboarding;
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
                ->addColumn('actions', function ($row) {
                    return [
                        'show_url' => route('onboardings.show', $row->id),
                        'edit_url' => route('onboardings.edit', $row->id),
                        'delete_url' => route('onboardings.destroy', $row->id)
                    ];
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('onboarding.index');
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
            'project_type' => 'required|in:hourly,monthly'
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
        return view('onboarding.show', compact('onboarding'));
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
            'project_type' => 'required|in:hourly,monthly'
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
} 