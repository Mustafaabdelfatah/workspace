<?php

namespace Modules\ProjectManagement\App\Enums;

enum TaskStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case REVIEW = 'review';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case ON_HOLD = 'on_hold';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::REVIEW => 'Under Review',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::ON_HOLD => 'On Hold',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => '#6B7280',
            self::IN_PROGRESS => '#3B82F6',
            self::REVIEW => '#F59E0B',
            self::COMPLETED => '#10B981',
            self::CANCELLED => '#EF4444',
            self::ON_HOLD => '#8B5CF6',
        };
    }

    public function canTransitionTo(TaskStatusEnum $newStatus): bool
    {
        return match($this) {
            self::PENDING => in_array($newStatus, [self::IN_PROGRESS, self::CANCELLED]),
            self::IN_PROGRESS => in_array($newStatus, [self::REVIEW, self::COMPLETED, self::ON_HOLD, self::CANCELLED]),
            self::REVIEW => in_array($newStatus, [self::IN_PROGRESS, self::COMPLETED, self::CANCELLED]),
            self::ON_HOLD => in_array($newStatus, [self::IN_PROGRESS, self::CANCELLED]),
            self::COMPLETED => false,
            self::CANCELLED => in_array($newStatus, [self::PENDING]),
        };
    }
}
