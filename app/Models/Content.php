<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['id', 'slug', 'html'];
    public $list = array(
        'id',
        'slug'
    );
    public $addList = array(
        'slug' => 'text',
        'html' => 'custom'
    );
    public $validation = array(
        'slug' => 'required',
        'html' => 'required'
    );
}
