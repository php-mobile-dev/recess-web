<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'title', 'value'];
    public $list = array(
        'id',
        'title',
        'value'
    );
    public $addList = array(
        'title' => 'text',
        'value' => 'text'
    );
    public $validation = array(
        'value' => 'required',
        'title' => 'required'
    );
}
