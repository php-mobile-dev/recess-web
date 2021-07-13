<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
	return $request->user();
});

Route::prefix('v1')->group(function () {
	Route::get('/config', [App\Http\Controllers\Api\V1ApiController::class, 'config']);
	Route::post('/login', [App\Http\Controllers\Api\V1ApiController::class, 'login'])->middleware('check_referer');
	Route::post('/logout', [App\Http\Controllers\Api\V1ApiController::class, 'logout']);
	Route::post('/signup', [App\Http\Controllers\Api\V1ApiController::class, 'signup'])->middleware('check_referer');
	Route::post('/verify-mobile', [App\Http\Controllers\Api\V1ApiController::class, 'verifyMobile']);
	Route::post('/validate-otp', [App\Http\Controllers\Api\V1ApiController::class, 'validateOtp']);
	Route::post('/add-preference', [App\Http\Controllers\Api\V1ApiController::class, 'addPreference']);
	Route::post('/reset-password', [App\Http\Controllers\Api\V1ApiController::class, 'reset_password']);
	Route::post('/add-event', [App\Http\Controllers\Api\V1ApiController::class, 'add_event']);
	Route::post('/get-events', [App\Http\Controllers\Api\V1ApiController::class, 'get_events']);
	Route::post('/my-friends', [App\Http\Controllers\Api\V1ApiController::class, 'friends']);
	Route::get('/suggested-friends', [App\Http\Controllers\Api\V1ApiController::class, 'suggested_friends']);
	Route::post('/add-friend', [App\Http\Controllers\Api\V1ApiController::class, 'send_request']);
	Route::post('/respond', [App\Http\Controllers\Api\V1ApiController::class, 'respond']);
	Route::post('/join-activity', [App\Http\Controllers\Api\V1ApiController::class, 'join_activity']);
	Route::post('/pending-friend-request', [App\Http\Controllers\Api\V1ApiController::class, 'pending_friend_requests']);
	Route::post('/change-password', [App\Http\Controllers\Api\V1ApiController::class, 'change_password']);
	Route::post('/change-avatar', [App\Http\Controllers\Api\V1ApiController::class, 'change_avatar']);
	Route::post('/update-bio', [App\Http\Controllers\Api\V1ApiController::class, 'update_bio']);
	Route::post('/invite-non-users', [App\Http\Controllers\Api\V1ApiController::class, 'invite_non_users']);
	Route::post('/contact-us', [App\Http\Controllers\Api\V1ApiController::class, 'contact_us']);
	Route::post('/profile', [App\Http\Controllers\Api\V1ApiController::class, 'profile']);
	Route::post('/unfriend', [App\Http\Controllers\Api\V1ApiController::class, 'unfriend']);
	Route::post('/update-token', [App\Http\Controllers\Api\V1ApiController::class, 'update_token']);
	Route::post('/notifications', [App\Http\Controllers\Api\V1ApiController::class, 'get_notifications']);
	Route::post('/read-notification', [App\Http\Controllers\Api\V1ApiController::class, 'read_notification']);
	Route::post('/notify-users', [App\Http\Controllers\Api\V1ApiController::class, 'notify_users']);
	Route::post('/update-bank', [App\Http\Controllers\Api\V1ApiController::class, 'update_bank']);
	Route::post('/make-ephemeral-key', [App\Http\Controllers\Api\V1ApiController::class, 'create_ephemeral_key']);
	Route::post('/make-payment-intent', [App\Http\Controllers\Api\V1ApiController::class, 'create_payment_intent']);
	Route::post('/make-transaction', [App\Http\Controllers\Api\V1ApiController::class, 'transaction_complete']);
	Route::post('/update-purchase', [App\Http\Controllers\Api\V1ApiController::class, 'update_purchase']);
	Route::post('/saved-cards', [App\Http\Controllers\Api\V1ApiController::class, 'saved_cards']);
	Route::post('/payment-history', [App\Http\Controllers\Api\V1ApiController::class, 'payment_history']);
	Route::post('/delete-card', [App\Http\Controllers\Api\V1ApiController::class, 'delete_card']);
	Route::post('/create-card', [App\Http\Controllers\Api\V1ApiController::class, 'create_card']);
	Route::post('/payment-details', [App\Http\Controllers\Api\V1ApiController::class, 'getPaymentDetails']);
	Route::post('/me', [App\Http\Controllers\Api\V1ApiController::class, 'me']);

	Route::post('/create-post', [App\Http\Controllers\Api\V2ApiPostController::class, 'create_post']);
	Route::post('/get-posts', [App\Http\Controllers\Api\V2ApiPostController::class, 'get_posts']);
	Route::post('/like', [App\Http\Controllers\Api\V2ApiPostController::class, 'like']);
	Route::post('/comment', [App\Http\Controllers\Api\V2ApiPostController::class, 'comment']);
	Route::post('/delete-comment', [App\Http\Controllers\Api\V2ApiPostController::class, 'delete_comment']);
	Route::post('/likes', [App\Http\Controllers\Api\V2ApiPostController::class, 'likes']);
	Route::post('/comments', [App\Http\Controllers\Api\V2ApiPostController::class, 'comments']);
	Route::post('/check-for-story', [App\Http\Controllers\Api\V2ApiPostController::class, 'check_for_story']);
	Route::post('/report', [App\Http\Controllers\Api\V2ApiPostController::class, 'report']);
	Route::post('/share', [App\Http\Controllers\Api\V2ApiPostController::class, 'share']);
	Route::post('/delete', [App\Http\Controllers\Api\V2ApiPostController::class, 'delete']);
	Route::post('/timeline', [App\Http\Controllers\Api\V2ApiPostController::class, 'timeline']);

});