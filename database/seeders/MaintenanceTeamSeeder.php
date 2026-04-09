<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceTeam;

class MaintenanceTeamSeeder extends Seeder
{
    public function run()
    {
        $teams = [
            [
                'name' => 'General Maintenance Team',
                'name_ar' => 'فريق الصيانة العامة',
                'description' => 'Team specialized in general maintenance work',
                'description_ar' => 'فريق متخصص في الأعمال الصيانة العامة',
                'team_leader_name' => 'أحمد محمد',
                'team_leader_email' => 'ahmed.mohamed@example.com',
                'team_leader_phone' => '0501234567',
                'specializations' => json_encode(['general']),
                'contact_phone' => '0501234567',
                'contact_email' => 'team1@example.com',
                'max_concurrent_jobs' => 3,
                'working_hours' => json_encode(['start' => '08:00', 'end' => '17:00']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Electrical Team',
                'name_ar' => 'فريق الكهرباء',
                'description' => 'Team specialized in electrical work',
                'description_ar' => 'فريق متخصص في الأعمال الكهربائية',
                'team_leader_name' => 'محمد علي',
                'team_leader_email' => 'mohamed.ali@example.com',
                'team_leader_phone' => '0507654321',
                'specializations' => json_encode(['electrical']),
                'contact_phone' => '0507654321',
                'contact_email' => 'team2@example.com',
                'max_concurrent_jobs' => 2,
                'working_hours' => json_encode(['start' => '09:00', 'end' => '18:00']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Plumbing Team',
                'name_ar' => 'فريق السباكة',
                'description' => 'Team specialized in plumbing work',
                'description_ar' => 'فريق متخصص في أعمال السباكة',
                'team_leader_name' => 'عبدالله خالد',
                'team_leader_email' => 'abdullah.khaled@example.com',
                'team_leader_phone' => '0509876543',
                'specializations' => json_encode(['plumbing']),
                'contact_phone' => '0509876543',
                'contact_email' => 'team3@example.com',
                'max_concurrent_jobs' => 2,
                'working_hours' => json_encode(['start' => '08:30', 'end' => '16:30']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'HVAC Team',
                'name_ar' => 'فريق التكييف',
                'description' => 'Team specialized in HVAC and cooling systems',
                'description_ar' => 'فريق متخصص في أعمال التكييف والتبريد',
                'team_leader_name' => 'سالم أحمد',
                'team_leader_email' => 'salem.ahmed@example.com',
                'team_leader_phone' => '0501112233',
                'specializations' => json_encode(['hvac']),
                'contact_phone' => '0501112233',
                'contact_email' => 'team4@example.com',
                'max_concurrent_jobs' => 2,
                'working_hours' => json_encode(['start' => '07:00', 'end' => '19:00']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Structural Team',
                'name_ar' => 'فريق الأعمال الإنشائية',
                'description' => 'Team specialized in structural and construction work',
                'description_ar' => 'فريق متخصص في الأعمال الإنشائية والبناء',
                'team_leader_name' => 'خالد عمر',
                'team_leader_email' => 'khaled.omar@example.com',
                'team_leader_phone' => '0504455667',
                'specializations' => json_encode(['structural']),
                'contact_phone' => '0504455667',
                'contact_email' => 'team5@example.com',
                'max_concurrent_jobs' => 1,
                'working_hours' => json_encode(['start' => '07:30', 'end' => '15:30']),
                'status' => 'active',
                'is_available' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($teams as $teamData) {
            MaintenanceTeam::create($teamData);
        }

        $this->command->info('Created ' . count($teams) . ' maintenance teams successfully!');
    }
}
