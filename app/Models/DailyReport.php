<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    public const WARN_LIMIT_MINUTES = 1440;

    protected $fillable = [
        'report_date',
        'memo',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * 承認用の警告チェックで使う集計SQL。
     */
    public static function warningMinutesSql(): string
    {
        return 'COALESCE((SELECT SUM(minutes) FROM time_entries WHERE time_entries.daily_report_id = daily_reports.id), 0)';
    }

    /**
     * 閲覧できる日報だけに絞り込む。
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->canApprove()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * ステータスの絞り込み（all/nullは無視）。
     */
    public function scopeStatusFilter(Builder $query, ?string $status): Builder
    {
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * 警告対象（0分/24時間超）だけに絞る。
     */
    public function scopeWarningsOnly(
        Builder $query,
        bool $only,
        string $warnSql,
        int $warnLimit
    ): Builder {
        if ($only) {
            $query->whereRaw("{$warnSql} = 0 OR {$warnSql} > ?", [$warnLimit]);
        }

        return $query;
    }

    /**
     * 並び替え（承認者用に拡張）。
     */
    public function scopeSorted(Builder $query, string $sort, string $dir): Builder
    {
        if ($sort === 'report_date') {
            return $query->orderBy('report_date', $dir)->orderBy('id', $dir);
        }

        if ($sort === 'total_minutes') {
            return $query->orderByRaw('COALESCE(total_minutes, 0) ' . $dir)
                ->orderBy('report_date', 'desc')
                ->orderBy('id', 'desc');
        }

        if ($sort === 'user_name') {
            return $query->orderBy(
                    User::select('name')
                        ->whereColumn('users.id', 'daily_reports.user_id'),
                    $dir
                )
                ->orderBy('report_date', 'desc')
                ->orderBy('id', 'desc');
        }

        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(\App\Models\DailyReportStatusLog::class)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');
    }

}
