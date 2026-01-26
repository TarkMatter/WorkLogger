<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use \App\Http\Controllers\ProjectController;

use App\Http\Controllers\Admin\UserPermissionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/locale', function (Request $request) {
    $data = $request->validate([
        'locale' => ['required', 'in:ja,en'],
    ]);

    session(['locale' => $data['locale']]);

    return back()->withCookie(cookie()->forever('locale', $data['locale']));
})->name('locale.set');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:manage-user-permissions'])->group(function () {
    Route::get('users', [UserPermissionController::class, 'index'])->name('users.index');
    Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])->name('users.permissions.edit');
    Route::put('users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');
});
    // --------------------------------------------------
    //　案件のCRUD

    Route::middleware(['auth'])->group(function () {

        // 一覧
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

        // adminのみ（作成）
        Route::middleware('can:create,App\Models\Project')->group(function () {
            Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        });

        // adminのみ（編集）
        Route::middleware('can:update,project')->group(function () {
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        });

        // adminのみ（削除）
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
            ->middleware('can:delete,project')
            ->name('projects.destroy');

        // 詳細（← これが最後！）
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });

    // Route::middleware(['auth'])->group(function () {
    //     Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    // });

    // Route::resource('projects', \App\Http\Controllers\ProjectController::class);

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
