<?php

namespace Modules\ProjectManagement\App\Models;

use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'start_time',
        'end_time',
        'hours',
        'is_billable',
        'hourly_rate',
        'metadata'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hours' => 'decimal:2',
        'is_billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAmountAttribute(): ?float
    {
        if (!$this->is_billable || !$this->hourly_rate) {
            return null;
        }

        return $this->hours * $this->hourly_rate;
    }

    public function getDurationHumanAttribute(): string
    {
        $hours = floor($this->hours);
        $minutes = ($this->hours - $hours) * 60;

        if ($hours > 0) {
            return $hours . 'h ' . round($minutes) . 'm';
        }

        return round($minutes) . 'm';
    }

    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    public function scopeNonBillable($query)
    {
        return $query->where('is_billable', false);
    }

    public function scopeForTask($query, int $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }
}
