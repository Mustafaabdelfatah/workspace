<?php

namespace Modules\ProjectManagement\App\Enums;

enum ProjectStatusEnum: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PLANNING => 'قيد التخطيط',
            self::ACTIVE => 'نشط',
            self::ON_HOLD => 'متوقف',
            self::COMPLETED => 'مكتمل',
            self::CANCELLED => 'ملغي',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PLANNING => 'bg-gray-100 text-gray-800',
            self::ACTIVE => 'bg-green-100 text-green-800',
            self::ON_HOLD => 'bg-yellow-100 text-yellow-800',
            self::COMPLETED => 'bg-blue-100 text-blue-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
