<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Session;
use Auth;
use Hash;
use App\Models\Event;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::count();
        $users = User::where('type', 'app_user')->count();
        $feeds = Post::where('type', 'feed')->count();
        $subscribers = User::where('purchased', 1)->count();
        return view('admin.dashboard', compact('users', 'feeds', 'events', 'subscribers'));
    }

    
    public function changePassword(Request $request){
        return $request->all();
        if(!Hash::check($request->old_password, Auth::user()->password)){
            Session::flash('error', "Sorry! This is not the current password");
            return redirect("/change-password");
        }
        if($request->password != $request->new_password){
            Session::flash('error', "New paswword and confirm password did not match");
            return redirect("/change-password");
        }
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        Session::flash('message', "Password Changed Successfully!");
        return redirect("/admin/dashboard");
    }

    public function resetPassword(Request $request){
        return $request->all();
    }
}
