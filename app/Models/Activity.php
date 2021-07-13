<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public $timestamps = false;

    protected $fillable = ['id', 'name', 'description'];
    public $list = array(
        'id',
        'name',
        'active'
    );
    public $addList = array(
        'name' => 'text',
        'description' => 'custom'
    );
    public $validation = array(
        'name' => 'required',
        'description' => 'required'
    );

    public function users(){
        return $this->belongsToMany('App\Models\User');
    }
}