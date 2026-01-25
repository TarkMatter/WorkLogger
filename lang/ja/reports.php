<?php

return [
    'title' => '日報',
    'create_title' => '日報 新規作成',
    'edit_title_with_date' => '日報 編集（:date）',
    'show_title_with_date' => '日報 詳細（:date）',

    'buttons' => [
        'create_and_edit' => '作成して編集へ',
        'update' => '更新',
        'submit' => '提出',
        'edit' => '編集',
        'detail' => '詳細',
        'go_detail' => '詳細へ',
        'back_to_index' => '一覧へ',
        'process' => '処理する',
        'approve' => '承認',
        'reject' => '差戻し',
        'fix_and_resubmit' => '修正して再提出する',
        'add_entry' => '工数を追加',
    ],

    'labels' => [
        'date' => '日付',
        'memo_optional' => 'メモ（任意）',
        'status' => '状態',
        'submitted_at' => '提出',
        'checked_at' => '確認',
        'approver' => '承認者',
        'rejector' => '差戻し者',
        'history' => '履歴',
        'reason' => '理由',
        'approval_actions' => '承認操作',
        'submitted_notice' => 'この日報は提出済みです',
        'total' => '合計',
        'time_entries' => '工数（Time Entries）',
        'project' => '案件',
        'task_optional' => '作業内容（任意）',
        'minutes' => '工数（分）',
        'user' => 'ユーザー',
        'operations' => '操作',
        'sort' => '並び替え',
        'order' => '順序',
        'warning_filter' => '表示',
    ],

    'tabs' => [
        'all' => 'すべて',
        'draft' => '下書き',
        'submitted' => '提出済み（未処理）',
        'approved' => '承認済み',
        'rejected' => '差戻し',
    ],

    'empty' => [
        'reports' => '日報がありません。',
        'history' => '履歴はまだありません。',
        'entries' => 'まだ工数がありません。上のフォームから追加してください。',
    ],

    'notes' => [
        'create_redirect' => '※ 同じ日付の日報が既にある場合は、その編集画面へ移動します（1日1枚ルール）。',
        'approver_submitted_hint' => '承認待ち（提出済み）を表示しています。「処理する」または行クリックで承認パネルへ移動できます。',
        'warnings_explain' => '※ 工数が 0分 または 24時間超 の場合、警告が表示されます。',
    ],

    'warnings' => [
        'only' => '警告のみ',
        'all' => 'すべて',
        'badge_zero' => '⚠ 工数0',
        'badge_over' => '⚠ 24h超',
        'reason_zero' => '工数が0分のため承認できません（未入力の可能性）。',
        'reason_over' => '合計工数が24時間を超えているため承認できません（異常値の可能性）。',
    ],

    'sort' => [
        'date' => '日付',
        'user_name' => 'ユーザー名',
        'total_minutes' => '合計工数',
        'desc' => '降順',
        'asc' => '昇順',
    ],

    'confirm' => [
        'submit' => 'この日報を提出します。提出後は編集できません。よろしいですか？',
        'delete_report' => 'この日報を削除します。よろしいですか？',
        'delete_entry' => 'この工数を削除します。よろしいですか？',
        'approve' => 'この日報を承認します。よろしいですか？',
        'reject' => 'この日報を差戻しします。よろしいですか？',
    ],

    'status' => [
        'draft' => '下書き',
        'submitted' => '提出済み',
        'approved' => '承認済み',
        'rejected' => '差戻し',
    ],

    'action' => [
        'created' => '作成',
        'submitted' => '提出',
        'approved' => '承認',
        'rejected' => '差戻し',
    ],

    'rejection' => [
        'latest' => '差戻し理由（最新）',
        'required' => '差戻し理由（必須）',
        'counter_suffix' => '文字',
        'placeholder' => '差戻し理由を入力してください',
    ],

    'time_entries' => [
        'select_project' => '選択してください',
        'task_placeholder' => '例：構想設計、図面修正、レビュー対応 など',
        'minutes_example' => '例：90（=1.5h） / 480（=8h）',
    ],

    'units' => [
        'minutes' => '分',
    ],


    'misc' => [
        'self' => '（自分）',
    ],
];
