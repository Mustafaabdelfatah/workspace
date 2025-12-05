<?php
namespace Modules\Core\GraphQL\Queries\User;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class DownloadUserInvitationTemplateQuery extends Query
{
    protected $attributes = [
        'name' => 'downloadUserInvitationTemplate',
    ];

    private $repo;
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('TranslationTemplateResponse');
    }

    public function resolve($root, $args)
    {
         return $this->repo->downloadUserInvitationTemplate();
    }
}
