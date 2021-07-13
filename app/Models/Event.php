<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	protected $fillable = ['id', 'name', 'starts_on', 'ends_on', 'address', 'fees', 'status', 'description', 'winnings'];
	public $list = array('id', 'name', 'posted_by', 'starts_on', 'ends_on', 'address', 'status');
	public $addList = array(
		'name' => 'text',
		'address' => 'textarea',
		'fees' => 'text',
		'description' => 'custom',
		'winnings' => 'text',
		'starts_on' => 'date',
		'ends_on' => 'date',
		'frequency' => 'text',
		'fees' => 'text',
		'days' => 'text',
	);
	public $validation = array(
		'name' => 'required',
		'description' => 'required',
	);

	public function getCreatedAtAttribute()
	{
		if (!empty($this->attributes['created_at'])) {
			return Carbon::parse($this->attributes['created_at'])->format(env('DATE_FORMAT'));
		} else {
			return '';
		}
	}
	// public function getStartsOnAttribute($val) {
	//     if(!empty($val))
	//         return Carbon::parse($val)->format(env('DATE_FORMAT'));
	//     else
	//         return '';
	// }
	// public function getEndsOnAttribute($val) {
	//     if(!empty($val))
	//         return Carbon::parse($val)->format(env('DATE_FORMAT'));
	//     else
	//         return '';
	// }

	public function getDaysAttribute($val)
	{
		return @json_decode($val, true);
	}

	public function getStatusAttribute($val)
	{
		return ucfirst($val);
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function participants()
	{
		return $this->belongsToMany('App\Models\User')->withTimestamps();
	}

	public function invitations()
	{
		return $this->belongsToMany('App\Models\User', 'event_invitations', 'event_id', 'user_id');
	}

	public function getNextEventDateAttribute($joined_for)
	{
		if ($this->attributes['activity_category_id'] != 2 && $this->attributes['activity_category_id'] != 1) {
			return null;
		}
		if ($this->attributes['activity_category_id'] == 1 && is_null($this->attributes['frequency'])) {
			$this->attributes['frequency'] = 'Daily';
		}

		$starts_on = Carbon::parse($this->attributes['starts_on']);
		$weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday','sunday'];
		$current_date = Carbon::now();
		$today = strtolower($current_date->format('l'));
		$event_days = @json_decode($this->attributes['days'], true);
		usort($event_days, function ($a, $b) use ($weekdays) {
			return array_search($a, $weekdays) - array_search($b, $weekdays);
		});
		if (strtolower($this->attributes['frequency']) == 'daily') {
			$event_time_today = Carbon::create($current_date->year, $current_date->month, $current_date->day, $starts_on->hour, $starts_on->minute, 0, 0);
			if (Carbon::now() > $event_time_today) {
				$return_date = $event_time_today->addDays(1);
			} else {
				$return_date = $event_time_today;
			}
		} else if (strtolower($this->attributes['frequency']) == 'weekly') {
			$return_date = new Carbon('next ' . $event_days[0]);
			$return_date = $return_date->addHours($starts_on->hour)->addMinutes($starts_on->minute);
			if ($current_date->weekOfYear > Carbon::parse($joined_for)->weekOfYear) {
				$event_time_this_week = new Carbon('this ' . $event_days[0]);
				$event_time_this_week = $event_time_this_week->addHours($starts_on->hour)->addMinutes($starts_on->minute);
				if (Carbon::now() < $event_time_this_week) {
					$return_date = $event_time_this_week;
				}
			} else {
				$return_date = $return_date->addDays(7);
			}
		} else if (strtolower($this->attributes['frequency']) == 'monthly') {
			$return_date = new Carbon('first '.ucfirst($event_days[0]).' of next month');
			$return_date = $return_date->addHours($starts_on->hour)->addMinutes($starts_on->minute);
			$event_time_this_week = new Carbon('first '.ucfirst($event_days[0]).' of this month');
			$event_time_this_week = $event_time_this_week->addHours($starts_on->hour)->addMinutes($starts_on->minute);
			if (Carbon::parse($joined_for)->month < Carbon::now()->month) {
				if (Carbon::now() < $event_time_this_week) {
					$return_date = $event_time_this_week;
				}
			} else {
				$return_date = $return_date->addDays(30);
			}
		}
		if ($return_date > Carbon::parse($this->attributes['ends_on'])) {
			$return_date = null;
		}
		return $return_date;
	}
}
