<?php

return [
    // ここに対応言語を追加していく（表示UIも自動で増える）
    'supported' => [
        'ja',
        'en',
        // 例: 'fr', 'de', 'zh_CN'
    ],

    // 万一不正な値が来たときのフォールバック
    'fallback' => 'en',

    'formats' => [
        // for <x-datetime type="date" ... />
        'date' => [
            'ja' => 'Y-m-d',
            'en' => 'M j, Y',        // Jan 12, 2026
        ],

        // default: <x-datetime ... />
        'datetime' => [
            'ja' => 'Y-m-d H:i',
            'en' => 'M j, Y H:i',    // Jan 12, 2026 06:56
        ],
    ],
];
