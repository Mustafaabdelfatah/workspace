<?php

namespace Modules\ProjectManagement\App\Enums;

enum ProjectMemberRoleEnum: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::OWNER => 'مالك',
            self::ADMIN => 'مشرف',
            self::MEMBER => 'عضو',
            self::VIEWER => 'مشاهد',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::OWNER => ['create', 'read', 'update', 'delete', 'manage_members', 'manage_tasks'],
            self::ADMIN => ['create', 'read', 'update', 'manage_tasks'],
            self::MEMBER => ['create', 'read', 'update'],
            self::VIEWER => ['read'],
        };
    }

    public function can(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }
}
