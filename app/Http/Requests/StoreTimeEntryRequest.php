<?php

namespace App\Http\Requests;

use App\Models\DailyReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 工数追加の入力検証をまとめたリクエスト。
 */
class StoreTimeEntryRequest extends FormRequest
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
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id'),
            ],
            'minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'task' => ['nullable', 'string', 'max:255'],
        ];
    }
}
