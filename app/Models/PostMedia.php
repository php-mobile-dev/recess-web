<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostMedia extends Model
{
    public $timestamps = false;
    protected $fillable = ['post_id', 'filename', 'mime_type', 'duration'];

    public function getFilenameAttribute($val) {
        return asset('/uploads/posts').'/'.$val;
    }
}