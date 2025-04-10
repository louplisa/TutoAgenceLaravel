<?php

use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
$idRegex = '[0-9]+';
$slugRegex = '[a-z0-9\-]+';

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/biens', [\App\Http\Controllers\PropertyController::class, 'index'])->name('property.index');
Route::get('/biens/{slug}-{property}', [\App\Http\Controllers\PropertyController::class, 'show'])->name('property.show')->where([
    'property' => $idRegex,
    'slug'     => $slugRegex,
]);
Route::post('/biens/{property}/contact', [\App\Http\Controllers\PropertyController::class, 'contact'])->name('property.contact')->where([
    'property' => $idRegex,
]);

Route::get('/images/{path}',[ImageController::class, 'show'])->where('path', '.*');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () use ($idRegex) {
    Route::resource('property', PropertyController::class)->except(['show']);
    Route::resource('option', OptionController::class)->except(['show']);
    Route::delete('picture/{picture}', [\App\Http\Controllers\Admin\PictureController::class, 'destroy'])
        ->name('picture.destroy')
        ->where([
        'picture' => $idRegex,
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
