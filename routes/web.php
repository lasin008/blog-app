<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('posts', PostController::class)->middleware('auth');
Route::resource('comments', CommentController::class)->middleware('auth');
Route::get('comment/{postId}', [CommentController::class, 'findByPost']);

Route::get('/home', [PostController::class, 'showPosts'])->name('home')->middleware('auth');

Route::get('post/{postId}/get', [PostController::class, 'find']);

Route::get('edit/{postId}', [PostController::class, 'edit']);
