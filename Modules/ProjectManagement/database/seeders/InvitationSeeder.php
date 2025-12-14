<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Core\Models\Workspace;
use Modules\Core\Models\Invitation;
use Carbon\Carbon;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        $workspaces = Workspace::all();
        $users = User::all();

        if ($workspaces->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No workspaces or users found. Please seed workspaces and users first.');
            return;
        }

        $invitationTypes = [
            'workspace_member',
            'project_manager',
            'admin_access',
            'viewer_access',
            'editor_access'
        ];

        $totalInvitations = 0;

        foreach ($workspaces as $workspace) {
            $inviterUser = $users->random();
            $numberOfInvitations = rand(3, 8);

            for ($i = 0; $i < $numberOfInvitations; $i++) {
                $targetUser = $users->where('id', '!=', $inviterUser->id)->random();
                $invitationType = $invitationTypes[array_rand($invitationTypes)];

                $expiresAt = Carbon::now()->addDays(rand(7, 30));
                $isAccepted = rand(1, 100) <= 70; // 70% acceptance rate

                $invitation = Invitation::create([
                    'token' => $this->generateInvitationToken(),
                    'workspace_id' => $workspace->id,
                    'email' => $targetUser->email,
                    'invited_user_id' => $targetUser->id,
                    'expires_at' => $expiresAt,
                    'accepted_at' => $isAccepted ? Carbon::now()->subDays(rand(1, 5)) : null,
                    'writer_id' => $inviterUser->id
                ]);

                $totalInvitations++;

                $this->command->info("Created invitation for {$targetUser->name} to workspace: " . $workspace->getTranslation('name', 'en'));
            }

            // Create some email-only invitations (for external users)
            $emailInvitations = rand(1, 3);
            for ($j = 0; $j < $emailInvitations; $j++) {
                $externalEmail = $this->generateRandomEmail();

                $invitation = Invitation::create([
                    'token' => $this->generateInvitationToken(),
                    'workspace_id' => $workspace->id,
                    'email' => $externalEmail,
                    'invited_user_id' => null,
                    'expires_at' => Carbon::now()->addDays(rand(7, 30)),
                    'accepted_at' => null,
                    'writer_id' => $inviterUser->id
                ]);

                $totalInvitations++;

                $this->command->info("Created email invitation for {$externalEmail} to workspace: " . $workspace->getTranslation('name', 'en'));
            }
        }

        $this->command->info("Successfully created {$totalInvitations} invitations!");
    }

    private function generateInvitationToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function generateRandomEmail(): string
    {
        $domains = ['gmail.com', 'outlook.com', 'yahoo.com', 'hotmail.com', 'company.com'];
        $names = ['john.doe', 'jane.smith', 'mike.johnson', 'sara.wilson', 'alex.brown', 'lisa.davis', 'tom.miller', 'amy.garcia'];

        $name = $names[array_rand($names)];
        $domain = $domains[array_rand($domains)];
        $number = rand(1, 999);

        return $name . $number . '@' . $domain;
    }
}
