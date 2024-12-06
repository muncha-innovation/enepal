<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationTemplateRequest;
use App\Models\EmailTemplate;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    //
    public function index() {
        $templates = NotificationTemplate::all();
        // dd($templates);
        return view('admin.notification.template.index', compact(['templates']));
    }
    public function global(Request $request) {
        dd('global templates');
    }


    public function edit(Request $request, NotificationTemplate $template) {
        
        // dd($template->placeholders);
        return view('admin.notification.template.email', compact(['template']));
    }

    public function update(StoreNotificationTemplateRequest $request, NotificationTemplate $template) 
    {
        $validated = $request->validated();
        
        // Handle translatable fields
        $translatableFields = ['subject', 'push_title', 'email_body', 'sms_body', 'push_body'];
        
        foreach ($translatableFields as $field) {
            if (isset($validated[$field])) {
                $template->setTranslations($field, $validated[$field]);
            }
        }
        
        // Handle non-translatable fields
        $nonTranslatableFields = array_diff(array_keys($validated), $translatableFields);
        $template->fill(array_intersect_key($validated, array_flip($nonTranslatableFields)));
        
        $template->save();
        
        return redirect()->route('admin.templates.index')->with('success', __('Template updated successfully'));
    }
}
