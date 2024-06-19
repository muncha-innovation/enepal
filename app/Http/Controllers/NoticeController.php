<?php

namespace App\Http\Controllers;

use App\Events\NoticeCreated;
use App\Models\Notice;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNoticeRequest;
use App\Models\Business;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        //
        $notices = $business->notices()->paginate(10);
        return view('modules.notices.index', compact('notices','business'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Business $business)
    {
        $business->load('address.country');
        return view('modules.notices.createOrEdit', compact('business') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNoticeRequest $request, Business $business)
    {
        $data = $request->validated();
        if(!isset($data['title']['en']) && !isset($data['title']['np'])) {
            return redirect()->back()->with('error', 'Either English or Nepali title is required');
        }
        if(!isset($data['content']['en']) && !isset($data['content']['np'])) {
            return redirect()->back()->with('error', 'Either English or Nepali content is required');
        }
        
        if((isset($data['title']['en']) && !isset($data['content']['en'])) || (!isset($data['title']['en']) && isset($data['content']['en']))) {
            return redirect()->back()->with('error', 'Both English title and content are required');
        }
        $notice = new Notice();
        $notice->setTranslation('title', 'en', $data['title']['en'])
            ->setTranslation('title', 'np', $data['title']['np']);
        $notice->setTranslation('content', 'en', $data['content']['en'])
            ->setTranslation('content', 'np', $data['content']['np']);
        $notice->business_id = $business->id;
        $notice->active = $data['active'];
        $notice->is_private = $data['is_private'];
        if($notice->is_private) {
            $notice->is_verified = true;
        }
        $notice->user_id = auth()->id();
        if($request->hasFile('image')) {
            $image = $request->file('image');
            $notice->image = upload('notices/', 'png', $image);
        }
        $notice->save();
        if($notice->is_private) {
            event(new NoticeCreated($business, $notice));
            $notice->is_sent=true;
            $notice->sent_at = now();
            $notice->save();
        }
        return redirect()->route('notices.index',$business)->with('success','Notice Created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business, Notice $notice)
    {
        return view('modules.notices.show', compact('notice', 'business'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business, Notice $notice)
    {
        //
        return view('modules.notices.createOrEdit', compact('notice','business'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function update(StoreNoticeRequest $request, Business $business,Notice $notice)
    {

        $data = $request->validated();
        if(!isset($data['title']['en']) && !isset($data['title']['np'])) {
            return redirect()->back()->with('error', 'Either English or Nepali title is required');
        }
        if(!isset($data['content']['en']) && !isset($data['content']['np'])) {
            return redirect()->back()->with('error', 'Either English or Nepali content is required');
        }
        
        if((isset($data['title']['en']) && !isset($data['content']['en'])) || (!isset($data['title']['en']) && isset($data['content']['en']))) {
            return redirect()->back()->with('error', 'Both English title and content are required');
        }
        $notice->setTranslation('title', 'en', $data['title']['en'])
            ->setTranslation('title', 'np', $data['title']['np']);
        $notice->setTranslation('content', 'en', $data['content']['en'])
            ->setTranslation('content', 'np', $data['content']['np']);
        $notice->business_id = $business->id;
        $notice->active = $data['active'];
        $notice->is_private = $data['is_private'];
        $notice->user_id = auth()->id();
        if($notice->is_private) {
            $notice->is_verified = true;
        }
        if($request->hasFile('image')) {
            $image = $request->file('image');
            $notice->image = upload('notices/', 'png', $image);
        }
        $notice->save();
        if($notice->is_private && !$notice->is_sent) {
            event(new NoticeCreated($business, $notice));
            $notice->is_sent=true;
            $notice->sent_at = now();
            $notice->save();
        }
        return redirect()->route('notices.index', $business)->with('success', 'Notice updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */   
    public function destroy(Business $business, Notice $notice)
    {
        Notice::destroy($notice->id);
        return response()->json([
            'success'=> true,
        ]);
    }

    public function verify() {
        abort_if(!auth()->user()->hasRole('super-admin'), 403);
        $notification = Notice::find(request()->id);
        $notification->is_verified = true;

        $business = Business::find($notification->business_id);
        if(!$notification->is_sent) {
            event(new NoticeCreated($business, $notification));
            $notification->is_sent=true;
            $notification->sent_at = now();
        }
        $notification->save();
        return back()->with('success', 'Notification verified successfully');

    }
}
