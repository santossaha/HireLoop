<?php

namespace App\Http\Controllers;

use App\Models\InterviewSchedule;
use Illuminate\Http\Request;

class InterviewScheduleController extends Controller
{
    public function store(Request $request, $candidateId)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidate_sourcings,id',
            'requirement_id' => 'required|exists:requirements,id',
            'proceed_for_mock' => 'required|boolean',
            'mock_datetime' => 'required_if:proceed_for_mock,true|nullable|date'
        ], [
            'proceed_for_mock.boolean' => 'The proceed for mock field must be true or false.',
            'mock_datetime.date_format' => 'The mock datetime must be in Y-m-d H:i:s format.'
        ]);

        $schedule = InterviewSchedule::updateOrCreate(
            ['candidate_id' => $candidateId],
            [
                'requirement_id' => $request->requirement_id,
                'proceed_for_mock' => $request->boolean('proceed_for_mock'),
                'mock_datetime' => $request->mock_datetime
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Interview schedule updated successfully',
            'data' => $schedule
        ]);
    }
}
