<?php

namespace App\Http\Requests;

use App\Models\DailyReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 日報作成の入力検証をまとめたリクエスト。
 */
class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 日報作成権限（ポリシー）を利用。
        return $this->user()?->can('create', DailyReport::class) ?? false;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'report_date' => [
                'required',
                'date',
                Rule::unique('daily_reports', 'report_date')
                    ->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'memo' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'report_date.unique' => __('reports.validation.report_date_unique'),
        ];
    }
}
