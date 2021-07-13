<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\Molder;
use App\Http\Helpers\FileUpload;
use App\Http\Traits\NotificationTrait;
use Validator;
use Log;
use App\Models\Report;

class V2ApiPostController extends Controller
{
    use Molder, FileUpload, NotificationTrait;

    protected function apiValidation($request, $validator)
    {
        $validation = Validator::make($request->all(), $validator);
        if ($validation->fails()) {
            $errors = $validation->errors();
            return $errors->toJson();
        }
        return '';
    }

    public function create_post(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id',
            'type' => 'required | in:FEED,STORY',
            'content_type' => 'required | in:TEXT,VIDEO,IMAGE'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];

        if (!empty($validation_result)) {
            $response['errors'] = $validation_result;
            return $response;
        }

        // Log::info('Param', $request->all());

        if ($request->post_id) {
            $post = Post::updateOrCreate(
                ['id' => $request->post_id],
                [
                    'user_id' => $request->user_id,
                    'type' => $request->type,
                    'post_title' => $request->post_title,
                    'post_text' => $request->post_text,
                    'parent_id' => $request->parent_id,
                    'font_size' => $request->font_size,
                    'background_color' => $request->background_color,
                    'content_type' => $request->content_type
                ]
            );
        } else {
            $post = new Post([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'post_title' => $request->post_title,
                'post_text' => $request->post_text,
                'parent_id' => $request->parent_id,
                'font_size' => $request->font_size,
                'background_color' => $request->background_color,
                'content_type' => $request->content_type
            ]);
        }

        $post->save();
        if ($request->content_type != 'TEXT') {
            $post->allMedia()->delete();
            foreach ($request->file('contents') as $content) {
                $file = $this->upload($content, 'uploads/posts');
                $post_media = new PostMedia([
                    'post_id' => $post->id,
                    'filename' => $file['file_name'],
                    'mime_type' => $file['mime_type'],
                    'duration' => ($request->duration) ? (int) $request->duration : 0
                ]);
                $post_media->save();
            }
        }
        $obj = $this->getPostObj(Post::getPostObj($post->id));
        return [
            'status_flag' => true,
            'message' => '',
            'post' => $obj
        ];
    }

    public function get_posts(Request $request)
    {
        $user_ids = DB::table('friends')->select('friend_id')->where('user_id', $request->user_id)->get()->pluck('friend_id')->toArray();
        array_push($user_ids, $request->user_id);
        $page = ($request->page) ?? 1;
        $per_page = ($request->per_page) ?? 50;
        $offset = ($page - 1) * $per_page;
        $type = ($request->type) ?? 'FEED';

        $params = [
            'user_ids' => $user_ids,
            'present_user_id' => $request->user_id,
            'offset' => $offset,
            'limit' => $per_page,
            'type' => $type
        ];

        $count_query = Post::getPostQuery($request->user_id);
        $total = $count_query->whereIn('posts.user_id', $user_ids)
            ->where('posts.type', $params['type'])->count();

        $posts = Post::feeds($params);
        return [
            'status_flag' => true,
            'message' => '',
            'posts' => $this->getPostCollection($posts),
            'total_posts' => $total
        ];
    }

    public function comment(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id',
            'post_id' => 'required | exists:posts,id',
            'comment' => 'required'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        if (!empty($validation_result)) {
            $response['errors'] = $validation_result;
            return $response;
        }
        $comment = new Comment($request->all());
        $comment->save();
        $post = Post::getPostObj($request->post_id);
        $cc = Comment::WithUser()->where('comments.id', $comment->id)->first();
        $this->sendNotification($request->user_id, [$post->user_id], 'commented', $this->getPostObj($post));
        return [
            'status_flag' => true,
            'message' => 'Comment Added',
            'comment_obj' => $this->getCommentObj($cc)
        ];
    }

    public function like(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id',
            'post_id' => 'required | exists:posts,id',
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        if (!empty($validation_result)) {
            $response['errors'] = $validation_result;
            return $response;
        }
        $post = Post::find($request->post_id);
        if ($post->likes()->where('id', $request->user_id)->exists()) {
            $post->likes()->detach($request->user_id);
        } else {
            $post->likes()->sync($request->user_id);
            $this->sendNotification($request->user_id, [$post->user_id], 'liked', $this->getPostObj(Post::getPostObj($request->post_id)));
        }
        return [
            'status_flag' => true,
            'message' => 'Preference saved'
        ];
    }

    public function delete_comment(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'comment_id' => 'required | exists:comments,id'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];

        Comment::where(function ($query) use ($request) {
            $query->where('id', $request->comment_id)
                ->orWhere('parent_id', $request->comment_id);
        })->delete();

        return [
            'status_flag' => true,
            'message' => 'Comment Removed'
        ];
    }

    public function likes(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'post_id' => 'required | exists:posts,id'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        $post = Post::find($request->post_id);
        $likes = $post->likes()->get();
        return [
            'status_flag' => true,
            'message' => '',
            'users' => $this->getShortUserCollection($likes, [], [])
        ];
    }

    public function comments(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'post_id' => 'required | exists:posts,id'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        $page = ($request->page) ?? 1;
        $limit = ($request->per_page) ?? 100;
        $offset = ($page - 1) * $limit;
        $total = Comment::where('post_id', $request->post_id)->count();
        $comments = Comment::WithUser()->where('post_id', $request->post_id)->whereNull('parent_id')->offset($offset)->limit($limit)->get();
        $parent_comment_ids = $comments->pluck('id')->toArray();
        $child_comments = Comment::WithUser()->whereIn('parent_id', $parent_comment_ids)->get()->groupBy('parent_id');
        $comment_coll = $this->getCommentCollection($comments);
        foreach ($comment_coll as $index => $parent_comment) {
            $comment_coll[$index]['child_count'] = 0;
            if (isset($child_comments[$parent_comment['id']])) {
                $comment_coll[$index]['childs'] = $this->getCommentCollection($child_comments[$parent_comment['id']]);
                $comment_coll[$index]['child_count'] = count($comment_coll[$index]['childs']);
            }
        }
        return [
            'status_flag' => true,
            'message' => '',
            'total' => $total,
            'comments' => $comment_coll
        ];
    }

    public function check_for_story(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        $yesterday = Carbon::now()->subDays(1);
        // $count = Post::where('user_id', $request->user_id)->where('type', 'STORY')->where('created_at','>=', $yesterday)->count();
        $count = 1;
        return [
            'status_flag' => true,
            'can_add_story' => ($count == 0),
            'message' => ($count == 0) ? 'Can Add story now' : 'Can not add a story now'
        ];
    }

    public function report(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id',
            'post_id' => 'required | exists:posts,id',
            'report_reason' => 'required'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        Report::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'report_reason' => $request->report_reason
        ]);
        return [
            'status_flag' => true,
            'message' => 'Sorry to hear that! Your concern has been sent to admin'
        ];
    }

    public function share(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id',
            'parent_post_id' => 'required | exists:posts,id',
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing'
        ];
        $post = Post::find($request->parent_post_id);
        $new_post = $post->replicate();
        $new_post->user_id = $request->user_id;
        $new_post->parent_id = $request->parent_post_id;
        $new_post->created_at = $new_post->updated_at = Carbon::now();
        $new_post->save();
        $medias = PostMedia::where('post_id', $post->id)->get();
        foreach ($medias as $media) {
            $new_media = $media->replicate();
            $new_media->post_id = $new_post->id;
            $new_media->save();
        }
        // $post = Post::select('type', 'post_title', 'post_text', 'font_size', 'background_color', 'content_type')->where('id', $request->parent_post_id)->first();
        // $new_post = new Post();
        // foreach($post as $column => $value){
        //     $new_post[$column] = $value;
        // }
        // $new_post->user_id = $request->user_id;
        // $new_post->parent_id = $request->parent_post_id;
        // $new_post->save();

        $obj = $this->getPostObj(Post::getPostObj($new_post->id));
        return [
            'status_flag' => true,
            'message' => '',
            'post' => $obj
        ];
    }

    public function delete(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'sometimes | required | exists:users,id',
            'post_id' => 'required'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing',
        ];

        if (!empty($validation_result)) {
            $response['errors'] = $validation_result;
            return $response;
        }

        $msg = "Post has been deleted";
        if ($request->post_id == 'all') {
            Post::where('user_id', $request->user_id)->delete();
            $msg = "All posts are deleted";
        } else {
            Post::where('id', $request->post_id)->delete();
        }
        return [
            'status_flag' => true,
            'message' => $msg,
        ];
    }

    public function timeline(Request $request)
    {
        $validation_result = $this->apiValidation($request, [
            'user_id' => 'required | exists:users,id'
        ]);
        $response = [
            'status_flag' => false,
            'message' => 'Parameters missing',
        ];

        if (!empty($validation_result)) {
            $response['errors'] = $validation_result;
            return $response;
        }

        $page = ($request->page) ?? 1;
        $per_page = ($request->per_page) ?? 50;
        $offset = ($page - 1) * $per_page;
        $type = ($request->type) ?? 'FEED';
        $search = ($request->search) ?? '';

        $posts_query = Post::getPostQuery($request->user_id)
            ->where('posts.type', $type)
            ->where(function ($qry) use ($search) {
                return $qry->where('posts.post_title', 'like', "%$search%")
                    ->orWhere('posts.post_text', 'like', "%$search%");
            })
            ->where('posts.user_id', $request->user_id)
            ->orderBy('updated_at', 'desc');
        $total = $posts_query->count();
        $posts = $posts_query->offset($offset)->limit($per_page)->get();
        $posts_arr = $this->getPostCollection($posts);

        return [
            'status_flag' => true,
            'message' => '',
            'posts' => $posts_arr,
            'total' => $total
        ];
    }
}
