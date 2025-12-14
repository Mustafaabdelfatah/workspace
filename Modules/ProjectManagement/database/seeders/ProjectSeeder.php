<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Core\Models\Workspace;
use Modules\ProjectManagement\App\Models\Project;
use Modules\ProjectManagement\App\Enums\ProjectStatusEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $workspaces = Workspace::all();
        $users = User::all();

        if ($workspaces->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No workspaces or users found. Please seed workspaces and users first.');
            return;
        }

        $projectTemplates = [
            [
                'name' => [
                    'en' => 'Downtown Business Complex',
                    'ar' => 'مجمع الأعمال بوسط المدينة'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::COMMERCIAL,
                'custom_project_type' => null
            ],
            [
                'name' => [
                    'en' => 'Luxury Residential Towers',
                    'ar' => 'أبراج سكنية فاخرة'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::RESIDENTIAL,
                'custom_project_type' => null
            ],
            [
                'name' => [
                    'en' => 'Modern Hospital Facility',
                    'ar' => 'مرفق مستشفى حديث'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::INDUSTRIAL,
                'custom_project_type' => null
            ],
            [
                'name' => [
                    'en' => 'Educational Campus',
                    'ar' => 'الحرم الجامعي التعليمي'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::INDUSTRIAL,
                'custom_project_type' => null
            ],
            [
                'name' => [
                    'en' => 'Industrial Manufacturing Plant',
                    'ar' => 'مصنع التصنيع الصناعي'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::INDUSTRIAL,
                'custom_project_type' => null
            ],
            [
                'name' => [
                    'en' => 'Smart City Infrastructure',
                    'ar' => 'بنية المدينة الذكية التحتية'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::INFRASTRUCTURE,
                'custom_project_type' => null
            ],
            [
                'name' => [
                    'en' => 'Heritage Restoration Project',
                    'ar' => 'مشروع ترميم التراث'
                ],
                'entity_type' => 'contractor',
                'project_type' => ProjectTypeEnum::OTHER,
                'custom_project_type' => 'Heritage Restoration'
            ]
        ];

        $statuses = [
            ProjectStatusEnum::PLANNING,
            ProjectStatusEnum::ACTIVE,
            ProjectStatusEnum::ON_HOLD,
            ProjectStatusEnum::COMPLETED
        ];

        foreach ($workspaces as $workspace) {
            $projectsToCreate = rand(3, 6);

            for ($i = 0; $i < $projectsToCreate; $i++) {
                $template = $projectTemplates[array_rand($projectTemplates)];
                $owner = $users->random();
                $manager = $users->where('id', '!=', $owner->id)->random();

                $uniqueName = [
                    'en' => $template['name']['en'] . ' - ' . $workspace->getTranslation('name', 'en'),
                    'ar' => $template['name']['ar'] . ' - ' . $workspace->getTranslation('name', 'ar')
                ];

                $startDate = now()->subDays(rand(1, 90));
                $endDate = $startDate->copy()->addDays(rand(180, 540));

                $project = Project::create([
                    'workspace_id' => $workspace->id,
                    'name' => $uniqueName,
                    'owner_id' => $owner->id,
                    'manager_id' => $manager->id,
                    'entity_type' => $template['entity_type'],
                    'project_type' => $template['project_type'],
                    'custom_project_type' => $template['custom_project_type'],
                    'status' => $statuses[array_rand($statuses)],
                    'workspace_details_completed' => true,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'latitude' => $this->randomLatitude(),
                    'longitude' => $this->randomLongitude(),
                    'area' => rand(500, 50000),
                    'area_unit' => 'm²',
                    'settings' => [
                        'budget_currency' => 'SAR',
                        'timezone' => 'Asia/Riyadh',
                        'notifications_enabled' => true
                    ]
                ]);

                $this->command->info("Created project: " . $uniqueName['en'] . " (Code: {$project->code})");
            }
        }

        $totalProjects = Project::count();
        $this->command->info("Successfully created {$totalProjects} projects across all workspaces!");
    }

    private function randomLatitude(): float
    {
        return round(24.7136 + (mt_rand() / mt_getrandmax()) * 0.5, 6);
    }

    private function randomLongitude(): float
    {
        return round(46.6753 + (mt_rand() / mt_getrandmax()) * 0.5, 6);
    }
}
