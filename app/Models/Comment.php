<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['post_id', 'comment', 'user_id', 'parent_id'];

    public function scopeWithUser($query)
    {
        $query->select('comments.id', 'comments.user_id', 'comments.comment', 'comments.parent_id', 'comments.created_at', 'users.id as user_id', 'users.name as user_name', 'users.avatar', 'users.address', 'users.purchased')
            ->leftJoin('users', 'users.id', '=', 'comments.user_id')
            ->orderBy('created_at', 'asc');
        return $query;
    }
}
