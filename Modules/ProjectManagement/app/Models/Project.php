<?php

namespace Modules\ProjectManagement\App\Models;

use Modules\Core\Models\User;
use Modules\Core\Models\Workspace;
use Modules\ProjectManagement\App\Models\Task;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ProjectManagement\App\Models\ProjectMember;
use Modules\ProjectManagement\App\Enums\ProjectStatus;
use Modules\ProjectManagement\App\Enums\ProjectStatusEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;

class Project extends BaseModel
{
    use SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'workspace_id',
        'name',
        'code',
        'description',
        'status',
        'owner_id',
        'manager_id',
        'parent_project_id',
        'project_type',
        'building_type',
        // 'company_id', //
        // 'company_position_id', //
        'start_date',
        'end_date',
        'settings',
        'latitude',
        'longitude',
        'area',
        'area_unit'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'settings' => 'array',
        'name' => 'array',
        'description' => 'array',
        'status' => ProjectStatusEnum::class,
        'project_type' => ProjectTypeEnum::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'area' => 'decimal:2',
    ];

    protected $appends = ['status_label', 'status_color', 'project_type_label'];

    // Relationships
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function parentProject()
    {
        return $this->belongsTo(Project::class, 'parent_project_id');
    }

    public function subProjects()
    {
        return $this->hasMany(Project::class, 'parent_project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    // Note: Add these relationships when company models are available
    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }

    // public function companyPosition()
    // {
    //     return $this->belongsTo(CompanyPosition::class);
    // }

    // Accessors & Mutators
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getProjectTypeLabelAttribute(): string
    {
        return $this->project_type ? $this->project_type->label() : '';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === ProjectStatusEnum::ACTIVE;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === ProjectStatusEnum::COMPLETED;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ProjectStatusEnum::ACTIVE->value);
    }

    public function scopeInWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('owner_id', $userId);
    }

    public static function generateCode($workspaceId): string
    {
        $lastProject = self::where('workspace_id', $workspaceId)
                          ->latest()
                          ->first();

        $nextNumber = $lastProject ?
            (int) substr($lastProject->code, 4) + 1 : 1;

        return 'PRJ-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function addMember(User $user, $role = 'member'): void
    {
        if (!$this->hasMember($user)) {
            $this->members()->attach($user->id, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    public function removeMember(User $user): void
    {
        $this->members()->detach($user->id);
    }
}
