<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
class ContentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct(){
        $this->middleware('auth');
        parent::setModel('Content');
    }
    public function index()
    {
        return parent::index();
    }

    
    public function create()
    {
        return parent::create();
    }

    
    public function listing(Request $request)
    {
        return parent::listing($request);
    }


    public function store(Request $request)
    {
        $name = strtoupper(str_replace(' ', '_', $request->slug));
        $request->request->add(['name' => $name]);
        return parent::store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return parent::edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return parent::update($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function change ($id){
       return parent::change($id);
    }
}
