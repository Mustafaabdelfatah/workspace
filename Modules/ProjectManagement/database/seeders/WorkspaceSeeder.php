<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Core\Models\Workspace;

class WorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $workspaceData = [
            [
                'name' => [
                    'en' => 'Elite Construction Company',
                    'ar' => 'شركة النخبة للإنشاءات'
                ],
                'logo_path' => 'logos/elite-construction.png',
                'workspace_type' => 'company',
                'a4_official_path' => 'templates/elite-a4-template.pdf',
                'stamp_path' => 'stamps/elite-official-stamp.png'
            ],
            [
                'name' => [
                    'en' => 'Modern Architecture Studio',
                    'ar' => 'استوديو العمارة الحديثة'
                ],
                'logo_path' => 'logos/modern-architecture.png',
                'workspace_type' => 'official',
                'a4_official_path' => 'templates/modern-arch-template.pdf',
                'stamp_path' => 'stamps/modern-arch-stamp.png'
            ],
            [
                'name' => [
                    'en' => 'Green Building Solutions',
                    'ar' => 'حلول البناء الأخضر'
                ],
                'logo_path' => 'logos/green-building.png',
                'workspace_type' => 'company',
                'a4_official_path' => 'templates/green-building-template.pdf',
                'stamp_path' => 'stamps/green-building-stamp.png'
            ],
            [
                'name' => [
                    'en' => 'Urban Development Corp',
                    'ar' => 'شركة التطوير العمراني'
                ],
                'logo_path' => 'logos/urban-development.png',
                'workspace_type' => 'official',
                'a4_official_path' => 'templates/urban-dev-template.pdf',
                'stamp_path' => 'stamps/urban-dev-stamp.png'
            ],
            [
                'name' => [
                    'en' => 'Luxury Interiors',
                    'ar' => 'التصميم الداخلي الفاخر'
                ],
                'logo_path' => 'logos/luxury-interiors.png',
                'workspace_type' => 'individual',
                'a4_official_path' => null,
                'stamp_path' => null
            ]
        ];

        foreach ($workspaceData as $data) {
            $owner = $users->random();

            $workspace = Workspace::create([
                'name' => $data['name'],
                'logo_path' => $data['logo_path'],
                'workspace_type' => $data['workspace_type'],
                'a4_official_path' => $data['a4_official_path'],
                'stamp_path' => $data['stamp_path'],
                'owner_id' => $owner->id,
                'writer_id' => $owner->id,
                'editor_id' => null
            ]);

            $this->command->info("Created workspace: " . $data['name']['en']);
        }

        $totalWorkspaces = Workspace::count();
        $this->command->info("Successfully created {$totalWorkspaces} workspaces!");
    }
}
