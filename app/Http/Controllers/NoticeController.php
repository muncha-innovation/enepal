<?php

namespace App\Http\Controllers;

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
        if($request->hasFile('image')) {
            $image = $request->file('image');
            $data['image'] = upload('notices/', 'png', $image);
        }
        Notice::create($data);
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
        if($request->hasFile('image')) {
            $data['image'] = upload('notices/', 'png', $data['image']);
        }
        $notice->update($data);
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
}
