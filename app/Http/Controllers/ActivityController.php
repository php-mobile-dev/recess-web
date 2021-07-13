<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
class ActivityController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct(){
        $this->middleware('auth');
        parent::setModel('Activity');
    }
    
}
