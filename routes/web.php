<?php

use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
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

Route::get('/main', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/projects', [ProjectController::class, 'index'])->name('projects');

Route::get('/map', [ProjectController::class, 'map'])->name('mapProject');

Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/create', function(){
    return Inertia::render('CreateProject');
    })->name('create');


    Route::post('create', [ProjectController::class, 'store'])->name('projects.store');

    Route::post('/favorites/toggle/{project}', [FavoriteController::class, 'toggle'])
    ->name('favorites.toggle')
    ->middleware(['auth']);

    Route::post('/favorites/{project}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');


    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('/projects/{project}/update', [ProjectController::class, 'update'])->name('projects.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::patch('/projects/{project}/status', [AdminProjectController::class, 'updateStatus'])
        ->name('projects.update-status');
});


require __DIR__.'/auth.php';
