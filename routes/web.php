<?php

use App\Http\Controllers\Admin\UserPermissionController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TimeEntryController;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    $pendingApprovals = collect();
    $pendingApprovalsCount = 0;

    if ($user->canApprove()) {
        $pendingBase = DailyReport::query()
            ->where('status', 'submitted')
            ->where('user_id', '!=', $user->id);

        $pendingApprovalsCount = (clone $pendingBase)->count();
        $pendingApprovals = $pendingBase
            ->with('user')
            ->withSum('timeEntries as total_minutes', 'minutes')
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }

    $rejectedBase = DailyReport::query()
        ->where('status', 'rejected')
        ->where('user_id', $user->id);

    $rejectedReportsCount = (clone $rejectedBase)->count();
    $rejectedReports = $rejectedBase
        ->withSum('timeEntries as total_minutes', 'minutes')
        ->orderByDesc('report_date')
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    return view('dashboard', compact(
        'pendingApprovals',
        'pendingApprovalsCount',
        'rejectedReports',
        'rejectedReportsCount'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/locale', function (Request $request) {
    // 対応言語を取得（['ja', 'en'] は取得できなかった場合の保険）
    $supported = config('locales.supported', ['ja', 'en']);

    $data = $request->validate([
        'locale' => ['required', Rule::in($supported)],
    ]);

    session(['locale' => $data['locale']]);

    return back()->withCookie(cookie()->forever('locale', $data['locale']));
})->name('locale.set');

Route::middleware('auth')->group(function () {
    // 認証ユーザーの基本設定
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 管理者向けの権限管理
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('can:manage-user-permissions')
        ->group(function () {
            Route::get('users', [UserPermissionController::class, 'index'])->name('users.index');
            Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])->name('users.permissions.edit');
            Route::put('users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');
        });

    // 案件のCRUD
    Route::resource('projects', ProjectController::class);

    // 日報のCRUD
    Route::resource('reports', DailyReportController::class);

    Route::prefix('reports/{report}')->name('reports.')->group(function () {
        // 日報の提出/承認/差し戻し
        Route::post('submit', [DailyReportController::class, 'submit'])->name('submit');
        Route::post('approve', [DailyReportController::class, 'approve'])->name('approve');
        Route::post('reject', [DailyReportController::class, 'reject'])->name('reject');

        // 工数の追加/削除
        Route::post('entries', [TimeEntryController::class, 'store'])->name('entries.store');
        Route::delete('entries/{entry}', [TimeEntryController::class, 'destroy'])->name('entries.destroy');
    });
});

require __DIR__ . '/auth.php';
