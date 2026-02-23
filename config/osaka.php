<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sticker Reminder Settings
    |--------------------------------------------------------------------------
    |
    | Configure how the aging / reminder system behaves. The threshold
    | determines how many days since a sticker was last checked before
    | it appears in the reminders dashboard. The warning threshold is
    | an earlier "heads up" tier.
    |
    */

    'reminders' => [
        // Default number of days before a pin is considered "overdue"
        'overdue_days' => (int) env('OSAKA_REMINDER_OVERDUE_DAYS', 30),

        // Number of days before a pin enters the "warning" tier (approaching overdue)
        'warning_days' => (int) env('OSAKA_REMINDER_WARNING_DAYS', 20),

        // Maximum configurable threshold (for the UI slider)
        'max_days' => (int) env('OSAKA_REMINDER_MAX_DAYS', 90),

        // Minimum configurable threshold (for the UI slider)
        'min_days' => (int) env('OSAKA_REMINDER_MIN_DAYS', 7),
    ],

];
