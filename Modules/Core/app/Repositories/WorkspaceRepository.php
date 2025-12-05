<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Workspace;


class WorkspaceRepository
{

    public function getWorkspaces($args)
    {

        $page = $args['page'] ?? null;
        $perPage = $args['per_page'] ?? null;
        $query = Workspace::when(!empty($args['module_id']),fn($q)=> $q->where('module_id',$args['module_id']));
        $workspaces = $query->orderBy('id', 'DESC');

        $workspaces = $page ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();

        return [
            'status' =>  !$workspaces->isEmpty(),
            'message' =>  $workspaces->isEmpty() ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => $page && $perPage ? [
                'total' => $workspaces->total(),
                'current_page' => $workspaces->currentPage(),
                'last_page' => $workspaces->lastPage(),
                'from' => $workspaces->firstItem(),
                'to' => $workspaces->lastItem(),
            ] : null,
            'records' => $workspaces,
        ];
    }

    public function getWorkspaceById($args)
    {
        $workspace = Workspace::find($args['id']);

        if (!$workspace) {
            return [
                'status' => false,
                'message' => __('lang_no_data_found'),
            ];
        }

        return [
            'status' => true,
            'message' => __('lang_data_found'),
            'data' => $workspace,
        ];
    }


    public function saveWorkspace($args)
    {
        DB::beginTransaction();
        try {
            $workspace = Workspace::updateOrCreate(
                ['id' => $args['id'] ?? null],
                [
                    'name' => $args['name'],
                    'workspace_type' => $args['workspace_type'],
                    'owner_id' => auth()->id(),
                    //'module_id' => $args['module_id'],
                ]
            );

            DB::commit();
            return [
                'status' => true,
                'message' => isset($args['id']) ? __('lang_data_updated_successfully') : __('lang_data_added_successfully'),
                'id' => $workspace->id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }



    public function deleteWorkspace($args)
    {
        $workspace = Workspace::find($args['id']);

        if (!$workspace) {
            return [
                'status' => false,
                'message' => __('lang_data_not_found'),
            ];
        }

        DB::beginTransaction();
        try {
            $workspace->delete();
            DB::commit();
            return [
                'status' => true,
                'message' => __('lang_data_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }

    public function setDefaultWorkspace($args)
    {
        $user = auth()->user();
        $user->default_workspace_id = $args['workspace_id'];
        $user->save();

        return [
            'status' => true,
            'message' => __('lang_default_workspace_updated_successfully'),
        ];
    }
}

