<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorTopCandidateController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Interview::with(['candidate','vendor','requirement','interviewer'])
                ->where('type','like','mock')
                ->where('result','like','pass');

            // Apply filters


            if ($request->has('department_id') && !empty($request->department_id)) {
                $rid = $request->department_id;
                $query->whereHas('requirement',function ($q) use ($rid){
                    $q->where('department_id', $rid);
                });
            }

            // Get total records count
            $totalRecords = $query->count();


            // Apply pagination
            $top_candidates = $query
                ->skip($request->start)
                ->take($request->length)
                ->get();

            $data = [];
            foreach ($top_candidates as $i => $top_candidate) {

                $skills = [];
                if(!empty($top_candidate->requirement)){
                    if(!empty($top_candidate->requirement->keySkills)){
                        foreach ($top_candidate->requirement->keySkills as $keySkill) {
                            $skills[] = $keySkill->name;
                        }
                    }
                }

                $data[] = [
                    'id' => $i+1,
                    'candidate_name' => !empty($top_candidate->candidate) ? $top_candidate->candidate->candidate_name : '-',
                    'skills' => implode(', ',$skills),
                    'client_budget' => !empty($top_candidate->requirement) ? $top_candidate->requirement->client_budget : 'N/A',
                    'final_budget' => !empty($top_candidate->requirement) ? $top_candidate->requirement->final_budget : 'N/A',
                    'contact_info' => [
                        'email' => !empty($top_candidate->candidate) ? $top_candidate->candidate->email : 'N/A',
                        'phone' => !empty($top_candidate->candidate) ? $top_candidate->candidate->phone : 'N/A'
                    ],
//                    'requirement_id' => !empty($top_candidate->requirement) ? $top_candidate->requirement->requirement_id : 'N/A',
                    'interviewer' => !empty($top_candidate->interviewer) ? $top_candidate->interviewer->name : 'N/A',
                    'resume' => '<a href="'.asset('storage/' . $top_candidate->candidate->resume_path).'" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-download"></i> Download </a>',
                    'mock_feedback' => $top_candidate->mock_feedback,
                    'actions' => '<a href="#" onclick="deleteCandidate('.$top_candidate->id.')" class="btn btn-danger mb-2"><i class="fa fa-trash"></i> </a>
<a href="'.route('interviews.show',$top_candidate->id).'" class="btn btn-info"><i class="fa fa-eye"></i></a>'
                ];
            }




            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }


        return view('vendors.top_candidates');
    }
    public function delete($id)
    {
        Interview::find($id)->delete();
        return redirect()->route('vendor.top-candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }

    public function view($id)
    {
        $top_candidate = Interview::find($id);
        return view('vendors.top_candidate_view',compact('top_candidate'));
    }
}
