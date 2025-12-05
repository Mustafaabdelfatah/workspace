<?php
namespace Modules\Core\GraphQL\Types ;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AttachmentsUploadHistoryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AttachmentsUploadHistory'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ],
            'attachments_url' => [
                'type' => \GraphQL::type('FileData'),
            ],
            'attachments_type' => [
                'type' => Type::string(),
            ],
            'attachments_size' => [
                'type' => Type::string(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
            'writer_name' => [
                'type' => Type::string(),
                'description' => 'Full name of the writer with ID',
                'resolve'     => function ($model){
                    return $model->writer ? $model->writer->full_name : null ;
                }
            ],
            'editor_name' => [
                'type' => Type::string(),
                'description' => 'Full name of the editor with ID',
                'resolve'     => function ($model){
                    return $model->editor ? $model->editor->full_name : null ;
                }
            ],
        ];
    }
}

