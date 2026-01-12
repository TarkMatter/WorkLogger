<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;

use App\Models\Project;
use App\Policies\ProjectPolicy;

use App\Models\DailyReport;
use App\Policies\DailyReportPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policy登録（この構成ではここでOK）
        Gate::policy(Project::class, ProjectPolicy::class);

        // 既にDailyReportPolicyを使っているなら、ここで登録しておくと確実
        Gate::policy(DailyReport::class, DailyReportPolicy::class);
    }
}
