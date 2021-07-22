<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\Models\Event;
class EventController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct(){
        $this->middleware('auth');
        parent::setModel('Event');
    }

    public function listing(Request $request)
    {
        $data = Event::select('events.id as id', 'events.name', 'starts_on', 'ends_on', 'events.address', 'status', 'users.name as posted_by')
                ->leftJoin('users', 'users.id', '=', 'events.user_id')
                ->orderBy('events.id', 'desc')
                ->get();
        foreach($data as $r){
            $r['Action'] = '<a class="btn btn-info btn-sm" href="'.url('/admin/event/'.$r->id.'/edit').'"><i class="fa fa-pencil"></i></a>';
        }
        return ['data' => $data];
    }

    public function edit($id)
    {
        $obj = Event::find($id);
        $heading = strtolower(preg_replace('/(?|([A-Z])([A-Z][a-z])|([a-z])([A-Z]))/', '${1} ${2}', 'event'));
        $fields = $obj->addList;
        $obj->days = implode(',', @json_decode($obj->days, true));
        return view('admin.common.edit', compact('fields','obj','heading'));
    }


    public function update(Request $request, $id){
        $request['starts_on'] = Carbon::parse($request['starts_on']);
        $request['ends_on'] = Carbon::parse($request['ends_on']);
        $obj = Event::find($id);
        if($obj)
        {
            $obj->name = $request->name;
            $obj->address = $request->address;
            $obj->fees = $request->fees;
            $obj->description = $request->description;
            $obj->winnings = $request->winnings;
            $obj->starts_on = $request->starts_on;
            $obj->ends_on = $request->ends_on;
            $obj->status = $request->status;
            $obj->fees = $request->fees;
            $obj->frequency = ucfirst($request->frequency);
            $obj->days = empty($request->days) ? '[]' : @json_encode(explode(',', $request->days));
            $obj->save();
            Session::flash('message', 'Record updated successfully');
        }
        else{
            Session::flash('message', 'Invalid ID');
        }
        return redirect("/admin/event");
    }
    
}