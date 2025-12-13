<?php

namespace Modules\ProjectManagement\App\Models;

use Modules\Core\Models\User;
use Modules\Core\Models\UserGroup;
use Modules\ProjectManagement\App\Enums\ProjectMemberRoleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectInvitation extends Model
{
    protected $fillable = [
        'project_id',
        'invited_user_id',
        'user_group_id',
        'email',
        'role',
        'invitation_date',
        'expires_at',
        'accepted_at',
        'declined_at',
        'invited_by',
        'message',
        'settings',
        'token'
    ];

    protected $casts = [
        'invitation_date' => 'datetime',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'settings' => 'array',
        'role' => ProjectMemberRoleEnum::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Accessors
    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsPendingAttribute(): bool
    {
        return is_null($this->accepted_at) && is_null($this->declined_at) && !$this->is_expired;
    }

    public function getIsAcceptedAttribute(): bool
    {
        return !is_null($this->accepted_at);
    }

    public function getIsDeclinedAttribute(): bool
    {
        return !is_null($this->declined_at);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')
                    ->whereNull('declined_at')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->whereNull('accepted_at')
                    ->whereNull('declined_at')
                    ->where('expires_at', '<=', now());
    }

    // Methods
    public function accept(): bool
    {
        if (!$this->is_pending) {
            return false;
        }

        $this->update(['accepted_at' => now()]);

        // Add user to project if individual invitation
        if ($this->invited_user_id) {
            $this->project->addMember($this->invitedUser, $this->role->value);
        }

        // Add all group users to project if group invitation
        if ($this->user_group_id) {
            foreach ($this->userGroup->users as $user) {
                $this->project->addMember($user, $this->role->value);
            }
        }

        return true;
    }

    public function decline(): bool
    {
        if (!$this->is_pending) {
            return false;
        }

        $this->update(['declined_at' => now()]);
        return true;
    }
}
