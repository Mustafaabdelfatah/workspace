<?php

namespace Modules\ProjectManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Core\Models\UserGroup;
use Modules\Core\Models\Workspace;

class UserGroupSeeder extends Seeder
{
    public function run(): void
    {
        $workspaces = Workspace::limit(3)->get();
        $users = User::limit(5)->get();

        if ($workspaces->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No workspaces or users found. Please seed workspaces and users first.');
            return;
        }

        $groupData = [
            [
                'name' => [
                    'en' => 'Project Managers',
                    'ar' => 'مدراء المشاريع'
                ],
                'description' => [
                    'en' => 'Group for project managers responsible for overseeing project execution and coordination',
                    'ar' => 'مجموعة لمدراء المشاريع المسؤولين عن الإشراف على تنفيذ المشاريع والتنسيق'
                ]
            ],
            [
                'name' => [
                    'en' => 'Senior Engineers',
                    'ar' => 'المهندسون الأقدم'
                ],
                'description' => [
                    'en' => 'Senior engineering team with extensive experience in design and technical oversight',
                    'ar' => 'فريق هندسي أقدم ذو خبرة واسعة في التصميم والإشراف التقني'
                ]
            ],
            [
                'name' => [
                    'en' => 'Site Supervisors',
                    'ar' => 'مشرفو الموقع'
                ],
                'description' => [
                    'en' => 'On-site supervisors responsible for daily operations and quality control',
                    'ar' => 'مشرفو الموقع المسؤولون عن العمليات اليومية ومراقبة الجودة'
                ]
            ],
            [
                'name' => [
                    'en' => 'Design Team',
                    'ar' => 'فريق التصميم'
                ],
                'description' => [
                    'en' => 'Creative design team handling architectural and interior design projects',
                    'ar' => 'فريق التصميم الإبداعي المختص بمشاريع التصميم المعماري والداخلي'
                ]
            ],
            [
                'name' => [
                    'en' => 'Quality Assurance',
                    'ar' => 'ضمان الجودة'
                ],
                'description' => [
                    'en' => 'Quality assurance specialists ensuring compliance with standards and regulations',
                    'ar' => 'أخصائيو ضمان الجودة الذين يضمنون الامتثال للمعايير واللوائح'
                ]
            ],
            [
                'name' => [
                    'en' => 'Financial Controllers',
                    'ar' => 'المراقبون الماليون'
                ],
                'description' => [
                    'en' => 'Financial control team managing budgets, costs, and financial reporting',
                    'ar' => 'فريق الرقابة المالية المسؤول عن إدارة الميزانيات والتكاليف والتقارير المالية'
                ]
            ],
            [
                'name' => [
                    'en' => 'Safety Officers',
                    'ar' => 'ضباط السلامة'
                ],
                'description' => [
                    'en' => 'Safety and health officers ensuring workplace safety and regulatory compliance',
                    'ar' => 'ضباط السلامة والصحة الذين يضمنون سلامة مكان العمل والامتثال التنظيمي'
                ]
            ],
            [
                'name' => [
                    'en' => 'Client Relations',
                    'ar' => 'علاقات العملاء'
                ],
                'description' => [
                    'en' => 'Client relationship management team handling communication and satisfaction',
                    'ar' => 'فريق إدارة علاقات العملاء المختص بالتواصل والرضا'
                ]
            ]
        ];

        foreach ($workspaces as $workspace) {
            $writer = $users->random();

            foreach ($groupData as $index => $group) {
                UserGroup::create([
                    'name' => $group['name'],
                    'description' => $group['description'],
                    'workspace_id' => $workspace->id,
                    'writer_id' => $writer->id,
                    'editor_id' => ($index % 2 === 0) ? $users->random()->id : null,
                ]);
            }

            $this->command->info("Created " . count($groupData) . " user groups for workspace: " . $workspace->getTranslation('name', 'en'));
        }

        $totalGroups = UserGroup::count();
        $this->command->info("Successfully created {$totalGroups} user groups across all workspaces!");
    }
}
