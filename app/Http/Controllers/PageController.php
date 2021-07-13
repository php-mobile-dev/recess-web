<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Intro;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Crypt;

class PageController extends Controller
{
    public function show($slug)
    {
        $name = strtoupper(str_replace('-','_',$slug));
        $content = Content::where('name', $name)->first();
        if($content)
        {
            $page_content = $content->html;
            return view('frontend', compact('page_content'));
        }
        else
        {
            return "Invalid URL";
        }
    }

    public function show_media(){
        $medias = Intro::orderBy('sl_no')->get();
        return view('admin.media', compact('medias'));
    }

    public function save_media(Request $request){
        try{
            $arr = [];
            if($request->hasFile('image'))
            {
                $arr = $this->upload($request->file('image'));
            }
            if(!empty($arr))
            {
                $sl_no = 1;
                $last_inserted = Intro::orderBy('id', 'desc')->first();
                if($last_inserted)
                {
                    $sl_no = $last_inserted->sl_no + 1;
                }
                $obj = new Intro;
                $obj->sl_no = $sl_no;
                $obj->mime_type = $arr['mime_type'];
                $obj->media = $arr['file_name'];
                $obj->save();
            }
            Session::flash('message', 'Record added successfully');
        }catch(\Exception $e){
            Session::flash('message', $e->getMessage());
        }
        return redirect("/media");
    }

    public function upload($file)
    {
        $returnArr = array(
            'mime_type' => '',
            'file_name' => ''
        );

        $mimeType = $file->getMimeType();
        $destinationPath = 'uploads';
        // dd($destinationPath);
        $uniqueId = Carbon::now()->format('Ymdhis');
        $originalName = $file->getClientOriginalName();
        $name = $uniqueId . '_' . $originalName.'.'.$file->getClientOriginalExtension();
        $file->move($destinationPath, $name);
        
        $returnArr['mime_type'] = $mimeType;
        $returnArr['file_name'] = $name;

        return $returnArr;
    }


    public function del_media($id){
        Intro::where('id', $id)->delete();
        return 1;
    }

    public function reset(Request $request){
        $hash = $request->hash;
        $email = '';
        $id = '';
        $error = true;
        if($hash){
            $id = base64_decode($hash);
            $user = User::find($id);
            if($user){
                $email = $user->email;
                $error = false;
            }
        }
        return view('auth.reset-password', compact('email', 'id', 'error'));
    }

    public function resetPassword(Request $request){
        $user = User::find($request->id);
        $user->password = bcrypt($request->password);
        $user->save();
        Session::flash('message', 'success');
        return redirect('webview/reset-password');
    }
}