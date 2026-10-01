<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $casts = [
        'last_backup_at' => 'datetime',
        'reminder_dismissed_at' => 'datetime',
        'reminder_snooze_days' => 'integer',
    ];

    protected $fillable = ['last_backup_at', 'reminder_dismissed_at', 'reminder_snooze_days'];

    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function needsReminder(): bool
    {
        if ($this->last_backup_at === null) {
            return true;
        }

        return $this->last_backup_at->diffInDays(now()) >= 14;
    }

    public function getSnoozeUntilAttribute(): ?\Carbon\CarbonInterface
    {
        if ($this->reminder_dismissed_at === null) return null;
        $days = (int) ($this->reminder_snooze_days ?? 14);
        if ($days < 1) $days = 14;
        return $this->reminder_dismissed_at->copy()->addDays($days);
    }

    public function isDismissed(): bool
    {
        if ($this->reminder_dismissed_at === null) return false;
        $until = $this->snooze_until;
        if (!$until) return false;
        return now()->lt($until);
    }

    public function shouldShowWarning(): bool
    {
        return $this->needsReminder() && ! $this->isDismissed();
    }
}
