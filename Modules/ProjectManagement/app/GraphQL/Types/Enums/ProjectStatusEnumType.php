<?php
namespace Modules\ProjectManagement\App\GraphQL\Types\Enums;

use GraphQL\Type\Definition\EnumType;
use Modules\ProjectManagement\App\Enums\ProjectStatus;
use Modules\ProjectManagement\App\Enums\ProjectStatusEnum;

class ProjectStatusEnumType extends EnumType
{
    public function __construct()
    {
        $config = [
            'name' => 'ProjectStatus',
            'values' => [
                'PLANNING' => [
                    'value' => ProjectStatusEnum::PLANNING->value,
                ],
                'ACTIVE' => [
                    'value' => ProjectStatusEnum::ACTIVE->value,
                ],
                'ON_HOLD' => [
                    'value' => ProjectStatusEnum::ON_HOLD->value,
                ],
                'COMPLETED' => [
                    'value' => ProjectStatusEnum::COMPLETED->value,
                ],
                'CANCELLED' => [
                    'value' => ProjectStatusEnum::CANCELLED->value,
                ],
            ]
        ];

        parent::__construct($config);
    }
}
