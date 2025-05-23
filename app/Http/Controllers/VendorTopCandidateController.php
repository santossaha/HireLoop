<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\Request;

class VendorTopCandidateController extends Controller
{
    public function index(Request $request)
    {
        $top_candidates = Interview::with(['candidate','vendor','requirement','interviewer'])
            ->where('type','like','mock')
            ->where('result','like','pass')->orderBy('id','desc')->get();
        return view('vendors.top_candidates',compact('top_candidates'));
    }
    public function delete($id)
    {
        Interview::find($id)->delete();
        return redirect()->route('vendor.top-candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }
}
