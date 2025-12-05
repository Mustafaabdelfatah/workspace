<?php
// Modules/ProjectManagement/App/Models/Task.php

namespace Modules\ProjectManagement\App\Models;

use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ProjectManagement\Models\TimeEntry;
use Modules\ProjectManagement\App\Enums\TaskType;
use Modules\ProjectManagement\App\Enums\TaskStatus;
use Modules\ProjectManagement\App\Enums\TaskPriority;
use Modules\ProjectManagement\App\Enums\TaskTypeEnum;
use Modules\ProjectManagement\App\Enums\TaskStatusEnum;
use Modules\ProjectManagement\App\Enums\TaskPriorityEnum;

class Task extends BaseModel
{
    use SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'task_code',
        'type',
        'priority',
        'status',
        'assignee_id',
        'reporter_id',
        'story_points',
        'estimated_hours',
        'actual_hours',
        'due_date',
        'start_date',
        'completed_at',
        'parent_task_id',
        'position'
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'completed_at' => 'datetime',
        'story_points' => 'integer',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'position' => 'integer',
        'status' => TaskStatusEnum::class,
        'priority' => TaskPriorityEnum::class,
        'type' => TaskTypeEnum::class,
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'priority_label',
        'priority_color',
        'type_label',
        'is_overdue'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id')->orderBy('position');
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

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->status === TaskStatusEnum::DONE) {
            return false;
        }

        return $this->due_date->isPast();
    }

    public function getProgressAttribute(): float
    {
        if ($this->estimated_hours > 0) {
            return min(100, ($this->actual_hours / $this->estimated_hours) * 100);
        }

        return 0;
    }

    // Scopes
    public function scopeInProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assignee_id', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', [TaskStatusEnum::DONE->value, TaskStatusEnum::CANCELLED->value]);
    }

    // Business Logic
    public static function generateCode($projectId): string
    {
        $lastTask = self::where('project_id', $projectId)
                       ->latest()
                       ->first();

        $nextNumber = $lastTask ?
            (int) substr($lastTask->task_code, 4) + 1 : 1;

        return 'TSK-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => TaskStatusEnum::DONE->value,
            'completed_at' => now(),
        ]);
    }

    public function addTimeEntry($hours, $description = null): TimeEntry
    {
        return $this->timeEntries()->create([
            'user_id' => auth()->id(),
            'hours' => $hours,
            'description' => $description,
            'logged_at' => now(),
        ]);
    }
}