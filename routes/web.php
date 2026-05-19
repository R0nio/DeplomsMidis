<?php

use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
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

Route::get('/main', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/projects', [ProjectController::class, 'index'])->name('projects');

Route::get('/map', [ProjectController::class, 'map'])->name('mapProject');

Route::get('/contact', function(){
return Inertia::render('Contact');
})->name('contact');

Route::get('/projectDetail', function(){
    return Inertia::render('ProjectDetail');
})->name('ProjectDetail');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/create', function(){
    return Inertia::render('CreateProject');
    })->name('create');

    Route::post('create', [ProjectController::class, 'store'])->name('projects.store');

    Route::get('/AdminPanel', function(){
    return Inertia::render('AdminPanel');
    })->name('adminPanel');

    Route::post('/favorites/toggle/{project}', [FavoriteController::class, 'toggle'])
    ->name('favorites.toggle')
    ->middleware(['auth']);

    Route::post('/favorites/{project}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Управление проектами
    Route::get('/projectss', [AdminProjectController::class, 'index'])->name('projects.index');
    
    // Одиночные действия
    Route::patch('/projects/{project}/status', [AdminProjectController::class, 'updateStatus'])->name('projects.update-status');
});


require __DIR__.'/auth.php';
