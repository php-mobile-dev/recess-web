<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'type', 'post_title', 'post_text', 'parent_id', 'font_size', 'background_color', 'content_type'];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function likes()
    {
        return $this->belongsToMany('App\Models\User');
    }
    public function comments()
    {
        return $this->belongsToMany('App\Models\User', 'comments', 'user_id', 'post_id');
    }
    public function allMedia()
    {
        return $this->hasMany('App\Models\PostMedia');
    }

    public static function getPostQuery($present_user_id)
    {
        return DB::table('posts')->selectRaw(
            "
            posts.*, 
            users.id as user_id,
            users.name as user_name,
            users.avatar,
            users.address,
            users.purchased,
            (SELECT count(*) FROM post_user where user_id = $present_user_id and post_id = posts.id) as is_liked, 
            (SELECT count(*) FROM post_user where post_id = posts.id) as no_of_likes,
            (SELECT count(*) FROM comments where post_id = posts.id) as no_of_comments"
        )->leftJoin('users', 'users.id', '=', 'posts.user_id')
            ->whereNull('posts.deleted_at');
    }

    public static function getPostObj($post_id)
    {
        $query = self::getPostQuery(0);
        return $query->where('posts.id', $post_id)->first();
    }

    public static function feeds($params)
    {
        $user_ids = $params['user_ids'];
        $present_user_id = isset($params['present_user_id']) ? $params['present_user_id'] : 0;
        $offset = isset($params['offset']) ? $params['offset'] : 0;
        $limit = isset($params['limit']) ? $params['limit'] : 50;
        $type = isset($params['type']) ? $params['type'] : 'FEED';
        $query = self::getPostQuery($present_user_id);
        return $query->whereIn('posts.user_id', $user_ids)
            ->where('posts.type', $type)
            ->orderBy('posts.updated_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
