<?php

namespace Modules\ProjectManagement\App\Enums;

enum ProjectTypeEnum: string
{
    case RESIDENTIAL = 'residential';
    case COMMERCIAL = 'commercial';
    case INDUSTRIAL = 'industrial';
    case INFRASTRUCTURE = 'infrastructure';
    case HOTELS = 'hotels';
    case HOSPITAL = 'hospital';
    case EDUCATION = 'education';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::RESIDENTIAL => 'Residential',
            self::COMMERCIAL => 'Commercial',
            self::INDUSTRIAL => 'Industrial',
            self::INFRASTRUCTURE => 'Infrastructure',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::RESIDENTIAL => 'Houses, apartments, residential complexes',
            self::COMMERCIAL => 'Offices, shops, malls, hotels',
            self::INDUSTRIAL => 'Factories, warehouses, manufacturing facilities',
            self::INFRASTRUCTURE => 'Roads, bridges, utilities',
            self::OTHER => 'Custom project type',
        };
    }
}