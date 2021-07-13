<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    public $timestamps = false;
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}