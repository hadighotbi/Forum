<?php

use App\Http\Controllers\Api\UserAvatarController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Auth\RegisterConfirmationController;
use App\Http\Controllers\BestRepliesController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\LockedThreadsController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\ThreadsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThreadSubscriptionsController;
use App\Http\Controllers\UserNotificationsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('/threads/');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

//Threads
Route::get('threads', [ThreadsController::class, 'index'])->name('threads');
Route::post('threads', [ThreadsController::class, 'store'])->middleware('must-be-confirmed');
Route::get('threads/create', [ThreadsController::class, 'create']);
Route::get('threads/{channel}/{thread}', [ThreadsController::class,'show']);
Route::patch('threads/{channel}/{thread}', [ThreadsController::class,'update'])->name('threads.update');
Route::delete('threads/{channel}/{thread}', [ThreadsController::class,'destroy']);
Route::get('threads/{channel}/{thread}/replies', [RepliesController::class, 'index']);
Route::post('threads/{channel}/{thread}/replies', [RepliesController::class, 'store']);
Route::get('threads/{channel}', [ThreadsController::class, 'index']);

//Lock Threads
Route::post('locked-threads/{thread}', [LockedThreadsController::class, 'store'])->name('locked-threads.store')->middleware('admin');
Route::delete('locked-threads/{thread}', [LockedThreadsController::class, 'destroy'])->name('locked-threads.destroy')->middleware('admin');

//Best Reply
Route::post('/replies/{reply}/best', [BestRepliesController::class, 'store'])->name('best-replies.store');

//Favorites
Route::post('replies/{reply}/favorites', [FavoritesController::class, 'store']);
Route::delete('replies/{reply}/favorites', [FavoritesController::class, 'destroy']);

//Replies
Route::delete('replies/{reply}', [RepliesController::class, 'destroy'])->name('replies.destroy');
Route::patch('replies/{reply}', [RepliesController::class, 'update']);

//Profile
Route::get('profiles/{user}', [ProfilesController::class, 'show'])->name('profile');

//Notification
Route::delete('profiles/{user}/notifications/{notification}' , [UserNotificationsController::class , 'destroy']);
Route::get('profiles/{user}/notifications' , [UserNotificationsController::class , 'index']);

//Subscription
Route::post('/threads/{channel}/{thread}/subscriptions' , [ThreadSubscriptionsController::class , 'store'])->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions' , [ThreadSubscriptionsController::class , 'destroy'])->middleware('auth');

//Email Confirmation
Route::get('/register/confirm' , [RegisterConfirmationController::class , 'index'])->name('register.confirm');

//API
Route::get('api/users', [UsersController::class , 'index']);
Route::post('api/users/{user}/avatar', [UserAvatarController::class , 'store'])->middleware('auth')->name('avatar');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
