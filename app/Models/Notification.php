<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Notification extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'text', 'unique_key', 'payload'];
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}