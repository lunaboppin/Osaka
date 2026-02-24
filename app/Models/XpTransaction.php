<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class XpTransaction extends Model
{
    use Auditable;

    protected $fillable = [
        'user_id',
        'action',
        'xp_amount',
        'description',
        'xp_actionable_type',
        'xp_actionable_id',
        'metadata',
    ];

    protected $casts = [
        'xp_amount' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Attributes excluded from audit diffs (too noisy).
     */
    protected array $auditExclude = ['metadata'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function xpActionable()
    {
        return $this->morphTo();
    }

    /**
     * Scope: only backfilled transactions.
     */
    public function scopeBackfilled($query)
    {
        return $query->whereJsonContains('metadata->backfilled', true);
    }

    /**
     * Scope: only organic (non-backfilled) transactions.
     */
    public function scopeOrganic($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('metadata')
              ->orWhereJsonDoesntContain('metadata->backfilled', true);
        });
    }
}
