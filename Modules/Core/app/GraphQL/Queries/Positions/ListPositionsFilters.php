<?php
declare(strict_types=1);
namespace Modules\Core\GraphQL\Queries\Positions;
use Closure;

use App\SOLID\Traits\AuthTraits;
use Illuminate\Support\Facades\Auth;
use Modules\Law\Entities\Group;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Position;

class ListPositionsFilters extends Mutation
{
    use AuthTraits;
    protected $attributes = [
        'name' => 'listPositionsFilters',
    ];

    public function type(): Type
    {
        return GraphQL::type("SearchUsers");
    }

    public function args(): array
    {
        return [
            'search_key' => [
                "type" => Type::string()
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if(empty($this->checkAuth())) {
            return [
                'status' => FALSE,
                'message' => 'unauthorized',
                'records' => [],
            ];
        }
        try {
            $userData = Auth::guard('api')->user()->toArray();
            if(!empty($userData)) {
                $lang_key = $userData['lang_key'] ?? "ar";
                $query = Position::query();
                $query->select("position_id as value", DB::raw("IF('$lang_key' = 'ar',position_title_arabic , position_title_english) as label"));
                if(!empty($args["search_key"])) {
                    $query->where(function (Builder $query) use ($args) {
                        $query->where("position_title_english", "like", '%'.arabic_world($args["search_key"]).'%');
                        $query->orWhere("position_title_arabic", "like", '%'.arabic_world($args["search_key"]).'%');
                    });
                }
                $query->groupBy('position_id');
                $query->orderBy('position_id',"ASC");
                $data = $query->get();
                if($data->isNotEmpty()) {
                    return [
                        'status'    => TRUE,
                        'message'   => 'Data Available !!!',
                        'records'   => $data
                    ];
                }
                return [
                    'status'    => FALSE,
                    'message'   => 'There are no Data !!!',
                    'records'   => []
                ];
            }
        } catch (\Exception $e) {
            return [
                'status'    => FALSE,
                'message'   => $e->getMessage(),
                'records'   => NULL,
            ];
        }
        return [];
    }
}
