<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function(){
    return redirect('/main');
});

// пример с мидлваер
// Route::get('/main', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/main', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/create', function(){
    return Inertia::render('CreateProject');
})->name('create');

Route::get('/projects', function(){
    return Inertia::render('Projects');
})->name('projects');

Route::get('/mapProject', function(){
return Inertia::render('MapProject');
})->name('mapProject');

Route::get('/AdminPanel', function(){
return Inertia::render('AdminPanel');
})->name('adminPanel');

Route::get('/contact', function(){
return Inertia::render('Contact');
})->name('contact');


Route::get('/projectDetail', function(){
    return Inertia::render('ProjectDetail');
})->name('ProjectDetail');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__.'/auth.php';
