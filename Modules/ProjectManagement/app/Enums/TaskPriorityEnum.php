<?php

namespace Modules\ProjectManagement\App\Enums;

enum TaskPriorityEnum: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'منخفض',
            self::MEDIUM => 'متوسط',
            self::HIGH => 'عالٍ',
            self::CRITICAL => 'حرج',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'bg-gray-100 text-gray-800',
            self::MEDIUM => 'bg-blue-100 text-blue-800',
            self::HIGH => 'bg-orange-100 text-orange-800',
            self::CRITICAL => 'bg-red-100 text-red-800',
        };
    }

    public function level(): int
    {
        return match($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::CRITICAL => 4,
        };
    }
}
