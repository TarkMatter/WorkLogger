<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // 読み取りは全員OK
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return true;
    }

    // ここからCRUD：adminが付与した権限がある場合のみ
    public function create(User $user): bool
    {
        return $user->hasPermission('projects.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.update');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.delete');
    }
}
