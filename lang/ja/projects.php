<?php

return [
    'title' => '案件',
    'create_title' => '案件 新規作成',
    'edit_title' => '案件 編集',
    'detail_title' => '案件 詳細',

    'labels' => [
        'code' => 'コード',
        'name' => '案件名',
        'start_date' => '開始日',
        'end_date' => '終了日',
        'description' => '説明',
    ],

    'empty' => '案件がありません。',

    'flash' => [
        'created' => '案件を作成しました。',
        'updated' => '案件を更新しました。',
        'deleted' => '案件を削除しました。',
        'cannot_delete_in_use' => 'この案件は日報の工数で使用されているため削除できません。',
    ],

    'confirm' => [
        'delete' => '削除します。よろしいですか？',
    ],

    'validation' => [
        'end_date_after_or_equal' => '終了日は開始日以降の日付を指定してください。',
    ],

    'unset' => '（未設定）',
];
