<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the Auditable trait — register model event listeners.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            $excluded = $model->getAuditExcludedAttributes();

            // Filter out excluded attributes and timestamps
            $changed = array_diff_key($dirty, array_flip($excluded), array_flip(['updated_at', 'created_at']));

            if (empty($changed)) {
                return;
            }

            $oldValues = [];
            $newValues = [];

            foreach ($changed as $key => $value) {
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
            }

            $model->logAudit('updated', null, $oldValues, $newValues);
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', null, $model->getAuditableAttributes());
        });
    }

    /**
     * Get the morph relationship to audit logs.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Attributes to exclude from audit logging.
     * Override this in the model to customize.
     */
    public function getAuditExcludedAttributes(): array
    {
        return property_exists($this, 'auditExclude') ? $this->auditExclude : [];
    }

    /**
     * Get the attributes that should be logged, minus excluded ones.
     */
    protected function getAuditableAttributes(): array
    {
        $excluded = array_merge(
            $this->getAuditExcludedAttributes(),
            ['updated_at', 'created_at']
        );

        return array_diff_key($this->attributesToArray(), array_flip($excluded));
    }

    /**
     * Generate a human-readable description for the audit entry.
     */
    protected function getAuditDescription(string $action): string
    {
        $modelName = class_basename(static::class);
        $identifier = $this->getAttribute('title')
            ?? $this->getAttribute('display_name')
            ?? $this->getAttribute('name')
            ?? $this->getAttribute('email')
            ?? "#{$this->getKey()}";

        return ucfirst($action) . " {$modelName}: {$identifier}";
    }

    /**
     * Write an audit log entry for this model.
     */
    protected function logAudit(string $action, ?array $newValues = null, ?array $oldValues = null, ?array $changedNew = null): void
    {
        // For 'updated' events, we pass old/new explicitly
        if ($action === 'updated' && $changedNew !== null) {
            $finalOld = $oldValues;
            $finalNew = $changedNew;
        } elseif ($action === 'created') {
            $finalOld = null;
            $finalNew = $newValues;
        } else {
            // deleted
            $finalOld = $oldValues ?? $newValues;
            $finalNew = null;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'description' => $this->getAuditDescription($action),
            'old_values' => $finalOld,
            'new_values' => $finalNew,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
