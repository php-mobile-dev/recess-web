<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppUser;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\FileUpload;

class BaseController extends Controller
{

    use FileUpload;
    private $model = '';
    private $pluralName = '';
    private $modelName = '';

    
    public function __construct()
    {
        // $this->middleware('auth')->except(['del']);
    } 
    
    
    public function setModel($model){
        $this->model = 'App\\Models\\'.$model;
        if($model == 'AppUser'){
            $this->pluralName = 'users';
            $this->modelName = 'user';
        }
        else{
            $this->pluralName = $model.'s';
            $this->modelName = $model;
        }
    }

    public function index()
    {
        $obj = new $this->model;
        $list_column = $obj->list;
        $data_string = [];
        foreach($list_column as $key => $col)
        {
            if(stripos($col, ' as ')){
                $col = explode(' as ', $col)[1];
                $list_column[$key] = $col;
            }
            $data_string[] = array('data' => trim($col));
        }
        array_push($data_string, array('data' => 'Action'));
        $data_string = json_encode($data_string);
        $heading = ucfirst(preg_replace('/(?|([A-Z])([A-Z][a-z])|([a-z])([A-Z]))/', '${1} ${2}', $this->modelName));
        $model = strtolower($this->modelName);
        return view("admin.common.list", compact('data_string', 'list_column', 'heading', 'model'));
    }

    
    public function create()
    {
        $obj = new $this->model;
        $fields = $obj->addList;
        $heading = strtolower(preg_replace('/(?|([A-Z])([A-Z][a-z])|([a-z])([A-Z]))/', '${1} ${2}', $this->modelName));;
        return view('admin.common.add', compact('fields', 'heading'));
    }

    
    public function listing(Request $request)
    {
        $obj = new $this->model;
        $table = $obj->getTable();
        $list_column = $obj->list;
        $model = strtolower($this->modelName);
        array_walk($list_column, function (&$item)
        {
            $item = DB::raw($item);
        });
        $query = $this->model::select($list_column);
        if($this->model == 'App\Models\User')
            $query = $query->Appuser();
        if($obj->translation){
            
            if($model == 'dailyquote'){
                $bridge_table = 'daily_quote_translations';
                $query->leftJoin($bridge_table, "$table.id", '=', $bridge_table.'.daily_quote_id');

            }else{
                $bridge_table = $model.'_translations';
                $query->leftJoin($bridge_table, "$table.id", '=', $bridge_table.'.'.$model.'_id');
            }

            $query->where("$bridge_table.locale", '=', session('current_locale'));
        }
        if($table == 'rules'){
            $query->orderBy("$table.sl_no");
        }else{
            $query->orderBy("$table.id",'desc');
        }
        
        $results = $query->get();
        
        $result = [];
        foreach($results as $r)
        {

            if(in_array('active', $list_column)){
                if($r->active)
                    $r->active = '<a href="'.url('/admin/change/'.$model.'/'.$r->id).'"><small class="label bg-green">Active</small></a>';
                else
                    $r->active = '<a href="'.url('/admin/change/'.$model.'/'.$r->id).'"><small class="label bg-red">Inactive</small></a>';
            }

            if(in_array('purchase_status', $list_column)){
                if($r->purchase_status == 1)
                    $r->purchase_status = '<small class="label bg-green">Pro</small>';
                else
                    $r->purchase_status = '<small class="label bg-blue">Free</small>';
            }

            $r['Action'] = '<a class="btn btn-info btn-sm" href="'.url('/admin/'.$model.'/'.$r->id.'/edit').'"><i class="fa fa-pencil"></i></a>';
            if(in_array($this->model, ['App\Models\User'])){
                $r['Action'] .= '<a class="btn btn-primary btn-sm" href="'.url('/admin/user/'.$r->id).'"><i class="fa fa-eye"></i></a>';
            }
                
            array_push($result, $r);
        }
        return array('data' => $result);
    }


    public function store(Request $request)
    {
        $obj = new $this->model;
        $request->validate($obj->validation);
        foreach($obj->addList as $field => $type){
            $obj->{$field} = $request->{$field};
        }
        if($this->modelName == 'Content' || $this->modelName == 'Setting'){
            $obj->name = $request->name;
        }
        $obj->save();
        $return = strtolower($this->modelName);
        Session::flash('message', 'Record added successfully');
        return redirect("/admin/$return");
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
        $obj = $this->model::find($id);
        $heading = strtolower(preg_replace('/(?|([A-Z])([A-Z][a-z])|([a-z])([A-Z]))/', '${1} ${2}', $this->modelName));
        $fields = $obj->addList;
        return view('admin.common.edit', compact('fields','obj','heading'));
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
        $obj = $this->model::find($id);
        if($obj)
        {
            $request->validate($obj->validation);
            foreach($obj->addList as $field => $type){
                $obj->{$field} = $request->{$field};
            }
            $obj->save();
            Session::flash('message', 'Record updated successfully');
        }
        else{
            Session::flash('message', 'Invalid ID');
        }
        $return = strtolower($this->modelName);
        return redirect("/admin/$return");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function del(Request $request)
    {
        $ids = $request->id;
        if(!empty($ids))
        {
            foreach($ids as $id){
                $obj = $this->model::find($id['value']);
                switch($this->modelName)
                {
                    case 'user' :
                        $obj->deals()->delete();
                        $obj->device()->delete();
                        break;
                    case 'category' :
                        $obj->words()->detach();
                        $obj->words()->pluck('word_id');
                        break;
                }
                $obj->delete();                 
            }
        }
        return ['response' => true];
    }



    public function change ($id){
        $obj = $this->model::find($id);
        if($obj)
        {
            $obj->active = $obj->active==1 ? 0 : 1;
            $obj->save();
        }
        Session::flash('message', 'Status Changed'); 
        return redirect(url()->previous());
    }


    public function upload($file, $destinationPath='uploads')
    {
        $returnArr = array(
            'mime_type' => '',
            'file_name' => ''
        );

        $mimeType = $file->getMimeType();
        
        // dd($destinationPath);
        $uniqueId = Carbon::now()->format('Ymdhis');
        $originalName = $file->getClientOriginalName();
        $name = $uniqueId . '_' . $originalName.'.'.$file->getClientOriginalExtension();
        $file->move($destinationPath, $name);
        
        $returnArr['mime_type'] = $mimeType;
        $returnArr['file_name'] = $name;
        return $returnArr;
    }
}