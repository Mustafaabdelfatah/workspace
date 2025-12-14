<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectManagementDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Complete ProjectManagement Module Seeding Cycle...');
        $this->command->newLine();

        $this->call([
            UserSeeder::class,
            WorkspaceSeeder::class,
            GroupSeeder::class,
            UserGroupSeeder::class,
            UserGroupUserSeeder::class,
            ProjectSeeder::class,
            // ProjectInvitationSeeder::class,
            InvitationSeeder::class,
            InvitationItemSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('Complete ProjectManagement Module seeding cycle completed successfully!');
        $this->command->info('Data Summary:');
        $this->command->info('   - Users: Created professional users with various roles');
        $this->command->info('   - Workspaces: Created companies with complete workspace details');
        $this->command->info('   - Groups: Created permission groups for access control');
        $this->command->info('   - User Groups: Created professional groups for each workspace');
        $this->command->info('   - Group Assignments: Assigned users to groups randomly');
        $this->command->info('   - Projects: Created realistic projects for each workspace');
        $this->command->info('   - Project Invitations: Sent project invitations to users and groups');
        $this->command->info('   - Workspace Invitations: Created general workspace invitations');
        $this->command->info('   - Invitation Items: Added scope-based invitation permissions');
        $this->command->info('');
        $this->command->info(' Complete invitation ecosystem ready for testing!');
    }
}