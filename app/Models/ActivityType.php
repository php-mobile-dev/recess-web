<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    protected $fillable = ['id', 'name'];
    public $list = array(
        'id',
        'name',
        'active'
    );
    public $addList = array(
        'name' => 'text'
    );
    public $validation = array(
        'name' => 'required'
    );
}
