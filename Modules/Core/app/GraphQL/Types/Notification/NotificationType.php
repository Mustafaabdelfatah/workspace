<?php
namespace Modules\Core\GraphQL\Types\Notification;


use GraphQL\Type\Definition\Type;
use Modules\Core\Models\Notification;
use Modules\Core\Models\User;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class NotificationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Notification',
        'description' => 'A notification object',
        'model' => Notification::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'title' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => fn($root, $args, $context) => $root->getTranslations('title'),
            ],
            'body' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => fn($root, $args, $context) => $root->getTranslations('body'),
            ],
            'image_url' => [
                'type' => Type::string(),
                'resolve' => fn($root) => $root->getProcessedImage(),
            ],
            'link_url' => [
                'type' => Type::string(),
                'resolve' => fn($root) => $root->getProcessedLink(),
            ],
            'action_type' => [
                'type' => Type::string(),
                'resolve' => fn($root) => $root->getActionType(),
            ],
            'data' => [
                'type' => Type::string(), 
                'resolve' => fn($root) => null
            ],
            'is_read' => [
                'type' => Type::boolean(),
                'resolve' => function ($root, $args, $context) {
                    $user = auth()->user();
                    return $root->isReadBy($user);
                },
            ],
            'sender' => [
                'type' => Type::string(),
                'resolve' => fn($root) => $root->sender?->full_name,
            ],
            'created_at' => [
                'type' => Type::string(),
                'resolve' => fn($root) => $root->created_at->toISOString(),
            ],
        ];
    }
}
