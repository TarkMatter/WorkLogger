<?php

return [
    'title' => 'Projects',
    'create_title' => 'Create Project',
    'edit_title' => 'Edit Project',
    'detail_title' => 'Project Detail',

    'labels' => [
        'code' => 'Code',
        'name' => 'Name',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'description' => 'Description',
    ],

    'empty' => 'No projects.',

    'flash' => [
        'created' => 'Project created.',
        'updated' => 'Project updated.',
        'deleted' => 'Project deleted.',
        'cannot_delete_in_use' => 'This project cannot be deleted because it is used in time entries.',
    ],

    'confirm' => [
        'delete' => 'Are you sure you want to delete?',
    ],

    'validation' => [
        'end_date_after_or_equal' => 'The end date must be on or after the start date.',
    ],

    'unset' => '(Not set)',
];
