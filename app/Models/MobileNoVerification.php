<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileNoVerification extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'code', 'mobile_no'];
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}