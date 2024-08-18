<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    //
    public function index() {
        $templates = EmailTemplate::all();
        return view('admin-views.emailTemplates.index', compact('templates'));
    }

    public function edit(Request $request, EmailTemplate $template) {
        $placeholders = ['userName', 'userEmail', 'userPassword', 'businessName', 'businessEmail', 'businessPhone', 'businessAddress'];

        return view('admin-views.emailTemplates.edit', compact(['template', 'placeholders']));
    }

    public function update(Request $request, EmailTemplate $template) 
    {
        $validated = $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'body' => 'required',
        ]);
        $template->update($validated);
        return redirect()->route('admin.templates.index');
    }
}
