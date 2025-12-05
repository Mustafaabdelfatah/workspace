<?php

namespace Modules\ProjectManagement\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\ProjectManagement\App\Enums\ProjectMemberRole;
use Modules\ProjectManagement\App\Enums\ProjectMemberRoleEnum;

class ProjectMember extends Pivot
{
    protected $table = 'project_members';

    protected $casts = [
        'joined_at' => 'datetime',
        'role' => ProjectMemberRoleEnum::class,
    ];

    protected $fillable = [
        'project_id',
        'user_id',
        'role',
        'joined_at'
    ];

    // Accessors
    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    public function getCanManageAttribute(): bool
    {
        return in_array($this->role, [ProjectMemberRoleEnum::OWNER, ProjectMemberRoleEnum::ADMIN]);
    }

    public function getCanEditAttribute(): bool
    {
        return $this->role !== ProjectMemberRoleEnum::VIEWER;
    }
}
