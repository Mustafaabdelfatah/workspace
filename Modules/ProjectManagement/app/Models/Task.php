<?php

namespace Modules\ProjectManagement\App\Models;

use Modules\Core\Models\User;
use Modules\ProjectManagement\App\Enums\TaskStatusEnum;
use Modules\ProjectManagement\App\Enums\TaskPriorityEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'parent_task_id',
        'title',
        'code',
        'description',
        'status',
        'priority',
        'assigned_to',
        'created_by',
        'start_date',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress_percentage',
        'tags',
        'settings'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'status' => TaskStatusEnum::class,
        'priority' => TaskPriorityEnum::class,
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'progress_percentage' => 'integer',
        'tags' => 'array',
        'settings' => 'array'
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'priority_label',
        'priority_color',
        'is_overdue',
        'days_remaining'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getPriorityLabelAttribute(): string
    {
        return $this->priority->label();
    }

    public function getPriorityColorAttribute(): string
    {
        return $this->priority->color();
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== TaskStatusEnum::COMPLETED;
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->due_date || $this->status === TaskStatusEnum::COMPLETED) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === TaskStatusEnum::COMPLETED;
    }

    public function getTotalSubTasksAttribute(): int
    {
        return $this->subTasks()->count();
    }

    public function getCompletedSubTasksAttribute(): int
    {
        return $this->subTasks()->where('status', TaskStatusEnum::COMPLETED->value)->count();
    }

    public function getSubTaskCompletionPercentageAttribute(): int
    {
        $total = $this->total_sub_tasks;
        if ($total === 0) return 100;

        return round(($this->completed_sub_tasks / $total) * 100);
    }

    // Scopes
    public function scopeInProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByStatus($query, TaskStatusEnum $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopeByPriority($query, TaskPriorityEnum $priority)
    {
        return $query->where('priority', $priority->value);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', [TaskStatusEnum::COMPLETED->value, TaskStatusEnum::CANCELLED->value]);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                    ->whereNotIn('status', [TaskStatusEnum::COMPLETED->value, TaskStatusEnum::CANCELLED->value]);
    }

    public function scopeMainTasks($query)
    {
        return $query->whereNull('parent_task_id');
    }

    public function scopeSubTasks($query)
    {
        return $query->whereNotNull('parent_task_id');
    }

    // Methods
    public static function generateCode(int $projectId): string
    {
        $project = Project::find($projectId);
        $lastTask = self::where('project_id', $projectId)->latest()->first();

        $nextNumber = $lastTask ?
            (int) substr($lastTask->code, strlen($project->code) + 2) + 1 : 1;

        return $project->code . '-T' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function canTransitionTo(TaskStatusEnum $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function transitionTo(TaskStatusEnum $newStatus, ?string $note = null): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;

        if ($newStatus === TaskStatusEnum::COMPLETED) {
            $this->completed_at = now();
            $this->progress_percentage = 100;
        } elseif ($oldStatus === TaskStatusEnum::COMPLETED && $newStatus !== TaskStatusEnum::COMPLETED) {
            $this->completed_at = null;
        }

        return $this->save();
    }

    public function updateProgress(int $percentage): bool
    {
        $percentage = max(0, min(100, $percentage));
        $this->progress_percentage = $percentage;

        if ($percentage === 100 && $this->status !== TaskStatusEnum::COMPLETED) {
            $this->transitionTo(TaskStatusEnum::COMPLETED);
        } elseif ($percentage > 0 && $this->status === TaskStatusEnum::PENDING) {
            $this->transitionTo(TaskStatusEnum::IN_PROGRESS);
        }

        return $this->save();
    }

    public function assignTo(?User $user): bool
    {
        $this->assigned_to = $user?->id;
        return $this->save();
    }

    public function getTotalTimeSpent(): float
    {
        return $this->timeEntries()->sum('hours');
    }

    public function getEfficiencyPercentage(): ?float
    {
        if (!$this->estimated_hours || $this->estimated_hours <= 0) {
            return null;
        }

        $totalTime = $this->getTotalTimeSpent();
        return ($this->estimated_hours / max($totalTime, 0.1)) * 100;
    }
}
