<?php

namespace Modules\ProjectManagement\App\Enums;

enum TaskPriorityEnum: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => '#10B981',
            self::MEDIUM => '#F59E0B',
            self::HIGH => '#F97316',
            self::URGENT => '#EF4444',
        };
    }

    public function weight(): int
    {
        return match($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }
}
