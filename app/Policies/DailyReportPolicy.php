<?php

namespace App\Policies;

use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DailyReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // 一覧はクエリ側で絞る想定
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DailyReport $dailyReport): bool
    {
        // 承認者/管理者は全件見れる想定（あとで必要なら絞る）
        if ($user->canApprove()) return true;

        // 一般は自分のだけ
        return $dailyReport->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DailyReport $dailyReport): bool
    {
        // 自分の、下書き/差戻しのみ編集可
        return $dailyReport->user_id === $user->id
            && in_array($dailyReport->status, ['draft', 'rejected'], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DailyReport $dailyReport): bool
    {
        // 自分の下書きだけ削除可（任意）
        return $dailyReport->user_id === $user->id
            && $dailyReport->status === 'draft';;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DailyReport $dailyReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DailyReport $dailyReport): bool
    {
        return false;
    }

        // ここからカスタム能力（状態遷移）
    public function submit(User $user, DailyReport $dailyReport): bool
    {
        // 自分の、下書き/差戻しのみ提出可
        return $dailyReport->user_id === $user->id
            && in_array($dailyReport->status, ['draft', 'rejected'], true);
    }

    public function approve(\App\Models\User $user, \App\Models\DailyReport $report): bool
    {
        // 承認者ロールであること
        if (! $user->canApprove()) {
            return false;
        }

        // 自分の日報は承認/差戻し不可（職務分離）
        if ((int) $report->user_id === (int) $user->id) {
            return false;
        }

        // 提出済みのみ承認/差戻し対象
        return $report->status === 'submitted';
    }

    public function reject(User $user, DailyReport $dailyReport): bool
    {
        // 承認者/管理者のみ、提出済みだけ差戻し可
        return $user->canApprove() && $dailyReport->status === 'submitted';
    }
}
