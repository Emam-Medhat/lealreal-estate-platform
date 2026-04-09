<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceTeam;

class PlumbingTeamSeeder extends Seeder
{
    public function run()
    {
        // Add more specific plumbing teams
        $plumbingTeams = [
            [
                'name' => 'Emergency Plumbing Team',
                'name_ar' => 'فريق الطوارئ السباكة',
                'description' => 'Team specialized in emergency plumbing repairs',
                'description_ar' => 'فريق متخصص في إصلاحات السباكة الطارئة',
                'team_leader_name' => 'خالد عبدالله',
                'team_leader_email' => 'khaled.plumbing@example.com',
                'team_leader_phone' => '0505556667',
                'specializations' => json_encode(['plumbing', 'emergency']),
                'contact_phone' => '0505556667',
                'contact_email' => 'emergency.plumbing@example.com',
                'max_concurrent_jobs' => 2,
                'working_hours' => json_encode(['start' => '00:00', 'end' => '23:59']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Residential Plumbing Team',
                'name_ar' => 'فريق السباكة السكنية',
                'description' => 'Team specialized in residential plumbing installations and repairs',
                'description_ar' => 'فريق متخصص في تركيبات وإصلاحات السباكة السكنية',
                'team_leader_name' => 'سالم محمد',
                'team_leader_email' => 'salem.residential@example.com',
                'team_leader_phone' => '0507778889',
                'specializations' => json_encode(['plumbing', 'residential']),
                'contact_phone' => '0507778889',
                'contact_email' => 'residential.plumbing@example.com',
                'max_concurrent_jobs' => 3,
                'working_hours' => json_encode(['start' => '07:00', 'end' => '19:00']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Commercial Plumbing Team',
                'name_ar' => 'فريق السباكة التجارية',
                'description' => 'Team specialized in commercial plumbing systems',
                'description_ar' => 'فريق متخصص في أنظمة السباكة التجارية',
                'team_leader_name' => 'عمر أحمد',
                'team_leader_email' => 'omar.commercial@example.com',
                'team_leader_phone' => '0509990001',
                'specializations' => json_encode(['plumbing', 'commercial']),
                'contact_phone' => '0509990001',
                'contact_email' => 'commercial.plumbing@example.com',
                'max_concurrent_jobs' => 2,
                'working_hours' => json_encode(['start' => '06:00', 'end' => '20:00']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Pipe Installation Team',
                'name_ar' => 'فريق تركيب الأنابيب',
                'description' => 'Team specialized in pipe installation and replacement',
                'description_ar' => 'فريق متخصص في تركيب واستبدال الأنابيب',
                'team_leader_name' => 'إبراهيم حسن',
                'team_leader_email' => 'ibrahim.pipes@example.com',
                'team_leader_phone' => '0501234568',
                'specializations' => json_encode(['plumbing', 'installation']),
                'contact_phone' => '0501234568',
                'contact_email' => 'pipe.installation@example.com',
                'max_concurrent_jobs' => 2,
                'working_hours' => json_encode(['start' => '08:00', 'end' => '17:00']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Drainage Team',
                'name_ar' => 'فريق الصرف الصحي',
                'description' => 'Team specialized in drainage systems and sewer lines',
                'description_ar' => 'فريق متخصص في أنظمة الصرف الصحي وخطوط المجاري',
                'team_leader_name' => 'يوسف علي',
                'team_leader_email' => 'yousef.drainage@example.com',
                'team_leader_phone' => '0509876544',
                'specializations' => json_encode(['plumbing', 'drainage']),
                'contact_phone' => '0509876544',
                'contact_email' => 'drainage.team@example.com',
                'max_concurrent_jobs' => 1,
                'working_hours' => json_encode(['start' => '06:30', 'end' => '18:30']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($plumbingTeams as $teamData) {
            MaintenanceTeam::create($teamData);
        }

        $this->command->info('Created ' . count($plumbingTeams) . ' plumbing teams successfully!');
    }
}
