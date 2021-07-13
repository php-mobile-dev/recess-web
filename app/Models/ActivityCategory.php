<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'name'];
    public $list = array(
        'id',
        'name',
        'active'
    );
    public $addList = array(
        'name' => 'text',
    );
    public $validation = array(
        'slug' => 'required',
    );
}
