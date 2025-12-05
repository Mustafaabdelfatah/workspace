<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\Invitation;
use Modules\Core\Models\UserGroup;
use Modules\Core\Models\User;
use Modules\Core\Emails\InvitationMail;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Models\AccessGrant;

class UserGroupRepository
{

    public function getUserGroups($args)
    {

        $page = $args['page'] ?? null;
        $perPage = $args['per_page'] ?? null;
        $workspaceId = $args['workspace_id'] ?? null;
        $query = UserGroup::whereHas('workspace');

        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }

        $query->with(['workspace', 'writer', 'users']);
        $invitations = $query->orderBy('id', 'DESC');

        $invitations = $page ? $query->paginate($perPage, ['*'], 'page', $page) : $query->get();

        return [
            'status' =>  !$invitations->isEmpty(),
            'message' =>  $invitations->isEmpty() ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => $page && $perPage ? [
                'total' => $invitations->total(),
                'current_page' => $invitations->currentPage(),
                'last_page' => $invitations->lastPage(),
                'from' => $invitations->firstItem(),
                'to' => $invitations->lastItem(),
            ] : null,
            'records' => $invitations,
        ];
    }


    public function saveUserGroup($args)
    {
        DB::beginTransaction();
        try {

            $userGroup = UserGroup::updateOrCreate(['id' => $args['id']??NULL],['name'=> $args['name'], 'description' => $args['description'], 'workspace_id' => $args['workspace_id']]);
           if(!isset($args['id']) && isset($args['emails'])) {
          
            foreach($args['emails'] as $email) {
                $invitedUserId = User::where('email', $email)->first()?->id ?? null;
            $invitation = Invitation::create([
                'workspace_id' => $args['workspace_id'],
                'email' => $email,
                'invited_user_id' => $invitedUserId,
                'expires_at' => now()->addDays(1),
                'token' => Str::random(64),
            ]);


            $invitation->items()->create([
                'scope_type' => 'user_group',
                'scope_id' => $userGroup->id
            ]);
           

            $frontendUrl = env("SITE_CLIENT_URL", "");
            $url = $frontendUrl . "/auth/invitation/{$invitation->token}";
    
            Mail::to($email)->send(new InvitationMail($url, 'en'));

            }
        }
        
            DB::commit();
            return [
                'status' => true,
                'message' => isset($args['id']) ? __('lang_data_updated_successfully') : __('lang_data_added_successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }



   

    public function sendUserGroupInvitation($args)
    {
        DB::beginTransaction();
        try {
            $userGroup = UserGroup::find($args['user_group_id']);
            foreach($args['emails'] as $email) {
                $invitedUserId = User::where('email', $email)->first()?->id ?? null;
            $invitation = Invitation::create([
                'workspace_id' => $userGroup->workspace_id,
                'email' => $email,
                'invited_user_id' => $invitedUserId,
                'expires_at' => now()->addDays(1),
                'token' => Str::random(64),
            ]);


            $invitation->items()->create([
                'scope_type' => 'user_group',
                'scope_id' => $userGroup->id
            ]);

            $frontendUrl = env("SITE_CLIENT_URL", "");
            $url = $frontendUrl . "/auth/invitation/{$invitation->token}";
    
            Mail::to($email)->send(new InvitationMail($url, 'en'));

            }
            
        
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
        return [
            'status' => true,
            'message' => __('lang_data_sent_successfully'),
        ];
    }

    public function deleteUserGroup($args)
    {
        $userGroup = UserGroup::find($args['id']);
        if (!$userGroup) {
            return [
                'status' => false,
                'message' => __('lang_data_not_found'),
            ];
        }
        DB::beginTransaction();
        try {
            $userGroup->delete();
            DB::commit();
            return [
                'status' => true,
                'message' => __('lang_data_deleted_successfully'),
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }


    public function assignUserGroupToScope($args)
    {
        $userGroup = UserGroup::find($args['user_group_id']);

        if(AccessGrant::where('workspace_id', $userGroup->workspace_id)->where('user_group_id', $userGroup->id)->where('scope_type', $args['scope_type'])->where('scope_id', $args['scope_id'] ?? null)->exists()) {
            return [
                'status' => false,
                'message' => __('lang_data_already_assigned'),
            ];
        }

        try {
            DB::beginTransaction();

        AccessGrant::create([
            'workspace_id' => $userGroup->workspace_id,
            'group_id' => $args['role_id'],
            'user_group_id' => $userGroup->id,
            'scope_type' => $args['scope_type'],
            'scope_id' => $args['scope_id'] ?? null,
        ]);

        DB::commit();
        return [
                'status' => true,
                'message' => __('lang_data_assigned_successfully'),
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


