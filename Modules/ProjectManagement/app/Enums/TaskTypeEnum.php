<?php

namespace Modules\ProjectManagement\App\Enums;

enum TaskTypeEnum: string
{
    case TASK = 'task';
    case BUG = 'bug';
    case FEATURE = 'feature';
    case IMPROVEMENT = 'improvement';

    public function label(): string
    {
        return match($this) {
            self::TASK => 'مهمة',
            self::BUG => 'خطأ',
            self::FEATURE => 'ميزة جديدة',
            self::IMPROVEMENT => 'تحسين',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TASK => '📝',
            self::BUG => '🐛',
            self::FEATURE => '⭐',
            self::IMPROVEMENT => '🔧',
        };
    }
}
