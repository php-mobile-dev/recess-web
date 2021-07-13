<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return bcrypt('Envolappcreation22!');
    return view('welcome');
});
Route::prefix('admin')->middleware(['auth:sanctum', 'verified'])->group(function (){
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('/change-password', function(){
        return view ('admin.password-change');
    });
    Route::post('/change-password', [App\Http\Controllers\HomeController::class, 'changePassword']);

    Route::post('/user/del', [App\Http\Controllers\UserController::class, 'del']);
    Route::resource('/user', App\Http\Controllers\UserController::class);
    Route::get('/change/user/{id}', [App\Http\Controllers\UserController::class, 'change']);
    Route::get('/list/user', [App\Http\Controllers\UserController::class, 'listing']);
    
    Route::resource('/setting', App\Http\Controllers\SettingsController::class);
    Route::get('/list/setting', [App\Http\Controllers\SettingsController::class, 'listing']);

    Route::post('/content/del', [App\Http\Controllers\ContentController::class, 'del']);
    Route::resource('/content', App\Http\Controllers\ContentController::class);
    Route::get('/list/content', [App\Http\Controllers\ContentController::class, 'listing']);

    Route::resource('/activitycategory', App\Http\Controllers\ActivityCategoryController::class);
    Route::get('/change/activitycategory/{id}', [App\Http\Controllers\ActivityCategoryController::class, 'change']);
    Route::get('/list/activitycategory', [App\Http\Controllers\ActivityCategoryController::class, 'listing']);

    Route::resource('/activitytype', App\Http\Controllers\ActivityTypeController::class);
    Route::get('/change/activitytype/{id}', [App\Http\Controllers\ActivityTypeController::class, 'change']);
    Route::get('/list/activitytype', [App\Http\Controllers\ActivityTypeController::class, 'listing']);

    Route::resource('/activity', App\Http\Controllers\ActivityController::class);
    Route::get('/change/activity/{id}', [App\Http\Controllers\ActivityController::class, 'change']);
    Route::get('/list/activity', [App\Http\Controllers\ActivityController::class, 'listing']);

    Route::resource('/event', App\Http\Controllers\EventController::class);
    Route::get('/change/event/{id}', [App\Http\Controllers\EventController::class, 'change']);
    Route::get('/list/event', [App\Http\Controllers\EventController::class, 'listing']);

    Route::resource('/report', App\Http\Controllers\ReportController::class);
    Route::get('/change/report/{id}', [App\Http\Controllers\ReportController::class, 'changeStatus']);
    Route::get('/list/report', [App\Http\Controllers\ReportController::class, 'listing']);

});


Route::get('/webview/reset-password', [App\Http\Controllers\PageController::class, 'reset']);
Route::post('/webview/res-password', [App\Http\Controllers\PageController::class, 'resetPassword']);
Route::get('/webview/{slug}', [App\Http\Controllers\PageController::class, 'show']);

Route::get('/test', function(){
    $event = \App\Models\Event::find(89);
    return $event->nextEventDate('2021-04-12 16:08:11');
});

// Route::get('/test-stripe', function(){
//     $users = \App\Models\User::AppUser()->get();
//     foreach($users as $user){
//         $resp = \App\Http\Helpers\StripeHelper::createCustomer([
//             'description' => $user->id,
//             "email" => $user->email,
//             "name" => $user->name
//         ]);
//         $user->stripe_id = $resp->id;
//         $user->save();
//     }
//     return "complete";
// });