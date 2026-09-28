<?php

namespace App\Http\Controllers;

use App\Models\ApplicationForm;
use Illuminate\Http\Request;

class PublicFormController extends Controller
{
    /**
     * Display the specified public form.
     *
     * @param  string  $code
     * @return \Illuminate\View\View
     */
    public function show($code)
    {
        $link = \App\Models\FormLink::where('link_code', $code)->firstOrFail();
        
        if (!$link->is_active) {
            abort(403, 'This shareable link is currently inactive.');
        }

        $form = $link->applicationForm()->with(['institution.organization'])->firstOrFail();
        
        if ($form->status !== 'active') {
            abort(403, 'This form is no longer accepting submissions.');
        }

        return view('forms.public_show', compact('form', 'link'));
    }
}
