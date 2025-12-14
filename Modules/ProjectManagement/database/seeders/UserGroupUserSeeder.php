<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Core\Models\UserGroup;
use Illuminate\Support\Facades\DB;

class UserGroupUserSeeder extends Seeder
{
    public function run(): void
    {
        $userGroups = UserGroup::all();
        $users = User::all();

        if ($userGroups->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No user groups or users found. Please run UserGroupSeeder first.');
            return;
        }

        $coreConnection = config('core.database_connection');

        foreach ($userGroups as $group) {
            $numberOfUsers = rand(2, 5);
            $selectedUsers = $users->random($numberOfUsers);

            foreach ($selectedUsers as $user) {
                DB::connection($coreConnection)->table('user_group_user')->updateOrInsert([
                    'user_group_id' => $group->id,
                    'user_id' => $user->id
                ]);
            }

            $this->command->info("Assigned {$numberOfUsers} users to group: " . $group->getTranslation('name', 'en'));
        }

        $totalAssignments = DB::connection($coreConnection)->table('user_group_user')->count();
        $this->command->info("Successfully created {$totalAssignments} user-group assignments!");
    }
}
