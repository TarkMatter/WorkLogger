<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'key' => 'projects.create',
                'label' => '案件：作成',
                'group' => 'projects',
                'description' => '案件を新規作成できる',
            ],
            [
                'key' => 'projects.update',
                'label' => '案件：更新',
                'group' => 'projects',
                'description' => '案件を更新できる',
            ],
            [
                'key' => 'projects.delete',
                'label' => '案件：削除',
                'group' => 'projects',
                'description' => '案件を削除できる',
            ],
        ];

        Permission::upsert($rows, ['key'], ['label','group','description']);
    }
}
