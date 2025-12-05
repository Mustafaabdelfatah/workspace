<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\Invitation;
use Modules\Core\Models\InvitationItem;
use Modules\Core\Models\User;
use Modules\Core\Emails\InvitationMail;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Models\AccessGrant;
use Modules\Core\Models\UserGroup;

class InvitationRepository
{

    public function getInvitations($args)
    {

        $page = $args['page'] ?? null;
        $perPage = $args['per_page'] ?? null;
        $workspaceId = $args['workspace_id'] ?? null;
        $query = Invitation::query();

        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }

        $query->with(['workspace', 'writer', 'items.group']);
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


    public function sendInvitation($args)
    {
        DB::beginTransaction();
        try {
            foreach ($args['emails'] as $email) {
            $invitationData = [
                'workspace_id' => $args['workspace_id'],
                'email' => $email ?? null,
                'invited_user_id' => User::where('email', $email)->first()?->id ?? null,
                'expires_at' => now()->addDays(1),
            ];

        
            if (!isset($args['id'])) {
                $invitationData['token'] = Str::random(64);
            }

            $invitation = Invitation::updateOrCreate(
                ['id' => $args['id'] ?? null],
                $invitationData
            );

            if (isset($args['items']) && is_array($args['items'])) {
                if (isset($args['id'])) {
                    $invitation->items()->delete();
                }

                foreach ($args['items'] as $item) {
                    InvitationItem::create([
                        'invitation_id' => $invitation->id,
                        'scope_type' => $item['scope_type'] ?? null,
                        'scope_id' => $item['scope_id'] ?? null,
                        'group_id' => $item['role_id'] ?? null,
                    ]);
                }
            }

            $frontendUrl = env("SITE_CLIENT_URL", "");
            $url = $frontendUrl . "/auth/invitation/{$invitation->token}";
    
            Mail::to($email)->send(new InvitationMail($url, 'en'));
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



    public function deleteInvitation($args)
    {
        $invitation = Invitation::find($args['id']);

        if (!$invitation) {
            return [
                'status' => false,
                'message' => __('lang_data_not_found'),
            ];
        }

        DB::beginTransaction();
        try {
            $invitation->delete();
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

    public function acceptInvitation($args)
    {
        DB::beginTransaction();
        try {
            $invitation = Invitation::where('token', $args['token'])->first();

            if (!$invitation) {
                return [
                    'status' => false,
                    'message' => __('lang_data_not_found'),
                ];
            }

            if ($invitation->expires_at && $invitation->expires_at < now()) {
                return [
                    'status' => false,
                    'message' => __('lang_data_expired'),
                ];
            }

            if ($invitation->accepted_at) {
                return [
                    'status' => false,
                    'message' => __('lang_data_already_accepted'),
                ];
            }

            $invitation->update([
                'accepted_at' => now(),
                'invited_user_id' => auth()->id(),
            ]);

            $invitation->items->each(function ($item) use ($invitation) {

                if($item->scope_type == 'user_group') {
                    $userGroup = UserGroup::find($item->scope_id);
                    $userGroup->users()->attach($invitation->invited_user_id);
                }else{
                AccessGrant::create([
                    'workspace_id' => $invitation->workspace_id,
                    'user_id' => $invitation->invited_user_id,
                    'group_id' => $item->group_id,
                    'scope_type' => $item->scope_type,
                    'scope_id' => $item->scope_id,
                ]);
            }
            });

            DB::commit();
            return [
                'status' => true,
                'message' => __('lang_data_accepted_successfully'),
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

