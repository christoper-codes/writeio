<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Blogs
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('blogs/create', [BlogController::class, 'create'])->name('blogs.create');
    Route::post('blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('blogs/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::post('blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    Route::patch('blogs/{blog}/toggle-publish', [BlogController::class, 'togglePublish'])->name('blogs.toggle-publish');
    Route::post('blogs/{blog}/upload-image', [BlogController::class, 'uploadImage'])->name('blogs.upload-image');

    // Media
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');

    // Topics
    Route::get('topics', [TopicController::class, 'index'])->name('topics.index');
    Route::post('topics', [TopicController::class, 'store'])->name('topics.store');
    Route::delete('topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
});

// Public profiles
Route::get('/u/{username}', [PublicProfileController::class, 'show'])->name('user.profile');
Route::get('/u/{username}/{slug}', [PublicProfileController::class, 'blog'])->name('user.blog');

require __DIR__.'/settings.php';
