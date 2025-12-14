<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Invitation;
use Modules\Core\Models\InvitationItem;
use Modules\Core\Models\Group;
use Modules\Core\Models\UserGroup;

class InvitationItemSeeder extends Seeder
{
    public function run(): void
    {
        $invitations = Invitation::all();
        $groups = Group::all();
        $userGroups = UserGroup::all();

        if ($invitations->isEmpty()) {
            $this->command->warn('No invitations found. Please run InvitationSeeder first.');
            return;
        }

        $scopeTypes = [
            'workspace',
            'project',
            'document',
            'task',
            'report',
            'finance',
            'quality_control'
        ];

        $totalItems = 0;

        foreach ($invitations as $invitation) {
            $numberOfItems = rand(1, 4);

            for ($i = 0; $i < $numberOfItems; $i++) {
                $scopeType = $scopeTypes[array_rand($scopeTypes)];
                $scopeId = rand(1, 100);
                $useGroup = rand(1, 100) <= 40; // 40% chance to assign a group

                $itemData = [
                    'invitation_id' => $invitation->id,
                    'scope_type' => $scopeType,
                    'scope_id' => $scopeId,
                    'group_id' => null
                ];

                if ($useGroup) {
                    if (!$groups->isEmpty() && rand(1, 100) <= 50) {
                        $itemData['group_id'] = $groups->random()->id;
                    }
                }

                InvitationItem::create($itemData);
                $totalItems++;

                $this->command->info("Created invitation item for scope: {$scopeType} (ID: {$scopeId})");
            }
        }

        $this->command->info("Successfully created {$totalItems} invitation items!");
    }
}
