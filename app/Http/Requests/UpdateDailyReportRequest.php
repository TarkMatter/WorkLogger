<?php

namespace App\Http\Requests;

use App\Models\DailyReport;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 日報更新の入力検証をまとめたリクエスト。
 */
class UpdateDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルートパラメータの日報に対する更新権限を確認。
        $report = $this->route('report');

        return $report instanceof DailyReport
            && ($this->user()?->can('update', $report) ?? false);
    }

    public function rules(): array
    {
        return [
            'memo' => ['nullable', 'string'],
        ];
    }
}
