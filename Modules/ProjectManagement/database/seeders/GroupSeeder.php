<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Group;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $groupsData = [
            [
                'group_key' => 'project_admin',
                'name' => 'Project Administrator',
                'module_id' => null
            ],
            [
                'group_key' => 'project_manager',
                'name' => 'Project Manager',
                'module_id' => null
            ],
            [
                'group_key' => 'team_lead',
                'name' => 'Team Leader',
                'module_id' => null
            ],
            [
                'group_key' => 'developer',
                'name' => 'Developer',
                'module_id' => null
            ],
            [
                'group_key' => 'quality_assurance',
                'name' => 'Quality Assurance',
                'module_id' => null
            ],
            [
                'group_key' => 'financial_controller',
                'name' => 'Financial Controller',
                'module_id' => null
            ],
            [
                'group_key' => 'client_manager',
                'name' => 'Client Manager',
                'module_id' => null
            ],
            [
                'group_key' => 'safety_officer',
                'name' => 'Safety Officer',
                'module_id' => null
            ],
            [
                'group_key' => 'viewer',
                'name' => 'Viewer',
                'module_id' => null
            ],
            [
                'group_key' => 'guest',
                'name' => 'Guest Access',
                'module_id' => null
            ]
        ];

        foreach ($groupsData as $data) {
            $existingGroup = Group::where('group_key', $data['group_key'])->first();

            if (!$existingGroup) {
                Group::create($data);
                $this->command->info("Created group: " . $data['name']);
            } else {
                $this->command->warn("Group already exists: " . $data['name']);
            }
        }

        $totalGroups = Group::count();
        $this->command->info("Total groups in system: {$totalGroups}");
    }
}
