<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityCategory;
use App\Models\ActivityType;
use App\Models\Activity;
use App\Models\PostMedia;
use App\Models\Post;
use App\Models\User;
use App\Models\Event;

trait Molder
{
    function getUserObj($obj)
    {
        return [
            'id' => $obj->id,
            'name' => $obj->name,
            'email' => $obj->email,
            'purchased' => $obj->purchased,
            'purchased_on' => $obj->purchased_on ?? '',
            'purchase_token' => $obj->purchase_token ?? '',
            'country_code' => ($obj->country_code) ?? '',
            'mobile_no' => ($obj->mobile_no) ?? '',
            'mobile_no_verified' => ($obj->mobile_no_verified) ?? 0,
            'avatar' => $obj->avatar,
            'bio' => ($obj->bio) ?? '',
            'address' => ($obj->address) ?? '',
            'longitude' => ($obj->longitude) ? (float) $obj->longitude :  0.0,
            'latitude' => ($obj->latitude) ? (float) $obj->latitude : 0.0,
            'search_radius' => ($obj->search_radius) ? (int) $obj->search_radius :  0,
            'selected_activity_ids' => $obj->activities()->get()->toArray(),
            'bank_details' => $obj->bankDetail()->first(),
            'event_count' => Event::where('user_id', $obj->id)->count(),
            'notifications' => [],
        ];
    }


    function getUserCollection($cols)
    {
        $userArr = [];
        foreach ($cols as $col) {
            array_push($userArr, $this->getUserObj($col));
        }
        return $userArr;
    }

    function getEventObj($obj)
    {
        $user = $obj->user()->first();
        if ($obj->activity_category_id == 2 && (Carbon::now() > Carbon::parse($obj->starts_on))) {
            $user_ids = [];
            switch ($obj->frequency) {
                case 'Daily':
                    $date_of_event = Carbon::today();
                    if (Carbon::now() > Carbon::parse($obj->starts_on)) {
                        $date_of_event = Carbon::tomorrow();
                    }
                    $user_ids = DB::table('event_user')
                        ->where('event_id', $obj->id)
                        ->whereRaw('DATE(joined_for) = ' . "'" . $date_of_event->format('Y-m-d') . "'")
                        ->get();
                    break;
                case 'Weekly':
                    // dd($this->getNextEventDate($obj)->format('Y') . '-' . $this->getNextEventDate($obj)->weekOfYear);
                    $user_ids = DB::table('event_user')
                        ->selectRaw('event_user.*, CONCAT(YEAR(joined_for), \'-\', WEEKOFYEAR(joined_for)) AS tt')
                        ->where('event_id', $obj->id)
                        ->whereRaw('CONCAT(YEAR(joined_for),\'-\',WEEKOFYEAR(joined_for)) = ' . "'" . $this->getNextEventDate($obj)->format('Y') . '-' . $this->getNextEventDate($obj)->weekOfYear . "'")
                        ->get();
                    break;
                case 'Monthly':
                    $user_ids = DB::table('event_user')
                        ->where('event_id', $obj->id)
                        ->whereRaw('CONCAT(MONTH(joined_for), \'-\',YEAR(joined_for) ) = ' . "'" . ltrim($this->getNextEventDate($obj)->format('m-Y'), '0') . "'")
                        ->get();
                    break;
            }
            $joined = User::whereIn('id', $user_ids->pluck('user_id')->toArray())->get();
            foreach ($joined as $joinee) {
                $joinee->joined_on = $user_ids->where('user_id', $joinee->id)->first()->created_at;
            }
        } else {
            $joined = $obj->participants()->get();
        }
        $joined_users = [];
        foreach ($joined as $joinee) {
            array_push($joined_users, [
                'id' => $joinee->id,
                'name' => $joinee->name,
                'avatar' => ($joinee->avatar) ?? '',
                'address' => ($joinee->address) ?? '',
                'latitude' => ($joinee->latitude) ?? 0.0,
                'longitude' => ($joinee->longitude) ?? 0.0,
                'joined_on' => ($joinee['pivot']) ? $joinee['pivot']->created_at : $joinee->joined_on,
                'purchased' => ($joinee->purchased == 'Purchased' || $joinee->purchased == 1) ? 'Purchased' : 'Free'
            ]);
        }
        return [
            'id' => $obj->id,
            'status' => $obj->status,
            'activity_category' => ActivityCategory::find($obj->activity_category_id),
            'activity_type' => ActivityType::find($obj->activity_type_id),
            'activity' => Activity::find($obj->activity_id),
            'name' => $obj->name,
            'start_time' => $obj->start_time,
            'end_time' => $obj->end_time,
            'starts_on' => $obj->getOriginal('starts_on'),
            'ends_on' => $obj->getOriginal('ends_on'),
            'address' => $obj->address,
            'longitude' => (float) $obj->longitude,
            'latitude' => (float) $obj->latitude,
            'no_of_participants' => is_null($obj->no_of_participants) ? 0 : (int) $obj->no_of_participants,
            'description' => is_null($obj->description) ? '' : $obj->description,
            'fees' => is_null($obj->fees) ? 0.0 : (float) $obj->fees,
            'frequency' => is_null($obj->frequency) ? '' : $obj->frequency,
            'days' => $obj->days,
            'trainer_experience' => $obj->trainer_experience,
            'winnings' => is_null($obj->winnings) ? 0.0 : (float) $obj->winnings,
            'created_at' => $obj->created_at,
            'created_by' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => ($user->avatar) ?? '',
                'address' => ($user->address) ?? '',
                'latitude' => ($user->latitude) ?? 0.0,
                'longitude' => ($user->longitude) ?? 0.0,
                'purchased' => ($user->purchased == 'Purchased' || $user->purchased == 1) ? 'Purchased' : 'Free'
            ],
            'already_joined' => $joined_users
        ];
    }

    function getNextEventDate($obj)
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $current_date = Carbon::now();
        $today = strtolower($current_date->format('l'));
        $event_days = $obj->days;
        $starts_on = Carbon::parse($obj->starts_on);
        usort($event_days, function ($a, $b) use ($weekdays) {
            return array_search($a, $weekdays) - array_search($b, $weekdays);
        });
        if (array_search($today, $weekdays) > array_search(end($event_days), $weekdays)) {
            return new Carbon('next ' . $event_days[0]);
        } else if (array_search($today, $weekdays) < array_search(end($event_days), $weekdays)) {
            return Carbon::now();
        } else {
            if (Carbon::now() >= Carbon::today()->addHours($starts_on->hour)->addMinutes($starts_on->minute)) {
                return new Carbon('next ' . $event_days[0]);
            } else {
                return Carbon::now();
            }
        }
    }
    function getEventCollection($cols)
    {
        $event_arr = [];
        foreach ($cols as $col) {
            array_push($event_arr, $this->getEventObj($col));
        }
        return $event_arr;
    }


    function getShortUserCollection($joined, $already_friends_id = [], $already_requested_friends_id = [])
    {
        $joined_users = [];
        foreach ($joined as $joinee) {
            $avatar = ($joinee->avatar) ?? '';
            array_push($joined_users, [
                'id' => $joinee->id,
                'name' => $joinee->name,
                'avatar' => $avatar,
                'address' => ($joinee->address) ?? '',
                'latitude' => ($joinee->latitude) ?? 0.0,
                'longitude' => ($joinee->longitude) ?? 0.0,
                'is_requested' => in_array($joinee->id, $already_requested_friends_id) ? true : false,
                'mutual_friends' => $joinee->friends()->whereIn('id', $already_friends_id)->count(),
                'purchased' => ($joinee->purchased == 'Purchased' || $joinee->purchased == 1) ? 'Purchased' : 'Free'
            ]);
        }
        return $joined_users;
    }

    function getPostObj($obj)
    {
        if ($obj) {
            $media = [];
            if (isset($obj->content_type) && $obj->content_type != 'TEXT')
                $media = PostMedia::select('filename', 'mime_type', 'duration')->where('post_id', $obj->id)->get()->toArray();

            return [
                'id' => $obj->id,
                'type' => $obj->type,
                'post_title' => $obj->post_title,
                'post_text' => $obj->post_text,
                'parent_id' => ($obj->parent_id) ?? 0,
                'font_size' => ($obj->font_size) ?? 0,
                'background_color' => ($obj->background_color) ?? '',
                'content_type' => $obj->content_type,
                'is_liked' => empty($obj->is_liked) ? false : true,
                'no_of_likes' => $obj->no_of_likes,
                'no_of_comments' => $obj->no_of_comments,
                'created_at' => $obj->created_at,
                'updated_at' => $obj->updated_at,
                'contents' => $media,
                'author' => [
                    'id' => $obj->user_id,
                    'name' => $obj->user_name,
                    'avatar' => !is_null($obj->avatar) ? asset('/uploads/users') . '/' . $obj->avatar : '',
                    'address' => ($obj->address) ?? '',
                    'purchased' => ($obj->purchased === 'Purchased' || $obj->purchased === 1) ? 'Purchased' : 'Free',
                    'latitude' =>  0.0,
                    'longitude' => 0.0,
                    'is_requested' => false,
                    'mutual_friends' => 0
                ],
                'parent_post' => ($obj->parent_id) ? $this->getPostObj(Post::getPostQuery($obj->user_id)->where('posts.id', $obj->parent_id)->first()) : new \stdClass()
            ];
        } else {
            return;
        }
    }

    function getPostCollection($cols)
    {
        $post_arr = [];
        foreach ($cols as $col) {
            array_push($post_arr, $this->getPostObj($col));
        }
        return $post_arr;
    }

    function getCommentObj($col)
    {
        return [
            'id' => $col->id,
            'parent_id' => ($col->parent_id) ?? 0,
            'created_at' => $col->created_at,
            'comment' => $col->comment,
            'author' => [
                'id' => $col->user_id,
                'name' => $col->user_name,
                'avatar' => !is_null($col->avatar) ? asset('/uploads/users') . '/' . $col->avatar : '',
                'purchased' => ($col->purchased == 'Purchased' || $col->purchased == 1) ? 'Purchased' : 'Free',
                'address' => ($col->address) ?? '',
                'is_requested' => false,
                'mutual_friends' => 0
            ],
            'childs' => [],
            'child_count' => 0
        ];
    }

    function getCommentCollection($cols)
    {
        $comments_arr = [];
        foreach ($cols as $col) {
            array_push($comments_arr, $this->getCommentObj($col));
        }
        return $comments_arr;
    }
}
