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
        'overdue_days' => (int) env('OSAKA_REMINDER_OVERDUE_DAYS', 250),

        // Number of days before a pin enters the "warning" tier (approaching overdue)
        'warning_days' => (int) env('OSAKA_REMINDER_WARNING_DAYS', 150),

        // Maximum configurable threshold (for the UI slider)
        'max_days' => (int) env('OSAKA_REMINDER_MAX_DAYS', 250),

        // Minimum configurable threshold (for the UI slider)
        'min_days' => (int) env('OSAKA_REMINDER_MIN_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | XP & Levelling System
    |--------------------------------------------------------------------------
    |
    | Configure how much XP each action rewards and the level thresholds.
    | Levels use escalating thresholds (RPG-style). The names array must
    | have the same number of entries as the thresholds array.
    |
    */

    'xp' => [
        // XP awarded per action (negative values = deductions)
        'amounts' => [
            'pin_created' => 10,
            'pin_updated' => 5,       // Status or photo change via edit
            'update_posted' => 5,     // Timeline update posted
            'photo_added' => 3,       // Bonus when a photo is attached
            'pin_checked' => 2,       // Marking a pin as checked
            'pin_deleted' => -10,     // Deduction on pin delete
            'profile_completed' => 1, // One-time: bio + avatar both set
        ],

        // Cumulative XP thresholds for each level (index = level - 1)
        'thresholds' => [0, 50, 150, 300, 500, 750, 1100, 1500, 2000, 2750, 3500],

        // Human-readable rank names (must match thresholds count)
        'names' => [
            'Newcomer',      // Level 1: 0 XP
            'Scout',         // Level 2: 50 XP
            'Explorer',      // Level 3: 150 XP
            'Contributor',   // Level 4: 300 XP
            'Pathfinder',    // Level 5: 500 XP
            'Cartographer',  // Level 6: 750 XP
            'Trailblazer',   // Level 7: 1100 XP
            'Veteran',       // Level 8: 1500 XP
            'Champion',      // Level 9: 2000 XP
            'Legend',        // Level 10: 2750 XP
            'Grandmaster',   // Level 11: 3500 XP
        ],
    ],

];
