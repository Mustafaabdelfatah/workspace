<?php

namespace Modules\ProjectManagement\App\Enums;

enum UserTypeEnum: string
{
    case CONTRACTOR = 'contractor';
    case CONSULTANT = 'consultant';
    case DEVELOPER_OWNER = 'developer_owner';

    public function label(): string
    {
        return match($this) {
            self::CONTRACTOR => 'Contractor',
            self::CONSULTANT => 'Consultant',
            self::DEVELOPER_OWNER => 'Developer/Owner',
        };
    }
}
