<?php

return [
    'title' => 'Daily Reports',
    'create_title' => 'Create Daily Report',
    'edit_title_with_date' => 'Edit Daily Report (:date)',
    'show_title_with_date' => 'Daily Report Detail (:date)',

    'buttons' => [
        'create_and_edit' => 'Create & Edit',
        'update' => 'Update',
        'submit' => 'Submit',
        'edit' => 'Edit',
        'detail' => 'Detail',
        'go_detail' => 'Detail',
        'back_to_index' => 'Back to list',
        'process' => 'Review',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'fix_and_resubmit' => 'Fix and resubmit',
        'add_entry' => 'Add time entry',
    ],

    'labels' => [
        'date' => 'Date',
        'memo_optional' => 'Memo (Optional)',
        'status' => 'Status',
        'submitted_at' => 'Submitted',
        'checked_at' => 'Checked',
        'approver' => 'Approver',
        'rejector' => 'Rejector',
        'history' => 'History',
        'reason' => 'Reason',
        'approval_actions' => 'Approval Actions',
        'submitted_notice' => 'This report is submitted.',
        'total' => 'Total',
        'time_entries' => 'Time Entries',
        'project' => 'Project',
        'task_optional' => 'Task (Optional)',
        'minutes' => 'Minutes',
        'user' => 'User',
        'operations' => 'Actions',
        'sort' => 'Sort',
        'order' => 'Order',
        'warning_filter' => 'Filter',
    ],

    'tabs' => [
        'all' => 'All',
        'draft' => 'Draft',
        'submitted' => 'Submitted (Pending)',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],

    'empty' => [
        'reports' => 'No reports.',
        'history' => 'No history yet.',
        'entries' => 'No time entries yet. Add one above.',
    ],

    'notes' => [
        'create_redirect' => 'If a report for the same date already exists, you will be redirected to its edit page (one per day).',
        'approver_submitted_hint' => 'Showing submitted (pending) reports. Click “Review” or the row to jump to the approval panel.',
        'warnings_explain' => 'Warnings appear when total minutes are 0 or over 24 hours.',
    ],

    'warnings' => [
        'only' => 'Warnings only',
        'all' => 'All',
        'badge_zero' => '⚠ 0 min',
        'badge_over' => '⚠ >24h',
        'reason_zero' => 'Cannot approve because total minutes are 0 (possibly missing entries).',
        'reason_over' => 'Cannot approve because total exceeds 24 hours (possibly invalid).',
    ],

    'sort' => [
        'date' => 'Date',
        'user_name' => 'User',
        'total_minutes' => 'Total minutes',
        'desc' => 'Desc',
        'asc' => 'Asc',
    ],

    'confirm' => [
        'submit' => 'Submit this report? You cannot edit it after submission.',
        'delete_report' => 'Delete this report?',
        'delete_entry' => 'Delete this time entry?',
        'approve' => 'Approve this report?',
        'reject' => 'Reject this report?',
    ],

    'status' => [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],

    'action' => [
        'created' => 'Created',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ],

    'rejection' => [
        'latest' => 'Rejection Reason (Latest)',
        'required' => 'Rejection Reason (Required)',
        'counter_suffix' => ' chars',
        'placeholder' => 'Enter a reason',
    ],

    'time_entries' => [
        'select_project' => 'Select a project',
        'task_placeholder' => 'e.g. design, drafting, review…',
        'minutes_example' => 'e.g. 90 (=1.5h) / 480 (=8h)',
    ],

    'units' => [
        'minutes' => 'min',
    ],


    'misc' => [
        'self' => '(me)',
    ],
];
