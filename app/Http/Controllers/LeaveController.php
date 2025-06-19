<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function store(Request $request, Onboarding $onboarding)
    {
       
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string',
            'is_half_day' => 'nullable|boolean'
        ]);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
       
        
        // Calculate total days
        $totalDays = 0;
        $currentDate = $fromDate->copy();
        
        while ($currentDate <= $toDate) {
            // Skip weekends
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                // If it's the same day and half day is selected
                if ($fromDate->isSameDay($toDate) && $request->boolean('is_half_day')) {
                    $totalDays += 0.5;
                } else {
                    $totalDays += 1;
                }
            }
            $currentDate->addDay();
        }
        

        $leave = $onboarding->leaves()->create([
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'is_half_day' => $request->boolean('is_half_day'),
            'total_days' => $totalDays
        ]);
      

        return redirect()
            ->route('onboardings.show', encrypt_id($onboarding->id))
            ->with('success', 'Leave applied successfully.');
    }

    public function destroy(Onboarding $onboarding, Leave $leave)
    {
        // Check if the leave belongs to the onboarding
        if ($leave->onboarding_id !== $onboarding->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid leave record.'
            ], 400);
        }

        $leave->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave deleted successfully.'
        ]);
    }
} 