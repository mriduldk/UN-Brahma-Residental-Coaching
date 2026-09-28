<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\ApplicationForm;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $instituteFilter = $request->input('institute_id');

        $query = Institution::whereHas('application_forms', function ($q) {
            $q->where('status', 'active');
        })->withCount(['application_forms as active_forms_count' => function ($q) {
            $q->where('status', 'active');
        }])->with(['application_forms' => function ($q) {
            $q->where('status', 'active')->orderBy('created_at', 'desc');
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('institution_name', 'like', "%{$search}%")
                  ->orWhere('institution_code', 'like', "%{$search}%");
            });
        }

        if ($instituteFilter) {
            $query->where('id', $instituteFilter);
        }

        $institutions = $query->orderBy('institution_name')->paginate(9);

        $allActiveInstitutions = Institution::whereHas('application_forms', function($q) {
            $q->where('status', 'active');
        })->orderBy('institution_name')->get();

        return view('discover.index', compact('institutions', 'allActiveInstitutions', 'search', 'instituteFilter'));
    }

    public function institute($id)
    {
        $institution = Institution::findOrFail($id);
        
        $forms = ApplicationForm::where('institution_id', $id)
                                ->where('status', 'active')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('discover.institute', compact('forms', 'institution'));
    }
}
