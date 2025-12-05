<?php

namespace Modules\ProjectManagement\App\Enums;

enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case REVIEW = 'review';
    case DONE = 'done';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::TODO => 'قيد الانتظار',
            self::IN_PROGRESS => 'قيد التنفيذ',
            self::REVIEW => 'قيد المراجعة',
            self::DONE => 'مكتمل',
            self::BLOCKED => 'متوقف',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::TODO => 'bg-gray-100 text-gray-800',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::REVIEW => 'bg-yellow-100 text-yellow-800',
            self::DONE => 'bg-green-100 text-green-800',
            self::BLOCKED => 'bg-red-100 text-red-800',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TODO => '⏳',
            self::IN_PROGRESS => '⚡',
            self::REVIEW => '👁️',
            self::DONE => '✅',
            self::BLOCKED => '🚫',
        };
    }
}