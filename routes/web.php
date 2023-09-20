<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Companies\CommentController;
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

Route::get('/', function () {
    return view('welcome');
});

// Users routes

Route::get('/users', [UserController::class, 'index'])->name('users.index');
// Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Companies routs

Route::get('/companies/top', [CompanyController::class, 'getCompaniesTop'])->name('companies.getCompaniesTop');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
//Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

// Comments routes

Route::prefix('/companies/{company}')->group(function () {
    Route::get('/comments/average_rating', [CommentController::class, 'getAverageRating'])->name('companies.comments.getAverageRating');

    Route::get('/comments', [CommentController::class, 'index'])->name('companies.comments.index');
    // Route::get('/comments/create', [CommentController::class, 'create'])->name('companies.comments.create');
    Route::post('/comments', [CommentController::class, 'store'])->name('companies.comments.store');
    Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('companies.comments.show');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('companies.comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('companies.comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('companies.comments.destroy');
});

