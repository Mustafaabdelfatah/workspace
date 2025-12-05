<?php

namespace Modules\Core\Repositories;

use Modules\Core\Models\User;
use Nwidart\Modules\Facades\Module;
use Modules\Core\Events\UserProfileUpdated;
use DB;
use Illuminate\Support\Facades\Storage;


class UserRepository
{
    protected $hr_conn = 'human_resources';

    public function getUsers(array $args)
    {
        $page = $args['page'];
        $perPage = $args['perPage'];
        $query = User::query();

        // Handle search key filter
        if (!empty($args['search_key'])) {
            $searchKey = strtolower($args['search_key']);
            $query->where(function ($subQuery) use ($searchKey) {
                $locales = ['en', 'ar'];
                $nameParts = ['first_name', 'second_name', 'third_name', 'last_name'];

                // Search localized full name combinations
                foreach ($locales as $locale) {
                    $concatParts = collect($nameParts)->map(function ($part) use ($locale) {
                        return "JSON_UNQUOTE(JSON_EXTRACT($part, '\$.$locale'))";
                    })->implode(", ' ', ");

                    $subQuery->orWhereRaw("LOWER(CONCAT($concatParts)) LIKE ?", ['%' . strtolower($searchKey) . '%']);
                }

                // Search non-translatable raw name fields
                foreach ($nameParts as $part) {
                    $subQuery->orWhereRaw("LOWER($part) LIKE ?", ['%' . strtolower($searchKey) . '%']);
                }

                // Additional fields
                $subQuery
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchKey) . '%'])
                    ->orWhereRaw('mobile LIKE ?', ['%' . $searchKey . '%'])
                    ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($searchKey) . '%']);
            });
        }

        // Handle active status filter
        if (isset($args['active_status'])) {
            $query->where('status', (int) $args['active_status']);
        }

        $sortOrder = !empty($args['sort_order']) && strtolower($args['sort_order']) === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortOrder);

        // Paginate the results
        $users = $query->paginate($perPage, ['*'], 'page', $page);
        return [
            'status' => !$users->isEmpty(),
            'message' => (($users->isEmpty())) ? __('lang_no_data_found') : __('lang_data_found'),
            'paging' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'records' => $users,
        ];
    }


    public function getUserProfile(array $args)
    {

        return [
            'status' => true,
            'message' => __('lang_data_found'),
            'data' => auth()->user(),
        ];
    }



    public function saveUser(array $args)
    {

        $data =
        [
            'email' => $args['email'],
            'mobile' => $args['mobile'],
            'name' => $args['name']
        ];

        if(isset($args['photo'])){
            $data['photo_path'] = Storage::putFile('users', $args['photo'])   ;
        }

        if(isset($args['id'])){
            $user = User::find($args['id']);
            $user->update($data);
        }else{
            $data['status'] = 1;
            $user = User::create($data);
        }
        return [
            'status' => true,
            'message' => isset($args['id']) ? __('user_updated_successfully') : __('user_created_successfully'),
        ];
    }




    public function updateUserProfile(array $args)
    {
        $user = User::find(auth()->user()->id);

        $data =
        [
            'email' => $args['email'],
            'mobile' => $args['mobile'],
            'name' => $args['name']
        ];

        if(isset($args['photo'])){
            $data['photo_path'] = Storage::putFile('users', $args['photo']);
        }

        $user->update($data);
        return [
            'status' => true,
            'message' => __('user_profile_updated_successfully'),
        ];
    }

    public function activeDeactivateUser(array $args)
    {
        try {
            DB::beginTransaction();

            $user = User::find($args['user_id']);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => __('user_not_found'),
                ];
            }

            $user->status = $user->status == 1 ? 0 : 1;

            $user->save();

            DB::commit();

            broadcast(new UserProfileUpdated($user->id));

            return [
                'status' => true,
                'message' => __('user_status_updated_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Failed to update user status: ' . $e->getMessage(),
            ];
        }
    }

    public function changeUserAdminNonAdmin(array $args)
    {
        try {
            DB::beginTransaction();

            $user = User::find($args['user_id']);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => __('user_not_found'),
                ];
            }

            $user->is_admin = $user->is_admin == 1 ? 0 : 1;

            $user->save();

            DB::commit();

            broadcast(new UserProfileUpdated($user->id));

            return [
                'status' => true,
                'message' => __('user_role_updated_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Failed to update user status: ' . $e->getMessage(),
            ];
        }
    }

    public function getModules()
    {
        $moduleNames = array_filter(array_keys(Module::all()), function ($name) {
            return $name !== 'core';
        });
        return [
            'status' => true,
            'message' => __('lang_data_found'),
            'modules' => $moduleNames,
        ];
    }



    public function deleteUser(array $args)
    {
        try {
            DB::beginTransaction();

            $user = User::find($args['user_id']);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => __('user_not_found'),
                ];
            }

            $user->status = 0;

            $user->save();

            $user->delete();

            DB::commit();

            return [
                'status' => true,
                'message' => __('user_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ];
        }
    }


}
