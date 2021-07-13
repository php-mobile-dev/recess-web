<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = ['email','password','active','name'];
    public $list = array(
        'id',
        'email',
        'name',
        'purchased',
        'created_at',
        'active'
    );
    public $addList = array(
        'avatar' => 'file',
        'email' => 'email',
        'password' => 'password',
        'name' => 'text',
        'mobile_no' => 'text',
        'address' => 'textarea',
        'bio' => 'text',
        'active' => 'switch',
        'mobile_no_verified' => 'switch',

    );
    public $validation = array(
        'email' => 'required | email | unique:app_users,email',
        'password' => 'required',
        'name' => 'required',
    );
    

    public function getPurchasedOnAttribute() {
        if(!empty($this->attributes['purchased_on']))
            return Carbon::parse($this->attributes['purchased_on'])->format(env('DATE_FORMAT'));
        else
            return '';
    }

    public function getCreatedAtAttribute() {
        if(!empty($this->attributes['created_at']))
            return Carbon::parse($this->attributes['created_at'])->format(env('DATE_FORMAT'));
        else
            return '';
    }

    public function getUpdatedAtAttribute() {
        if(!empty($this->attributes['updated_at']))
            return Carbon::parse($this->attributes['updated_at'])->format(env('DATE_FORMAT'));
        else
            return '';
    }
    public function getPurchasedAttribute($val) {
        if($val == 0)
            return 'Free';
        else
            return 'Purchased';
    }
    public function getAvatarAttribute() {
        if(empty($this->attributes['avatar']))
            return '';
        else
            return asset('/uploads/users').'/'.$this->attributes['avatar'];
    }

    public function scopeAppuser($query)
    {
        return $query->where('type', 'app_user');
    }

    public function posts(){
        return $this->hasMany('App\Models\Post');
    }

    public function friends(){
        return $this->belongsToMany('App\Models\User', 'friends', 'friend_id', 'user_id');
    }

    public function friendRequests(){
        return $this->belongsToMany('App\Models\User', 'friend_requests', 'user_id', 'sender_id');
    }

    public function events(){
        return $this->hasMany('App\Models\Event');
    }

    public function devices(){
        return $this->hasMany('App\Models\Device');
    }

    public function activities(){
        return $this->belongsToMany('App\Models\Activity');
    }

    public function bankDetail(){
        return $this->hasOne('App\Models\BankDetail');
    }
}