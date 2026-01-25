<?php

/**
 * Compatibility layer.
 * Old keys like __('ui.nav.dashboard') will keep working by reading the new split files.
 * Once you migrate all views to new keys (nav.*, common.*, ...), you can delete this file.
 */
return [
    'nav' => require __DIR__ . '/nav.php',
    'common' => require __DIR__ . '/common.php',
    'dashboard' => require __DIR__ . '/dashboard.php',
    'profile' => require __DIR__ . '/profile.php',
    'projects' => require __DIR__ . '/projects.php',
    'admin' => require __DIR__ . '/admin.php',
];
