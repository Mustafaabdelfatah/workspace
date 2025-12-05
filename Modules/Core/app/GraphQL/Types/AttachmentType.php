<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachmentType extends GraphQLType
{
    protected $attributes = [
        'name' => 'transactionAttachmentType',
        'description' => 'this type defines attachment type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ],
            'title' => [
                'type' => Type::string(),
            ],
            'attach_with_document' => [
                'type' => Type::boolean(),
            ],
            'attach_with_official_paper' => [
                'type' => Type::boolean(),
            ],
            'attachment' => [
                'type' => GraphQL::type('attachmentByUrlType'),
            ]
        ];
    }
}
