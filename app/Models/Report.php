<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Report extends Model
{
    protected $table = 'report_post';
    protected $fillable = ['post_id', 'user_id', 'report_reason'];
    public $list = array(
        'id',
        'post_id',
        'reported_by',
        'reported_against',
        'report_reason',
        'status',
        'admin_action',
        'created_at'
    );
    public function getCreatedAtAttribute() {
        if(!empty($this->attributes['created_at']))
            return Carbon::parse($this->attributes['created_at'])->format(env('DATE_FORMAT'));
        else
            return '';
    }
    public function getStatusAttribute($val) {
        return ucfirst($val);
    }

    public function post(){
        return $this->belongsTo('App\Models\Post');
    }
}