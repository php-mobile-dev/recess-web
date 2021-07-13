<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\FileUpload;
use App\Http\Helpers\GeoLocation;
use App\Http\Traits\Molder;
use App\Http\Traits\NotificationTrait;
use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\ActivityType;
use App\Models\Content;
use App\Models\Device;
use App\Models\Event;
use App\Models\FriendRequest;
use App\Models\MobileNoVerification;
use App\Models\Notification;
use App\Models\Oauth;
use App\Models\Setting;
use App\Models\User;
use App\Models\BankDetail;
use App\Models\BankTransaction;
use App\Http\Helpers\StripeHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mail;
use Validator;

class V1ApiController extends Controller
{
	use Molder, FileUpload, NotificationTrait;

	/**
	 * Function to send the list of config values
	 */
	public function config()
	{
		$settings = Setting::select('name', 'value')->get();
		foreach ($settings as $setting) {
			$response[$setting->name] = $setting->value;
		}
		$activity_categories = ActivityCategory::all()->toArray();
		$activity_types = ActivityType::all()->toArray();
		$activities = Activity::all()->toArray();
		$contents = Content::select('name', 'html')->get();
		$urls = [
			'privacy_policy' => $contents->where('name', 'PRIVACY_POLICY')->first()->html,
			'terms_of_service' => $contents->where('name', 'TERMS_OF_SERVICE')->first()->html,
			'about_us' => $contents->where('name', 'ABOUT_US')->first()->html,
			'faq' => url('/webview/faq'),
		];

		return [
			'status_flag' => true,
			'settings' => $response,
			'activity_categories' => $activity_categories,
			'activity_types' => $activity_types,
			'activities' => $activities,
			'urls' => $urls,
			'message' => 'Success',
		];
	}

	public function login(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'device_token' => 'required',
			'device_type' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'status_code' => 403,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$user = null;
		try {
			$user = User::AppUser()->where('email', $request->email)->where('active', 1)->first();
			if ($request->token) {
				$oauth = Oauth::where('provider', $request->provider)->where('token', $request->token)->first();
				if ((Oauth::where('user_id', $user->id)->count() > 0) && $oauth) {
					$user = User::AppUser()->where('active', 1)->where('id', $oauth->user_id)->first();
				}
			}

			$creds_check = false;
			if ($user) {
				$creds_check = true;
				if (empty($request->token) && !Hash::check($request->password, $user->password)) {
					$response['message'] = 'Invalid Password';
					$creds_check = false;
				}
				if ($creds_check) {
					$user->devices()->where('device_type', $request->device_type)->delete();
					$device = new Device;
					$device->device_token = $request->device_token;
					$device->device_type = $request->device_type;
					$device->user_id = $user->id;
					$device->save();
					return [
						'status_flag' => true,
						'status_code' => 200,
						'message' => 'Login successful',
						'user_details' => $this->getUserObj($user),
					];
				}
			} else {
				$response['message'] = ($request->token) ? 'Provider Token Mismatched!' : 'This Email is not registered with us';
				$response['status_code'] = ($request->token) ? 600 : 403;
			}
		} catch (\Exception $e) {
			$response['message'] = $e->getTraceAsString();
		}
		return $response;
	}

	protected function apiValidation($request, $validator)
	{
		$validation = Validator::make($request->all(), $validator);
		if ($validation->fails()) {
			$errors = $validation->errors();
			return $errors->toJson();
		}
		return '';
	}

	public function signup(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'email' => 'required | email',
			'name' => 'required',
			'device_token' => 'required',
			'device_type' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
			'status_code' => 403,
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		try {
			if ($request->user_id) {
				$user = User::find($request->user_id);
				$user->devices()->where('device_type', $request->device_type)->delete();
			} else if (User::where('email', $request->email)->Appuser()->count() > 0) {
				$response['message'] = "This Email is already taken";
				$response['status_code'] = 600;
				return $response;
			} else {
				$user = new User;
			}
			$email = $user->email = $request->email;
			$user->name = $request->name;
			if (!empty($request->password)) {
				$user->password = bcrypt($request->password);
			}

			$user->save();

			$device = new Device;
			$device->user_id = $user->id;
			$device->device_token = $request->device_token;
			$device->device_type = $request->device_type;
			$device->save();

			$response = StripeHelper::createCustomer([
				'description' => $user->id,
				"email" => $user->email,
				"name" => $user->name
			]);
			$user->stripe_id = $response->id;
			$user->save();

			if (!empty($request->provider) && !empty($request->token)) {
				Oauth::where('user_id', $user->id)->where('provider', $request->provider)->delete();
				$oauth = new Oauth([
					'user_id' => $user->id,
					'provider' => $request->provider,
					'token' => $request->token,
				]);
				$oauth->save();
			}

			return [
				'status_flag' => true,
				'message' => 'Signup successful',
				'status_code' => 200,
				'user_details' => $this->getUserObj($user),
			];
		} catch (\Exception $e) {
			$response['error'] = $e->getMessage();
			$response['message'] = $e->getTraceAsString();
		}
		return $response;
	}

	public function verifyMobile(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'mobile_no' => 'required',
			'country_code' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		try {
			$verification = MobileNoVerification::where('user_id', $request->user_id)->where('mobile_no', '+' . $request->country_code . $request->mobile_no)->first();
			if (is_null($verification)) {
				$verification = new MobileNoVerification([
					'user_id' => $request->user_id,
					'mobile_no' => '+' . $request->country_code . $request->mobile_no,
					'code' => rand(pow(10, 3), pow(10, 4) - 1),
				]);
				$verification->save();
			}
			// SendSMS::sendMessage($verification->code." is your One Time Password (OTP) for your Recess Account.", $verification->mobile_no);

			return [
				'status_flag' => true,
				'message' => "One time password is sent to you no",
				'otp' => (int) $verification->code,
			];
		} catch (\Exception $e) {
			$response['message'] = $e->getMessage();
			$response['error'] = $e->getTraceAsString();
		}
		return $response;
	}

	public function validateOtp(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'mobile_no' => 'required',
			'country_code' => 'required',
			'otp' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		try {
			$verification = MobileNoVerification::where('user_id', $request->user_id)
				->where('mobile_no', '+' . $request->country_code . $request->mobile_no)
				->where('code', $request->otp)
				->first();
			if ($verification) {
				$user = User::find($request->user_id);
				$user->country_code = $request->country_code;
				$user->mobile_no = $request->mobile_no;
				$user->mobile_no_verified = 1;
				$user->save();
				$verification->delete();
				return [
					'status_flag' => true,
					'message' => "Mobile No verified",
				];
			}
			$response['message'] = 'Invalid OTP';
			return $response;
		} catch (\Exception $e) {
			$response['message'] = $e->getTraceAsString();
		}
	}

	public function addPreference(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'search_radius' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		try {
			$activity_ids = ($request->activity_ids) ?? [];
			$user = User::find($request->user_id);
			$user->search_radius = $request->search_radius;
			$user->latitude = $request->latitude;
			$user->address = $request->address;
			$user->longitude = $request->longitude;
			$user->save();
			$user->activities()->sync($activity_ids);
			return [
				'status_flag' => true,
				'message' => "Preference Saved",
				'user_details' => $this->getUserObj($user),
			];
		} catch (\Exception $e) {
			$response['message'] = $e->getMessage();
			$response['error'] = $e->getTraceAsString();
		}
		return $response;
	}

	public function reset_password(Request $request)
	{
		if (empty($request->email)) {
			return [
				'status_flag' => false,
				'message' => 'Email needed',
			];
		}
		$user = User::where('email', $request->email)->first();
		if ($user) {
			$email = $request->email;
			$hash = base64_encode($user->id);
			$reset_url = url('/webview/reset-password') . '?hash=' . $hash;
			Mail::send('mail_template', ['url' => $reset_url], function ($message) use ($email) {
				$message->to($email, 'Admin')->subject('Reset Password');
			});
			return [
				'status_flag' => true,
				'message' => "A link to reset your password has been sent to your email ID",
			];
		} else {
			return [
				'status_flag' => false,
				'message' => 'Sorry! This email id is not registered with us. Please try with valid email',
			];
		}
	}

	public function add_event(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'activity_category_id' => 'required',
			'activity_type_id' => 'required',
			'activity_id' => 'required',
			'name' => 'required',
			'starts_on' => 'required',
			'ends_on' => 'required',
			'address' => 'required',
			'longitude' => 'required',
			'longitude' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		try {
			if ($request->id) {
				$event = Event::find($request->id);
			} else {
				$event = new Event;
			}
			foreach ($request->except('invites') as $key => $value) {
				if ($key == 'days' && !is_null($value)) {
					$value = @json_encode($value);
				} else if ($key == 'starts_on' || $key == 'ends_on') {
					$value = Carbon::parse($value);
				}
				$event[$key] = $value;
			}
			$event->save();
			if (!empty($request->invites)) {
				$event->invitations()->sync($request->invites);
				$this->sendNotification($request->user_id, $request->invites, 'invited_to_activity', $this->getEventObj($event));
			}
			return [
				'status_flag' => true,
				'message' => 'Activity Posted',
				'event' => $this->getEventObj($event),
			];
		} catch (\Exception $e) {
			$response['error'] = $e->getMessage();
			$response['message'] = $e->getTraceAsString();
		}
		return $response;
	}

	public function get_events(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'type' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$latitude = $request->latitude;
		$longitude = $request->longitude;
		$user = User::find($request->user_id);
		if (is_null($latitude) || is_null($longitude)) {
			$latitude = $user->latitude;
			$longitude = $user->longitude;
		}
		$search_radius = 1000000;
		if (!is_null($request['range'])) {
			$search_radius = (int) $request['range'];
		}
		$query = Event::select("*", DB::raw(GeoLocation::getLatLongQuery($latitude, $longitude)))
			->where('ends_on', '>=', Carbon::now())
			->having('distance', '<=', $search_radius)
			->orderBy('distance', 'asc');

		if ($request->type == 'upcoming') {
			$joined_events = DB::table('event_user')->where('user_id', $request->user_id)->get()->pluck('event_id')->toArray();
			$query->where(function ($qry) use ($joined_events, $request) {
				$qry->where('user_id', $request->user_id)
					->orWhereIn('id', $joined_events);
			});
		} else {
			// $query->where('user_id', '<>', $request->user_id);
		}
		$events = $query->get();
		if ($request->type != 'upcoming') {
			foreach ($events as $event_key => $event) {
				if ($event->activity_type_id == 2) {
					if (DB::table('event_invitations')->where('event_id', $event->id)->where('user_id', $request->user_id)->count() == 0)
						$events->forget($event_key);
				}
			}
		}
		return [
			'status_flag' => true,
			'events' => $this->getEventCollection($events),
			'message' => '',
		];
	}

	public function friends(Request $request)
	{
		$page = ($request->page) ?? 1;
		$per_page = ($request->per_page) ?? 50;
		$offset = ($page - 1) * $per_page;

		// $query = DB::table('friends')
		//             ->select('users.id', 'users.avatar', 'users.email', 'users.name')
		//             ->leftJoin('users', 'users.id', '=', 'friends.friend_id')
		//             ->where('friends.user_id', $request->user_id);

		$query = User::select('users.id', 'users.avatar', 'users.email', 'users.name', 'users.latitude', 'users.longitude', 'users.purchased')
			->whereRaw('id in (SELECT friends.friend_id from friends where friends.user_id = ' . $request->user_id . ')');

		$total = $query->count();

		if ($request->search) {
			$query->where('users.name', 'like', "%$request->search%");
		}
		$already_friends_id = DB::table('friends')->select('friend_id')->where('user_id', $request->user_id)->get()->pluck('friend_id')->toArray();

		$friends = $this->getShortUserCollection(
			$query->limit($per_page)->offset($offset)->get(),
			$already_friends_id,
			[]
		);
		return [
			'status_flag' => true,
			'total_friends' => $total,
			'friends' => $friends,
			'message' => '',
		];
	}

	public function suggested_friends(Request $request)
	{
		$friends = collect();
		$already_friends_id = [];
		$already_requested_friends_id = [];
		if ($request->user_id) {
			$user = User::find($request->user_id);
			$latitude = $user->latitude;
			$longitude = $user->longitude;
			$search_radius = ($user->search_radius) ? (int) $user->search_radius : 0;
			$already_friends_id = DB::table('friends')->where('user_id', $request->user_id)->get()->pluck('friend_id')->toArray();
			$already_requested_friends_id = DB::table('friend_requests')->where('sender_id', $request->user_id)->pluck('user_id')->toArray();
			$friends_id_who_requested = DB::table('friend_requests')->where('user_id', $request->user_id)->get();

			array_push($already_friends_id, $request->user_id);

			if ($request->has('search')) {
				$friends = User::AppUser()->select("*", DB::raw(GeoLocation::getLatLongQuery($latitude, $longitude)))
					->where(function ($query) use ($request) {
						return $query->where('name', 'like', "%$request->search%")
							->orWhere('email', 'like', "%$request->search%");
					})
					->where('users.id', '<>', $request->user_id)
					->get();
			} else {
				$friends = User::AppUser()->select("*", DB::raw(GeoLocation::getLatLongQuery($latitude, $longitude)))
					->having('distance', '<=', $search_radius)
					->whereNotIn('id', $already_friends_id)
					->whereNotIn('id', $friends_id_who_requested->pluck('sender_id')->toArray())
					->orderBy('distance', 'asc')
					->get();
			}
		} else {
			$user_ids = Event::select('user_id', DB::raw('count(id) as total_event'))
				->groupBy('user_id')
				->orderBy('total_event', 'desc')
				->limit(20)
				->get()
				->pluck('user_id')
				->toArray();
			$friends = User::whereIn('id', $user_ids)->get();
		}
		$friend_collection = $this->getShortUserCollection($friends, $already_friends_id, $already_requested_friends_id);
		foreach ($friend_collection as $key => $fcol) {
			$friend_collection[$key]['is_already_friend'] = in_array($fcol['id'], $already_friends_id);
			$friend_collection[$key]['have_already_requested'] = false;
			$friend_collection[$key]['friend_request_id'] = 0;
			$ff = $friends_id_who_requested->where('sender_id', $fcol['id'])->first();
			if ($ff) {
				$friend_collection[$key]['have_already_requested'] = true;
				$friend_collection[$key]['friend_request_id'] = $ff->id;
			}
		}
		return [
			'status_flag' => true,
			'message' => '',
			'suggested_friends' => $friend_collection,
		];
	}

	public function send_request(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'sender_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$request_exist = FriendRequest::where('user_id', $request->user_id)
			->where('sender_id', $request->sender_id)
			->first();

		if ($request_exist) {
			$request_exist->delete();
			return [
				'status_flag' => true,
				'request_id' => null,
				'message' => '',
			];
		} else {
			$request_exist = FriendRequest::where('user_id', $request->sender_id)
				->where('sender_id', $request->user_id)
				->count();
			if ($request_exist > 0) {
				return [
					'status_flag' => true,
					'request_id' => null,
					'message' => 'Looks like this user has already sent you a friend request. Please check your friend requests and take action.',
				];
			}
			$request = FriendRequest::updateOrCreate([
				'sender_id' => $request->sender_id,
				'user_id' => $request->user_id,
			], [
				'sender_id' => $request->sender_id,
				'user_id' => $request->user_id,
			]);
			$this->sendNotification($request->sender_id, [$request->user_id], 'friend_request', $request);
			return [
				'status_flag' => true,
				'request_id' => $request->id,
				'message' => '',
			];
		}
	}

	public function respond(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'request_id' => 'required | exists:friend_requests,id',
			'acceptance' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$friend_request = FriendRequest::find($request->request_id);

		if ($request->acceptance == 1) {
			$exist = DB::table('friends')
				->whereIn('user_id', [$friend_request->sender_id, $friend_request->user_id])
				->whereIn('friend_id', [$friend_request->sender_id, $friend_request->user_id])
				->count();
			if ($exist == 0) {
				DB::table('friends')->insert([
					['user_id' => $friend_request->sender_id, 'friend_id' => $friend_request->user_id],
					['friend_id' => $friend_request->sender_id, 'user_id' => $friend_request->user_id],
				]);
			}
			$friend_request->delete();
			return [
				'status_flag' => true,
				'message' => 'Invitation Accepted',
			];
		} else {
			$friend_request->delete();
			return [
				'status_flag' => true,
				'message' => 'Invitation Ignored',
			];
		}
	}

	public function join_activity(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'event_id' => 'required | exists:events,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		$obj = Event::find($request->event_id);
		$participants_count = $obj->participants()->count();
		if ($obj->activity_category_id == 2) {
			switch ($obj->frequency) {
				case 'Daily':
					$participants_count = DB::table('event_user')->where('event_id', $obj->id)->where('joined_for', Carbon::parse($request->joined_for))->count();
					break;
				case 'Weekly':
					$participants_count = DB::table('event_user')->where('event_id', $obj->id)->whereRaw('WEEK(joined_for) = ' . Carbon::parse($request->joined_for)->format('W'))->count();
					break;
				case 'Monthly':
					$participants_count = DB::table('event_user')->where('event_id', $obj->id)->whereMonth('joined_for', Carbon::parse($request->joined_for)->format('m'))->count();
					break;
			}
		}

		if ($obj->no_of_participants > $participants_count) {
			$obj->participants()->attach([$request->user_id => ['joined_for' => $request->joined_for]]);
			$this->sendNotification($request->user_id, [$obj->user_id], 'joined_activity', $this->getEventObj($obj));
			return [
				'status_flag' => true,
				'message' => 'Successfully Joined',
				'event' => $this->getEventObj($obj),
			];
		} else {
			return [
				'status_flag' => false,
				'message' => 'This Event is already full',
				'event' => $this->getEventObj($obj),
			];
		}
	}

	public function pending_friend_requests(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		$user = User::find($request->user_id);
		$pending_ids = DB::table('friend_requests')->select('id', 'sender_id')->where('user_id', $request->user_id)->get();
		$pending_users = User::select('users.id', 'users.avatar', 'users.email', 'users.name', 'users.latitude', 'users.longitude')
			->whereIn('id', $pending_ids->pluck('sender_id')->toArray())
			->get();
		$already_friends_id = $user->friends()->get()->pluck('id')->toArray();
		$pendings = $this->getShortUserCollection($pending_users, $already_friends_id, []);
		foreach ($pendings as $key => $pending) {
			$pendings[$key]['friend_request_id'] = $pending_ids->where('sender_id', $pending['id'])->first()->id;
		}
		return [
			'status_flag' => true,
			'message' => '',
			'pending_requests' => $pendings,
		];
	}

	public function change_password(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'password' => 'required',
			'old_password' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		$user = User::find($request->user_id);
		if (Hash::check($request->old_password, $user->password)) {
			$user->password = bcrypt($request->password);
			$user->save();
			return [
				'status_flag' => true,
				'message' => 'Password Changed Successfully',
			];
		} else {
			return [
				'status_flag' => false,
				'message' => 'Old Password did not match'
			];
		}
	}

	public function change_avatar(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		$user = User::find($request->user_id);
		$avatar = null;
		if ($request->avatar) {
			$file = $this->upload($request->avatar, 'uploads/users');
			$avatar = isset($file['file_name']) ? $file['file_name'] : null;
		}
		$user->avatar = $avatar;
		$user->save();
		$action = empty($avatar) ? 'removed' : 'updated';
		return [
			'status_flag' => true,
			'message' => 'Profile picture has been ' . $action,
			'user' => $this->getUserObj($user),
		];
	}

	public function update_bio(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		$user = User::find($request->user_id);
		if ($request->address) {
			$user->address = $request->address;
		}
		if ($request->bio) {
			$user->bio = $request->bio;
		}
		if ($request->name) {
			$user->name = $request->name;
		}
		$user->save();
		return [
			'status_flag' => true,
			'message' => 'Details updated',
			'user' => $this->getUserObj($user),
		];
	}

	public function invite_non_users(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'contacts' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$app_link = Setting::where('name', 'IOS_APP_LINK')->first()->value;
		$message = $request->username . " has invite you to join Recess, follow this link to download the app " . $app_link;
		foreach ($request->contacts as $contact) {
			$type = 'mobile_no';
			if (strpos($contact, '@')) {
				$type = 'email';
			}
			$exists = User::where($type, $contact)->count();
			if ($exists == 0) {
				if ($type == 'mobile_no') {
					//SendSMS::sendMessage($message, $contact);
				} else {
					Mail::send('invite', ['app_link' => $app_link, 'username' => $request->username], function ($msg) use ($contact) {
						$msg->to($contact, 'User')->subject('Invite to join your friend on Recess');
					});
				}
			}
		}
		return [
			'status_flag' => true,
			'message' => 'Contacts you shared are invited to join Recess',
		];
	}

	public function contact_us(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'email' => 'required | email',
			'message' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$email = Setting::where('name', 'EMAIL')->first()->value;

		$subject = 'Someone tried to contact us';
		if ($request->submitted_from && $request->submitted_from == "web") {
			$request->name . " has tried to contact us from Website";
		}
		Mail::send('contact', ['user_message' => $request->message, 'user_mail' => $request->email], function ($msg) use ($email, $subject) {
			$msg->to($email, 'Admin')->subject($subject);
		});

		return [
			'status_flag' => true,
			'message' => 'Thank you for contacting us, we will get back to you soon.',
		];
	}

	public function profile(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'viewer_id' => 'required | exists:users,id',
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$user = User::find($request->user_id);
		$user_obj = $this->getUserObj($user);
		$common_friend_ids = DB::select("select friend_id from friends where user_id = " . $request->viewer_id . " and friend_id in (select friend_id from friends where user_id = " . $request->user_id . ")");
		$common_friend_ids = collect($common_friend_ids)->pluck("friend_id")->toArray();

		$user_obj['mutual_friends'] = $this->getShortUserCollection(User::whereIn('id', $common_friend_ids)->get());
		foreach ($user_obj['mutual_friends'] as $key => $value) {
			$user_obj['mutual_friends'][$key]['is_already_friend'] = true;
			$user_obj['mutual_friends'][$key]['mutual_friends'] = count(DB::select("select friend_id from friends where user_id = " . $request->viewer_id . " and friend_id in (select friend_id from friends where user_id = " . $value['id'] . ")"));
		}
		$user_obj['is_already_friend'] = DB::table('friends')->where('user_id', $request->user_id)->where('friend_id', $request->viewer_id)->count() > 0;
		$user_obj['have_already_requested'] = false;
		$user_obj['friend_request_id'] = 0;
		$ff = DB::table('friend_requests')->where('user_id', $request->viewer_id)->where('sender_id', $request->user_id)->first();
		if ($ff) {
			$user_obj['have_already_requested'] = true;
			$user_obj['friend_request_id'] = $ff->id;
		}
		$user_obj['is_requested'] = DB::table('friend_requests')->where('user_id', $request->user_id)->where('sender_id', $request->viewer_id)->count() > 0;
		$user_obj['total_friends'] = DB::table('friends')->where('user_id', $request->user_id)->count();

		return [
			'status_flag' => true,
			'message' => '',
			'user' => $user_obj,
		];
	}

	public function unfriend(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'friend_id' => 'required | exists:users,id',
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		DB::select("DELETE FROM friends WHERE (user_id = $request->user_id AND friend_id = $request->friend_id) OR (user_id = $request->friend_id AND friend_id = $request->user_id)");
		return [
			'status_flag' => true,
			'message' => 'User has been removed from your friend list',
		];
	}

	public function update_token(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'device_token' => 'required',
			'device_type' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		Device::where('device_type', $request->device_type)->where('user_id', $request->user_id)->delete();
		$device = new Device;
		$device->device_token = $request->device_token;
		$device->device_type = $request->device_type;
		$device->user_id = $request->user_id;
		$device->save();
		return [
			'status_flag' => true,
			'message' => 'Device token updated',
		];
	}

	public function get_notifications(Request $request)
	{
		$page = ($request->page) ?? 1;
		$per_page = ($request->per_page) ?? 50;
		$offset = ($page - 1) * $per_page;

		$query = Notification::where('user_id', $request->user_id);

		$total = $query->count();

		$notifications = $query->orderBy('updated_at', 'desc')->get();
		foreach ($notifications as $key => $notification) {
			$notifications[$key]->activity = null;
			$notifications[$key]->post = null;
			if (!is_null($notification->payload)) {
				$notifications[$key]->payload = @json_decode($notification->payload, true);
				if (in_array($notification->unique_key, ['invited_to_activity', 'joined_activity'])) {
					$notifications[$key]->activity = $notifications[$key]->payload;
					unset($notifications[$key]->payload);
				} else if (in_array($notification->unique_key, ['liked', 'commented'])) {
					$notifications[$key]->post = $notifications[$key]->payload;
					unset($notifications[$key]->payload);
				}
			}
		}
		return [
			'status_flag' => true,
			'total_notifications' => $total,
			'notifications' => $notifications,
			'message' => '',
		];
	}

	public function read_notification(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'notification_id' => 'required | exists:notifications,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];

		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		Notification::where('id', $request->notification_id)->delete();
		return [
			'status_flag' => true,
			'message' => 'Notification archieved',
		];
	}

	public function notify_users(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'sender_id' => 'required | exists:users,id',
			'user_ids' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$this->sendNotification($request->sender_id, $request->user_ids, 'event_reminder', null);
		return [
			'status_flag' => true,
			'message' => 'Notification sent to selected users',
		];
	}

	public function logout(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'device_type' => 'required'
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$user = User::find($request->user_id);
		$user->devices()->where('device_type', $request->device_type)->delete();
		return [
			'status_flag' => true,
			'message' => 'User logged out',
		];
	}

	public function update_bank(Request $request)
	{
		// $validation_result = $this->apiValidation($request, [
		// 	'user_id' => 'required | exists:users,id',
		// 	'bank_name' => 'required',
		// 	'account_no' => 'required',
		// 	'account_holder' => 'required',
		// 	'aba_number' => 'required',
		// ]);
		// $response = [
		// 	'status_flag' => false,
		// 	'message' => 'Parameters missing',
		// ];
		// if (!empty($validation_result)) {
		// 	$response['errors'] = $validation_result;
		// 	return $response;
		// }
		if ($request->remove == 1) {
			BankDetail::where('user_id', $request->user_id)->delete();
			return [
				'status_flag' => true,
				'message' => 'Bank details Removed',
				'user_details' => $this->getUserObj(User::find($request->user_id)),
			];
		}
		BankDetail::updateOrCreate(['user_id' => $request->user_id], $request->all());
		return [
			'status_flag' => true,
			'message' => 'Bank details updated',
			'user_details' => $this->getUserObj(User::find($request->user_id)),
		];
	}

	public function update_purchase(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'purchased' => 'required'
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		$user = User::find($request->user_id);
		$user->purchased = $request->purchased;
		$user->purchased_on = $request->purchased ? Carbon::now() : null;
		$user->purchase_token = $request->purchase_token;
		$user->save();

		return [
			'status_flag' => true,
			'message' => 'Purchase status updated',
			'user_details' => $this->getUserObj($user)
		];
	}

	public function make_transaction(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'amount' => 'required',
			'currency' => 'required',
			'description' => 'required',
			'stripe_token' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		try {
			\Stripe\Stripe::setApiKey(env("STRIPE_SECRET_KEY"));
			$stripe_resp = \Stripe\Charge::create([
				'amount' => $request->amount,
				'currency' => $request->currency,
				'description' => $request->description,
				'source' => $request->stripe_token
			]);
			return [
				'status_flag' => true,
				'message' => 'Successful Transaction',
				'stripe_resp' => $stripe_resp
			];
		} catch (\Exception $e) {
			return [
				'status_flag' => false,
				'message' => 'Oops! Something went wrong',
				'errors' => $e->getMessage()
			];
		}
	}

	public function create_ephemeral_key(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required|exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}

		$user = User::find($request->user_id);
		if ($user->stripe_id) {
			$key = StripeHelper::createEmphemeralKey($user->stripe_id);
			return @json_encode($key);
		} else {
			return [
				'status_flag' => false,
				'message' => 'Stripe Customer ID missing',
			];
		}
	}

	public function create_payment_intent(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'amount' => 'required',
			'currency' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		try {
			$params = $request->all();
			$user = User::find($params['customer']);
			$params['customer'] = $user->stripe_id;
			$resp = StripeHelper::createPaymentIntent($params);
			return [
				'status_flag' => true,
				'message' => '',
				'intent_token' => $resp
			];
		} catch (\Exception $e) {
			return [
				'status_flag' => false,
				'message' => 'Oops! Something went wrong',
				'errors' => $e->getMessage()
			];
		}
	}

	public function saved_cards(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		$user = User::find($request->user_id);
		$cards = [];
		if ($user->stripe_id) {
			$resp = StripeHelper::fetchCards($user->stripe_id);
			return [
				'status_flag' => true,
				'message' => '',
				'saved_cards' => $resp
			];
		}
		return [
			'status_flag' => true,
			'message' => 'Purchase status updated',
			'user_details' => $this->getUserObj($user)
		];
	}

	public function transaction_complete(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'event_id' => 'required | exists:events,id',
			'amount' => 'required',
			'currency' => 'required',
			'card_ends_with' => 'required',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		BankTransaction::create($request->all());
		return [
			'status_flag' => true,
			'message' => 'Transaction recorded',
		];
	}


	public function payment_history(Request $request)
	{
		$page = ($request->page) ?? 1;
		$per_page = ($request->per_page) ?? 10;
		$offset = ($page - 1) * $per_page;
		$total_paid = BankTransaction::selectRaw("ROUND(SUM(amount), 2) as total")->where('user_id', $request->user_id)->first()->total;
		$total_received = BankTransaction::selectRaw("ROUND(SUM(amount), 2) as total")
			->whereIn('event_id', Event::select('id')->where('user_id', $request->user_id)->get()->pluck('id')->toArray())
			->first()->total;
		if ($request->type == 'paid') {
			$payment_histories = BankTransaction::where('user_id', $request->user_id)->offset($offset)->limit($per_page)->get();
			foreach ($payment_histories as $payment) {
				$event = Event::find($payment->event_id);
				$event_obj = $this->getEventObj($event);
				$payment->next_payment_date = $event->getNextEventDateAttribute($payment->joined_for);
				$payment->event = $event_obj;
			}
		} else {
			$event_ids = Event::select('id')->where('user_id', $request->user_id)->get()->pluck('id')->toArray();
			$payment_histories = BankTransaction::selectRaw('bank_transactions.event_id, SUM(amount) as total')
				->whereIn('event_id', $event_ids)
				->groupBy('event_id')
				->having('total', '>', 0)
				->offset($offset)
				->limit($per_page)
				->get();
			foreach ($payment_histories as $payment) {
				$payment->total = (float) $payment->total;
				$payment->event = $this->getEventObj(Event::find($payment->event_id));
			}
		}


		return [
			'status_flag' => true,
			'total_paid' => (float) $total_paid,
			'total_received' => $total_received ? (float) $total_received : 0.0,
			'payment_history' => $payment_histories,
			'message' => ''
		];
	}

	public function delete_card(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'stripe_card_id' => 'required'
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		$user = User::find($request->user_id);
		if ($user->stripe_id) {
			$resp = StripeHelper::deleteCard($user->stripe_id, $request->stripe_card_id);
			return [
				'status_flag' => true,
				'message' => 'Card Deleted',
			];
		}
		return [
			'status_flag' => false,
			'message' => 'Stripe Customer ID not found',
			'user_details' => $this->getUserObj($user)
		];
	}

	public function create_card(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
			'amex_token' => 'required'
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		$user = User::find($request->user_id);
		if ($user->stripe_id) {
			$resp = StripeHelper::createCard($user->stripe_id, $request->amex_token);
			return [
				'status_flag' => true,
				'message' => 'Card Added',
				'card' => $resp
			];
		}
		return [
			'status_flag' => false,
			'message' => 'Stripe Customer ID not found',
			'user_details' => $this->getUserObj($user)
		];
	}

	public function getPaymentDetails(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'event_id' => 'required | exists:events,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$payment_histories = BankTransaction::where('event_id', $request->event_id)->get();
		foreach ($payment_histories as $payment) {
			$event = Event::find($payment->event_id);
			$event_obj = $this->getEventObj($event);
			$payment->user = $this->getShortUserCollection(User::where('id', $payment->user_id)->get())[0];
			$payment->next_payment_date = $event->getNextEventDateAttribute($payment->joined_for);
			$payment->event = $event_obj;
		}
		return [
			'status_flag' => true,
			'payments' => $payment_histories,
			'message' => ''
		];
	}

	public function me(Request $request)
	{
		$validation_result = $this->apiValidation($request, [
			'user_id' => 'required | exists:users,id',
		]);
		$response = [
			'status_flag' => false,
			'message' => 'Parameters missing',
		];
		if (!empty($validation_result)) {
			$response['errors'] = $validation_result;
			return $response;
		}
		$user = User::find($request->user_id);
		return [
			'status_flag' => true,
			'user' => $this->getUserObj($user),
			'message' => ''
		];
	}
}
