<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oauth extends Model
{
    public $timestamps = false;
    public $fillable = ['user_id', 'provider', 'token'];
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}