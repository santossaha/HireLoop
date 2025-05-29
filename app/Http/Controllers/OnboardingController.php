<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Interview;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    public function create(Interview $interview)
    {
        $vendors = Vendor::all();
        
        return view('onboarding.create', compact('interview', 'vendors'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'requirement_id' => 'required|exists:requirements,id',
            'vendor_id' => 'required|exists:vendors,id',
            'candidate_id' => 'required|exists:candidate_sourcings,id',
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
            ->route('onboardings.show', $onboarding)
            ->with('success', 'Onboarding created successfully.');
    }

    public function show(Onboarding $onboarding)
    {
        return view('onboarding.show', compact('onboarding'));
    }

    public function edit(Onboarding $onboarding)
    {
        return view('onboarding.edit', compact('onboarding'));
    }

    public function update(Request $request, Onboarding $onboarding)
    {
        $validator = Validator::make($request->all(), [
            'requirement_id' => 'required|exists:requirements,id',
            'vendor_id' => 'required|exists:vendors,id',
            'candidate_id' => 'required|exists:candidate_sourcings,id',
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
            ->route('onboardings.show', $onboarding)
            ->with('success', 'Onboarding updated successfully.');
    }
} 