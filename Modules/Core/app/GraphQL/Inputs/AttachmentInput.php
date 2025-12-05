<?php
namespace Modules\Core\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class AttachmentInput extends InputType
{
    protected $attributes = [
        'name' => 'attachmentInput',
        'description' => 'this input defines attachment inputs'
    ];

    public function fields(): array
    {
        return [
            'title' => [
                'type' => Type::string(),
            ],
            'attachment' => [
                'type' => GraphQL::type('Upload'),
            ],
            'attach_with_document' => [
                'type' => Type::boolean(),
            ],
            'attach_with_official_paper' => [
                'type' => Type::boolean(),
            ]
        ];
    }
}
