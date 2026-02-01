<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 案件更新の入力検証をまとめたリクエスト。
 */
class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルートパラメータの案件に対する更新権限を確認。
        $project = $this->route('project');

        return $project instanceof Project
            && ($this->user()?->can('update', $project) ?? false);
    }

    public function rules(): array
    {
        $project = $this->route('project');
        $projectId = $project?->id;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($projectId)],
            'name' => ['required', 'string', 'max:255', Rule::unique('projects', 'name')->ignore($projectId)],
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
