<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --------------------------------------------------
    //　案件のCRUD
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);

    // --------------------------------------------------
    // 日報のCRUD
    Route::resource('reports', \App\Http\Controllers\DailyReportController::class);
    
    // 日報の提出
    Route::post('reports/{dailyReport}/submit', [\App\Http\Controllers\DailyReportController::class, 'submit'])
        ->name('reports.submit');

    // 日報の承認
    Route::post('reports/{dailyReport}/approve', [\App\Http\Controllers\DailyReportController::class, 'approve'])
    ->name('reports.approve');

    // 日報の差し戻し
    Route::post('reports/{dailyReport}/reject', [\App\Http\Controllers\DailyReportController::class, 'reject'])
        ->name('reports.reject');

    // --------------------------------------------------
    // 工数の追加
    Route::post('reports/{dailyReport}/entries', [\App\Http\Controllers\TimeEntryController::class, 'store'])
        ->name('reports.entries.store');
    
    // 工数の削除
    Route::delete('reports/{dailyReport}/entries/{entry}', [\App\Http\Controllers\TimeEntryController::class, 'destroy'])
        ->name('reports.entries.destroy');
});

require __DIR__.'/auth.php';
