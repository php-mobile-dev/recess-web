<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
// use App\Models\GroupTranslation;
use App\Models\Setting;
use Carbon\Carbon;
use Session;
use Hash;
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('auth');
        parent::setModel('User');
    }
    public function index()
    {
        return parent::index();
    }

    
    // public function create()
    // {
    //     $obj = new User;
    //     $heading = 'user';
    //     $groups = GroupTranslation::where('locale', session('current_locale'))->get();
    //     $fields = $obj->addList;
    //     return view('admin.common.add', compact('fields', 'heading', 'groups'));
    // }

    
    public function listing(Request $request)
    {
        return parent::listing($request);
    }


    public function store(Request $request)
    {
        $user = new User;
        $validation = $user->validation;
        $validation['email'] = 'required|email|unique:users,email';
        $validation['password'] = 'required';
        $request->validate($validation);

        $user->password = bcrypt($request->password);
            
        if($request->hasFile('avatar')){
            $file = $this->upload($request->avatar, 'uploads/users');
            $user->avatar = $file['file_name'];
        }
        $user->email = $request->email;
        $user->active = $request->active;
        $user->name = $request->name;
        $user->mobile_no = $request->mobile_no;
        $user->address = $request->address;
        $user->bio = $request->bio;
        $user->mobile_no_verified = $request->mobile_no_verified;
        $user->save();
        Session::flash('message', 'Record updated successfully');
        
        Session::flash('message', 'Record added successfully');
        return redirect("/admin/user");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id){
        $user = User::find($id);
        $posts = $user->posts()->withTrashed()->limit(10)->get();
        foreach($posts as $post){
            $post['media'] = $post->allMedia()->get();
        }
        $user->no_of_friends = $user->friends()->count();
        $user->no_of_posts = $user->posts()->withTrashed()->count();
        $user->no_of_feeds = $user->events()->count();
        $data = [
            'report' => [],
            'post' => $posts,
            'user' => $user,
            'media' => []
        ];
        return view('admin.post.view', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $obj = User::find($id);
    //     $heading = 'user';
    //     $fields = $obj->addList;
    //     $groups = GroupTranslation::where('locale', session('current_locale'))->get();
    //     return view('admin.common.edit', compact('fields','obj','heading', 'groups'));
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
        $user = User::find($id);
        $validation = $user->validation;
        $validation['email'] = 'required|email|unique:users,email,'.$id;
        $validation['password'] = 'sometimes';
        $request->validate($validation);
        
        if($user)
        {
            if(!Hash::check($request->password, $user->password))
            {
                $user->password = bcrypt($request->password);
            }
            if($request->hasFile('avatar')){
                $file = $this->upload($request->avatar, 'uploads/users');
                $user->avatar = $file['file_name'];
            }
            $user->email = $request->email;
            $user->active = $request->active;
            $user->name = $request->name;
            $user->mobile_no = $request->mobile_no;
            $user->address = $request->address;
            $user->bio = $request->bio;
            $user->mobile_no_verified = $request->mobile_no_verified;
            $user->save();
            Session::flash('message', 'Record updated successfully');
        }
        return redirect('/admin/user');
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



    public function import(Request $request) 
    {
        $error_bag = '';
        if($request->has('file'))
        {
            $row = 0;
            if (($handle = fopen($request->file, "r")) !== FALSE) 
            {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                {
                    $row++;
                    $error = $this->csvValidation($data);
                    if(empty($error))
                    {
                        $user = User::where('email', $data[0])->first();
                        if($user === null)
                        {
                            $user = User::Create(
                                [
                                    'email' => $data[0],
                                    'password' => bcrypt($data[1]),
                                    'is_active' => $data[2]
                                ]
                            );
                        }
                        else{
                            $error_bag.= "<li>Duplicate Entry on Line Number: $row </li>";
                        }
                    }
                    else
                    {
                        $error_bag.= "<li>$error on Line Number: $row </li>";
                    }
                }
                fclose($handle);
            }
        }
        if(!empty($error_bag))
            Session::flash('csv_error', $error_bag);
        else
            Session::flash('message', "$row number of records Imported");
        return redirect('/home');
    }

    private function csvValidation($data)
    {
        if(empty($data[0]) || empty($data[1]))
        {
            return 'Missing Data';
        }
        if($data[2] != 1 && $data[2] != 0)
        {
            return 'Is Active column must be either 0 or 1';
        }
        return '';
    }



    public function export(){
        $users = User::all();
        $data = [
                    'Email,Name,Company,Created at,Activation status'
                ];
        foreach($users as $user)
        {
            $email = $user->email;
            $name = $user->name;
            $company = str_replace(',', ' | ', $user->company);
            $created_at = Carbon::parse($user->created_at)->format('Y-m-d');
            $status = ($user->is_active == 1) ? 'Active' : 'Inactive' ;
            array_push($data,"$email , $name , $company , $created_at , $status");
        }

        $filename = "Users-".Carbon::now()->format('Y-m-d').".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $fp = fopen('php://output', 'wb');
        foreach ( $data as $line ) {
            $val = explode(",", $line);
            fputcsv($fp, $val);
        }
        fclose($fp);
    }


    public function notify($id){
        $user = User::find($id);
        $device = $user->device()->first();
        return view('admin.notify', compact('user', 'device'));

    }


    public function sendNotification(Request $request){
        return 'It Works';
    }
}