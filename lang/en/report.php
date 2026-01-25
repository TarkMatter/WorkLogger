<?php

/**
 * Compatibility layer.
 * Old keys like __('report.status.draft') will keep working.
 * Once you migrate to __('reports.status.draft'), you can delete this file.
 */
return require __DIR__ . '/reports.php';
