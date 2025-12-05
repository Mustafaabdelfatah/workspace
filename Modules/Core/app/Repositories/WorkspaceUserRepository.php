<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\AccessGrant;
use Modules\Core\Models\User;


class WorkspaceUserRepository
{



    public function getAccessGrants($args)
    {

        $page = $args['page'] ?? null;
        $perPage = $args['per_page'] ?? null;

        $accessGrants = AccessGrant::where('workspace_id', $args['workspace_id']);

        if(!empty($args['scope_type'])) {
            $accessGrants = $accessGrants->where('scope_type', $args['scope_type']);
        }

        if(!empty($args['scope_id'])) {
            $accessGrants = $accessGrants->where('scope_id', $args['scope_id']);
        }
        
        

         $accessGrants = $page ? $accessGrants->paginate($perPage, ['*'], 'page', $page) : $accessGrants->get();

        return [
            'status' =>  !$accessGrants->isEmpty(),
            'message' =>  $accessGrants->isEmpty() ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => $page && $perPage ? [
                'total' => $accessGrants->total(),
                'current_page' => $accessGrants->currentPage(),
                'last_page' => $accessGrants->lastPage(),
                'from' => $accessGrants->firstItem(),
                'to' => $accessGrants->lastItem(),
            ] : null,
            'records' => $accessGrants,
        ];
    }

    public function getWorkspaceUsers($args)
    {

        $page = $args['page'] ?? null;
        $perPage = $args['per_page'] ?? null;
        $workspaceUsers = User::with('accessGrants');
        $workspaceUsers = $workspaceUsers->orderBy('id', 'DESC');

        $workspaceUsers = $page ? $workspaceUsers->paginate($perPage, ['*'], 'page', $page) : $workspaceUsers->get();

        return [
            'status' =>  !$workspaceUsers->isEmpty(),
            'message' =>  $workspaceUsers->isEmpty() ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => $page && $perPage ? [
                'total' => $workspaceUsers->total(),
                'current_page' => $workspaceUsers->currentPage(),
                'last_page' => $workspaceUsers->lastPage(),
                'from' => $workspaceUsers->firstItem(),
                'to' => $workspaceUsers->lastItem(),
            ] : null,
            'records' => $workspaceUsers,
        ];
    }



    public function updateAccessGrant($args)
    {
        DB::beginTransaction();
        try {
            $accessGrant = AccessGrant::find($args['access_grant_id']);
            $accessGrant->update([
                'group_id' => $args['role_id'],
            ]);
            DB::commit();
            return [
                'status' => true,
                'message' => __('lang_data_updated_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }

    public function revokeWorkspaceUser($args)
    {
        DB::beginTransaction();
        try {
           
            AccessGrant::where('workspace_id', $args['workspace_id'])->whereIn('user_id', $args['user_ids'])->delete();

            DB::commit();
            return [
                'status' => true,
                'message' => __('lang_data_revoked_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }



    public function revokeAccessGrant($args)
    {
            $accessGrants = AccessGrant::where('workspace_id', $args['workspace_id'])->whereIn('id', $args['access_grant_ids']);

        DB::beginTransaction();
        try {
            $accessGrants->delete();
            DB::commit();
            return [
            'status' => true,
            'message' => __('lang_data_revoked_successfully'),
        ];
    } catch (\Exception $e) {
        DB::rollBack();
        return [
            'status' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
        ];
    }
}

}