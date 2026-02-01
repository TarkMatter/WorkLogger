<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 管理者向けのユーザー情報・権限更新の入力検証。
 */
class UpdateUserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 管理者権限（Gate）を利用。
        return $this->user()?->can('manage-user-permissions') ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique(\App\Models\User::class, 'email')->ignore($userId),
            ],
            'role' => ['required', 'in:admin,approver,member'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }
}
