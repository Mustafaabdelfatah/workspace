<?php

namespace Modules\ProjectManagement\App\Enums;

enum BuildingTypeEnum: string
{
    case HOUSE = 'house';
    case WAREHOUSE = 'warehouse';
    case FACTORY = 'factory';
    case MALL = 'mall';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::HOUSE => 'House',
            self::WAREHOUSE => 'Warehouse',
            self::FACTORY => 'Factory',
            self::MALL => 'Mall',
            self::OTHER => 'Other',
        };
    }
}
