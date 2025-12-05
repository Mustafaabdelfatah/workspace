<?php


namespace Modules\Core\GraphQL\Mutations\Notification;


use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\NotificationRepository;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\Validator;

class SendNotificationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'sendNotification',
    ];
    private  $repo;

    public function __construct(NotificationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'title' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
            'body' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
            'image_url' => [
                'type' => Type::string(),
            ],
            'link_url' => [
                'type' => Type::string(),
            ],
            'image' => [
                'type' => GraphQL::type('Upload'),
            ],
            'file' => [
                'type' => GraphQL::type('Upload'),
            ],
            'user_ids' => [
                'type' => Type::listOf(Type::int()),
            ],
            ];
    }

    public function resolve($root, array $args)
    {
        $validator = Validator::make($args, [
            'title' => 'required|array',
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'body' => 'required|array',
            'body.en' => 'required|string',
            'body.ar' => 'required|string',
            'image_url' => 'nullable|string',
            'link_url' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20000',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->errors()->first()];
        }

        return $this->repo->sendNotification($args);
    }
}
