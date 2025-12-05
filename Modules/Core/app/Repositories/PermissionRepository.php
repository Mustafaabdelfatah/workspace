<?php

namespace Modules\Core\Repositories;

//use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Group;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\User;
use Illuminate\Support\Arr;
use DB;
use Hash;
use Modules\Core\Traits\ValidationMessageTraits;
use Spatie\Permission\Models\Permission;

class PermissionRepository
{
use ValidationMessageTraits;
    public function getPermissionsTree()
    {
        $permissions = Permission::all()->pluck('name');
        if ($permissions->isEmpty()) {
            return [
                'name' => 'root',
                'children' => NULL
            ];
        }
        $tree = $this->buildPermissionTree($permissions);
        return [
            'name' => 'root',
            'children' => $tree
        ];
    }

    private function buildPermissionTree($permissions)
    {
        $tree = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission);
            $current = &$tree;

            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [
                        'name' => $part,
                        'children' => []
                    ];
                }
                $current = &$current[$part]['children'];
            }
        }

        return $this->convertTreeFormat($tree);
    }

    private function convertTreeFormat($tree)
    {
        $formattedTree = [];

        foreach ($tree as $node) {
            $formattedTree[] = [
                'name' => $node['name'],
                'children' => $this->convertTreeFormat($node['children'])
            ];
        }

        return $formattedTree;
    }


    public function createEditGroup($params){
       $rules = [
            'name' => [
                "required"
            ],
            'name.en' => [
                "required",
                'unique:groups,name->en' . (isset($params['id']) ? ',' . $params['id'] : ''),
            ],
            'name.ar' => [
                "required",
                'unique:groups,name->ar' . (isset($params['id']) ? ',' . $params['id'] : ''),
            ],
            'module_id' => [
                "nullable",
             ],
             'group_key' => [
                'required',
                'unique:groups,name' . (isset($params['id']) ? ',' . $params['id'] : ''),
             ],
           'permissions' => [
               'required_without:id',
               'array'
           ],
           'permissions.*' => [
               'string',
               'exists:permissions,name'
           ],
        ];
        DB::beginTransaction();
        try {
        if (isset($params['id'])) {
            $rules['id'] = [
                "exists:groups,id"
            ];
        }

        $validator = Validator::make($params, $rules, $this->validationMessages());

        if ($validator->fails()) {
            return [
                'status' => FALSE,
                'message' => implode(' ', $validator->errors()->all()),
                ];
        }
        if (isset($params['id'])) {
            $group = Group::find($params['id']);
            if (!$group) {
                return [
                    'status' => FALSE,
                    'message' => "Group not found",
                ];
            }
            $group = Group::findOrFail($params['id']);
            $group->update([
                'name' => $params['name'],
                'group_key' => $params['group_key'],
            ]);

        }else{
            $group = Group::create([
                'name' => $params['name'],
                'group_key' => $params['group_key'],
            ]);

         }
            if (isset($params['permissions'])) {
                $group->syncPermissions($params['permissions']);
            }
            DB::commit();

        return [
            'status'    => TRUE,
            'message'   => isset($params['id']) ? "Group updated successfully" : "Group created successfully"
        ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'status' => FALSE,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }

    public function getGroups($args){
        
        $page = $args['page'];
        $perPage = $args['per_page'];

        $groups = Group::with('permissions')->paginate($perPage, ['*'], 'page', $page);

        if ($groups->isEmpty()) {
            return [
                'status' => false,
                'message' => 'No groups found',
                'paging' => [
                    'total' => 0,
                    'current_page' => $page,
                    'last_page' => 0,
                    'from' => 0,
                    'to' => 0,
                ],
                'records' => [],
            ];
        }

        return [
            'status' => true,
            'message' => 'Groups retrieved successfully',
            'paging' => [
                'total' => $groups->total(),
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'from' => $groups->firstItem(),
                'to' => $groups->lastItem(),
            ],
            'records' => $groups->items(),
        ];
    }

    public function getGroup($args){
        $group = Group::with('permissions')->find($args['id']);
        if (!$group) {
            return [
                'status' => false,
                'message' => 'Group not found',
                'data' => []
            ];
        }

           return [
            'status' => true,
            'message' => 'Group found',
            'data' => $group
        ];
    }
}
