<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserPermissionsRequest;
use App\Models\Permission;
use App\Models\User;

class UserPermissionController extends Controller
{
    // 管理者向けのユーザー/権限編集機能。
    public function index()
    {
    $users = User::query()
        ->where('role', '!=', 'admin')     // adminは一覧に出さない
        ->with('permissions')             // バッジ表示のために権限を先読み（N+1防止）
        ->orderBy('id')
        ->get();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $permissions = Permission::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy(fn ($p) => $p->group ?? 'other');

        $user->load('permissions');
        $assigned = $user->permissions->pluck('id')->all();

        return view('admin.users.permissions', compact('user', 'permissions', 'assigned'));
    }

    public function update(UpdateUserPermissionsRequest $request, User $user)
    {
        $data = $request->validated();

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ])->save();

        // role=admin は常に全権限扱いなので、保持してもいいけど運用上分かりやすく空にする
        if ($data['role'] === 'admin') {
            $user->permissions()->sync([]);
        } else {
            $user->permissions()->sync($data['permissions'] ?? []);
        }

        return $this->redirectRouteWithSuccess(
            'admin.users.permissions.edit',
            $user,
            __('admin.flash.user_updated')
        );
    }


}
