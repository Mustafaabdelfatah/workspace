<?php

namespace Modules\ProjectManagement\App\Enums;

enum BuildingTypeEnum: string
{

    case APARTMENT = 'apartment';
    case VILLA = 'villa';
    case DUPLEX = 'duplex';
    case TOWNHOUSE = 'townhouse';
    case RESIDENTIAL_COMPLEX = 'residential_complex';
    case MALL = 'mall';
    case OFFICE_BUILDING = 'office_building';
    case HOTEL = 'hotel';
    case HOSPITAL = 'hospital';
    case SCHOOL = 'school';
    case FACTORY = 'factory';
    case WAREHOUSE = 'warehouse';
    case WORKSHOP = 'workshop';
    case MOSQUE = 'mosque';
    case ADMINISTRATIVE = 'administrative';
    case GOVERNMENTAL = 'governmental';

    public function label(): string
    {
        return match($this) {
            self::APARTMENT => 'شقة',
            self::VILLA => 'فيلا',
            self::DUPLEX => 'دوبلكس',
            self::TOWNHOUSE => 'تاون هاوس',
            self::RESIDENTIAL_COMPLEX => 'مجمع سكني',
            self::MALL => 'مول',
            self::OFFICE_BUILDING => 'مبنى مكاتب',
            self::HOTEL => 'فندق',
            self::HOSPITAL => 'مستشفى',
            self::SCHOOL => 'مدرسة',
            self::FACTORY => 'مصنع',
            self::WAREHOUSE => 'مستودع',
            self::WORKSHOP => 'ورشة',
            self::MOSQUE => 'مسجد',
            self::ADMINISTRATIVE => 'إداري',
            self::GOVERNMENTAL => 'حكومي',
        };
    }

    public function category(): string
    {
        return match($this) {
            self::APARTMENT,
            self::VILLA,
            self::DUPLEX,
            self::TOWNHOUSE,
            self::RESIDENTIAL_COMPLEX => 'سكني',

            self::MALL,
            self::OFFICE_BUILDING,
            self::HOTEL => 'تجاري',

            self::HOSPITAL,
            self::SCHOOL => 'خدمي',

            self::FACTORY,
            self::WAREHOUSE,
            self::WORKSHOP => 'صناعي',

            self::MOSQUE => 'ديني',

            self::ADMINISTRATIVE,
            self::GOVERNMENTAL => 'حكومي',
        };
    }
}