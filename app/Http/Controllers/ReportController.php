<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\Post;
class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct(){
        $this->middleware('auth');
        parent::setModel('Report');
    }

    public function listing(Request $request)
    {
        $data = Report::select('report_post.id as id', 'report_post.post_id',  'report_post.status', 'report_post.report_reason', 'status', 'admin_action', 'users.name as reported_by', 'poster.name as reported_against' ,'report_post.created_at')
                ->leftJoin('users', 'users.id', '=', 'report_post.user_id')
                ->leftJoin('posts', 'posts.id', '=', 'report_post.post_id')
                ->leftJoin('users as poster', 'poster.id', '=', 'posts.user_id')
                ->orderBy('report_post.id', 'desc')
                ->get();
        foreach($data as $r){
            $r['Action'] = '<a class="btn btn-info btn-sm" href="'.url('/admin/report/'.$r->id).'"><i class="fa fa-eye"></i></a>';
            if($r['status'] == 'Pending'){
                $r['Action'] .= '<a class="btn btn-success btn-sm" data-toggle="tooltip" title="Overrule Report" href="'.url('/admin/change/report/'.$r->id.'?action=pass').'"><i class="fa fa-check"></i></a>';
                $r['Action'] .= '<a class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete Post" href="'.url('/admin/change/report/'.$r->id.'?action=delete').'"><i class="fa fa-close"></i></a>';
            }
        }
        return ['data' => $data];
    }

    public function changeStatus (Request $request, $id){
        $obj = Report::find($id);
        $action = $request->action;
        if($action == 'pass'){
            $obj->admin_action = 'Report Overruled';
        }else{
            $obj->admin_action = 'Post Deleted';
            Post::where('id', $obj->post_id)->delete();
        }
        $obj->status = 'resolved';
        $obj->save();
        Session::flash('message', 'Selected action performed'); 
        return redirect(url()->previous());
    }


    public function show ($id){
        $report = Report::find($id);
        $post = $report->post()->withTrashed()->first();
        $post['media'] = $post->allMedia()->get();
        $user = $post->user()->first();
        $user->no_of_friends = $user->friends()->count();
        $user->no_of_posts = $user->posts()->withTrashed()->count();
        $user->no_of_feeds = $user->events()->count();
        $data = [
            'report' => $report,
            'post' => array($post),
            'user' => $user,
            'media' => $post['media']
        ];
        return view('admin.post.view', compact('data'));
    }
    
}