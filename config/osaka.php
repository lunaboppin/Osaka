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

    /*
    |--------------------------------------------------------------------------
    | Profile Customisation
    |--------------------------------------------------------------------------
    |
    | Visual customisation options for user profiles — themes, avatar frames,
    | and badge display configuration.
    |
    */

    'profile' => [
        // Preset profile themes. Each theme defines colours used on the
        // public profile card (banner overlay, accent, text colours).
        'themes' => [
            'default' => [
                'label' => 'Default',
                'accent' => '#D4A843',
                'banner_bg' => 'bg-gradient-to-r from-osaka-charcoal to-osaka-charcoal-light',
                'banner_text' => 'text-osaka-cream',
                'card_border' => 'border-osaka-gold/20',
                'min_level' => 1,
            ],
            'osaka_night' => [
                'label' => 'Osaka Night',
                'accent' => '#8B5CF6',
                'banner_bg' => 'bg-gradient-to-r from-violet-950 via-indigo-950 to-slate-900',
                'banner_text' => 'text-violet-100',
                'card_border' => 'border-violet-400/30',
                'min_level' => 3,
            ],
            'cherry_blossom' => [
                'label' => 'Cherry Blossom',
                'accent' => '#EC4899',
                'banner_bg' => 'bg-gradient-to-r from-pink-200 via-rose-200 to-pink-300',
                'banner_text' => 'text-pink-900',
                'card_border' => 'border-pink-300/40',
                'min_level' => 4,
            ],
            'neon' => [
                'label' => 'Neon',
                'accent' => '#22D3EE',
                'banner_bg' => 'bg-gradient-to-r from-gray-900 via-cyan-950 to-gray-900',
                'banner_text' => 'text-cyan-300',
                'card_border' => 'border-cyan-400/30',
                'min_level' => 5,
            ],
            'ember' => [
                'label' => 'Ember',
                'accent' => '#F97316',
                'banner_bg' => 'bg-gradient-to-r from-orange-950 via-red-950 to-amber-950',
                'banner_text' => 'text-orange-200',
                'card_border' => 'border-orange-400/30',
                'min_level' => 6,
            ],
            'arctic' => [
                'label' => 'Arctic',
                'accent' => '#38BDF8',
                'banner_bg' => 'bg-gradient-to-r from-sky-100 via-blue-100 to-indigo-100',
                'banner_text' => 'text-sky-900',
                'card_border' => 'border-sky-300/40',
                'min_level' => 7,
            ],
            'legendary' => [
                'label' => 'Legendary',
                'accent' => '#FBBF24',
                'banner_bg' => 'bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500',
                'banner_text' => 'text-amber-950',
                'card_border' => 'border-amber-400/50',
                'min_level' => 10,
            ],
        ],

        // Avatar frames — decorative borders unlocked by level.
        // CSS classes are applied to the avatar wrapper.
        'avatar_frames' => [
            'none' => [
                'label' => 'None',
                'border_class' => 'border-4 border-osaka-gold/30',
                'ring_class' => '',
                'min_level' => 1,
            ],
            'bronze' => [
                'label' => 'Bronze',
                'border_class' => 'border-4 border-amber-600',
                'ring_class' => 'ring-2 ring-amber-600/30 ring-offset-2 ring-offset-white',
                'min_level' => 2,
            ],
            'silver' => [
                'label' => 'Silver',
                'border_class' => 'border-4 border-gray-400',
                'ring_class' => 'ring-2 ring-gray-400/30 ring-offset-2 ring-offset-white',
                'min_level' => 4,
            ],
            'gold' => [
                'label' => 'Gold',
                'border_class' => 'border-4 border-osaka-gold',
                'ring_class' => 'ring-2 ring-osaka-gold/40 ring-offset-2 ring-offset-white',
                'min_level' => 6,
            ],
            'diamond' => [
                'label' => 'Diamond',
                'border_class' => 'border-4 border-cyan-400',
                'ring_class' => 'ring-2 ring-cyan-400/40 ring-offset-2 ring-offset-white',
                'min_level' => 8,
            ],
            'prismatic' => [
                'label' => 'Prismatic',
                'border_class' => 'border-4 border-transparent bg-clip-border',
                'ring_class' => 'ring-2 ring-purple-400/40 ring-offset-2 ring-offset-white prismatic-border',
                'min_level' => 10,
            ],
        ],

        // Maximum number of badges a user can pin to their profile
        'max_displayed_badges' => 5,
    ],

];
