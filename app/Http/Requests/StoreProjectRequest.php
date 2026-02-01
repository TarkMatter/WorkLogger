<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 案件作成の入力検証をまとめたリクエスト。
 */
class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 案件作成権限（ポリシー）を利用。
        return $this->user()?->can('create', Project::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:255', 'unique:projects,name'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => __('projects.validation.end_date_after_or_equal'),
        ];
    }
}
