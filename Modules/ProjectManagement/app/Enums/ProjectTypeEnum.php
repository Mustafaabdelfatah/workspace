<?php

namespace Modules\ProjectManagement\App\Enums;

enum ProjectTypeEnum: string
{
    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';
    case INDUSTRIAL = 'industrial';
    case MIXED_USE = 'mixed_use';
    case AGRICULTURAL = 'agricultural';
    case INFRASTRUCTURE = 'infrastructure';
    case PUBLIC_WORKS = 'public_works';
    case RENOVATION = 'renovation';
    case INTERIOR_DESIGN = 'interior_design';

    public function label(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'سكني',
            self::COMMERCIAL => 'تجاري',
            self::INDUSTRIAL => 'صناعي',
            self::MIXED_USE => 'مختلط',
            self::AGRICULTURAL => 'زراعي',
            self::INFRASTRUCTURE => 'بنية تحتية',
            self::PUBLIC_WORKS => 'أشغال عامة',
            self::RENOVATION => 'ترميم',
            self::INTERIOR_DESIGN => 'تشطيب داخلي',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'home',
            self::COMMERCIAL => 'store',
            self::INDUSTRIAL => 'factory',
            self::MIXED_USE => 'building',
            self::AGRICULTURAL => 'tractor',
            self::INFRASTRUCTURE => 'road',
            self::PUBLIC_WORKS => 'hammer-wrench',
            self::RENOVATION => 'brush',
            self::INTERIOR_DESIGN => 'sofa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RESIDENTIAL => 'blue',
            self::COMMERCIAL => 'green',
            self::INDUSTRIAL => 'orange',
            self::MIXED_USE => 'purple',
            self::AGRICULTURAL => 'lime',
            self::INFRASTRUCTURE => 'gray',
            self::PUBLIC_WORKS => 'yellow',
            self::RENOVATION => 'pink',
            self::INTERIOR_DESIGN => 'indigo',
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($type) => [$type->value => $type->label()])->toArray();
    }
}
