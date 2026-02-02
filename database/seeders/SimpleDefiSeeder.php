<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimpleDefiSeeder extends Seeder
{
    public function run()
    {
        // First create sample properties if they don't exist
        $propertyCount = DB::table('properties')->count();
        if ($propertyCount == 0) {
            $properties = [
                [
                    'title' => 'فيلا فاخرة في الرياض',
                    'description' => 'فيلا فاخرة في حي النخيل',
                    'location' => 'الرياض، حي النخيل',
                    'price' => 1500000,
                    'property_type' => 'villa',
                    'status' => 'available',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'title' => 'مكاتب تجارية في جدة',
                    'description' => 'مكاتب تجارية حديثة',
                    'location' => 'جدة، حي الروضة',
                    'price' => 2000000,
                    'property_type' => 'commercial',
                    'status' => 'available',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'title' => 'فيلا في الدمام',
                    'description' => 'فيلا فاخرة في الدمام',
                    'location' => 'الدمام',
                    'price' => 1200000,
                    'property_type' => 'villa',
                    'status' => 'available',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'title' => 'شقة سكنية في الرياض',
                    'description' => 'شقة سكنية عصرية',
                    'location' => 'الرياض، حي الملز',
                    'price' => 800000,
                    'property_type' => 'apartment',
                    'status' => 'available',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'title' => 'محل تجاري في جدة',
                    'description' => 'محل تجاري مميز',
                    'location' => 'جدة، حي الروضة',
                    'price' => 600000,
                    'property_type' => 'retail',
                    'status' => 'available',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            ];

            DB::table('properties')->insert($properties);
        }

        // Get the first few property IDs
        $properties = DB::table('properties')->limit(5)->get();

        // Create sample crowdfunding campaigns
        if ($properties->count() >= 3) {
            $campaigns = [
                [
                    'property_id' => $properties[0]->id,
                    'title' => 'مشروع سكني الرياض',
                    'description' => 'مشروع سكني فاخر في حي النخيل بالرياض',
                    'target_amount' => 500000,
                    'current_amount' => 325000,
                    'min_investment' => 10000,
                    'return_rate' => 15.5,
                    'duration_months' => 12,
                    'start_date' => Carbon::now()->subDays(15),
                    'end_date' => Carbon::now()->addDays(30),
                    'status' => 'active',
                    'created_by' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'property_id' => $properties[1]->id,
                    'title' => 'مجمع تجاري جدة',
                    'description' => 'مجمع تجاري حديث في حي الروضة بجدة',
                    'target_amount' => 750000,
                    'current_amount' => 450000,
                    'min_investment' => 15000,
                    'return_rate' => 18.2,
                    'duration_months' => 18,
                    'start_date' => Carbon::now()->subDays(10),
                    'end_date' => Carbon::now()->addDays(45),
                    'status' => 'active',
                    'created_by' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'property_id' => $properties[2]->id,
                    'title' => 'فيلا الدمام',
                    'description' => 'فيلا فاخرة في الدمام',
                    'target_amount' => 400000,
                    'current_amount' => 400000,
                    'min_investment' => 8000,
                    'return_rate' => 14.8,
                    'duration_months' => 10,
                    'start_date' => Carbon::now()->subDays(60),
                    'end_date' => Carbon::now()->subDays(5),
                    'status' => 'completed',
                    'created_by' => 1,
                    'created_at' => Carbon::now()->subDays(65),
                    'updated_at' => Carbon::now()->subDays(5)
                ]
            ];

            DB::table('crowdfunding_campaigns')->insert($campaigns);

            // Create sample investments
            $campaigns = DB::table('crowdfunding_campaigns')->limit(2)->get();
            $users = DB::table('users')->limit(3)->get();

            if ($campaigns->count() > 0 && $users->count() > 0) {
                $investments = [
                    [
                        'campaign_id' => $campaigns[0]->id,
                        'user_id' => $users[0]->id,
                        'amount' => 25000,
                        'shares' => 2.5,
                        'share_price' => 10000,
                        'status' => 'confirmed',
                        'created_at' => Carbon::now()->subHours(2),
                        'updated_at' => Carbon::now()->subHours(2)
                    ],
                    [
                        'campaign_id' => $campaigns[1]->id,
                        'user_id' => $users[1]->id,
                        'amount' => 15000,
                        'shares' => 1.0,
                        'share_price' => 15000,
                        'status' => 'confirmed',
                        'created_at' => Carbon::now()->subHours(5),
                        'updated_at' => Carbon::now()->subHours(5)
                    ],
                    [
                        'campaign_id' => $campaigns[0]->id,
                        'user_id' => $users[2]->id,
                        'amount' => 10000,
                        'shares' => 1.0,
                        'share_price' => 10000,
                        'status' => 'confirmed',
                        'created_at' => Carbon::now()->subHours(8),
                        'updated_at' => Carbon::now()->subHours(8)
                    ]
                ];

                DB::table('crowdfunding_investments')->insert($investments);
            }

            // Create sample loans
            if ($properties->count() >= 5) {
                $loans = [
                    [
                        'property_id' => $properties[3]->id,
                        'borrower_name' => 'محمد أحمد',
                        'borrower_email' => 'mohammed@example.com',
                        'borrower_phone' => '966500000001',
                        'loan_amount' => 250000,
                        'interest_rate' => 12.5,
                        'loan_term_months' => 24,
                        'purpose' => 'شراء عقار سكني',
                        'collateral_value' => 350000,
                        'monthly_income' => 15000,
                        'credit_score' => 750,
                        'monthly_payment' => 11850,
                        'status' => 'approved',
                        'approved_date' => Carbon::now()->subMonths(3),
                        'start_date' => Carbon::now()->subMonths(3),
                        'end_date' => Carbon::now()->addMonths(21),
                        'created_at' => Carbon::now()->subMonths(3),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'property_id' => $properties[4]->id,
                        'borrower_name' => 'فاطمة محمد',
                        'borrower_email' => 'fatima@example.com',
                        'borrower_phone' => '966500000002',
                        'loan_amount' => 180000,
                        'interest_rate' => 14.2,
                        'loan_term_months' => 18,
                        'purpose' => 'تمويل عقاري',
                        'collateral_value' => 250000,
                        'monthly_income' => 12000,
                        'credit_score' => 720,
                        'monthly_payment' => 11250,
                        'status' => 'approved',
                        'approved_date' => Carbon::now()->subMonths(2),
                        'start_date' => Carbon::now()->subMonths(2),
                        'end_date' => Carbon::now()->addMonths(16),
                        'created_at' => Carbon::now()->subMonths(2),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'property_id' => null,
                        'borrower_name' => 'عبدالله سالم',
                        'borrower_email' => 'abdullah@example.com',
                        'borrower_phone' => '966500000003',
                        'loan_amount' => 300000,
                        'interest_rate' => 13.5,
                        'loan_term_months' => 30,
                        'purpose' => 'شراء عقار تجاري',
                        'collateral_value' => 450000,
                        'monthly_income' => 20000,
                        'credit_score' => 780,
                        'monthly_payment' => 13500,
                        'status' => 'pending',
                        'created_at' => Carbon::now()->subHours(3),
                        'updated_at' => Carbon::now()
                    ]
                ];

                DB::table('defi_loans')->insert($loans);
            }
        }

        // Create sample risk assessments
        if ($properties->count() >= 2) {
            $assessments = [
                [
                    'property_id' => $properties[0]->id,
                    'assessment_type' => 'investment',
                    'criteria' => json_encode([
                        'location' => ['factor' => 'الموقع الجغرافي', 'weight' => 0.25, 'score' => 85],
                        'price' => ['factor' => 'التسعير', 'weight' => 0.20, 'score' => 80],
                        'market_demand' => ['factor' => 'الطلب في السوق', 'weight' => 0.20, 'score' => 75],
                        'property_condition' => ['factor' => 'حالة العقار', 'weight' => 0.15, 'score' => 90],
                        'legal_status' => ['factor' => 'الوضع القانوني', 'weight' => 0.20, 'score' => 85]
                    ]),
                    'overall_score' => 82.5,
                    'risk_level' => 'منخفض',
                    'recommendations' => json_encode(['استثمار جيد مع عائد متوقع', 'موقع ممتاز يضمن القيمة']),
                    'assessed_by' => 1,
                    'created_at' => Carbon::now()->subHours(2),
                    'updated_at' => Carbon::now()->subHours(2)
                ],
                [
                    'property_id' => $properties[1]->id,
                    'assessment_type' => 'loan',
                    'criteria' => json_encode([
                        'location' => ['factor' => 'الموقع الجغرافي', 'weight' => 0.25, 'score' => 75],
                        'price' => ['factor' => 'التسعير', 'weight' => 0.20, 'score' => 70],
                        'market_demand' => ['factor' => 'الطلب في السوق', 'weight' => 0.20, 'score' => 80],
                        'property_condition' => ['factor' => 'حالة العقار', 'weight' => 0.15, 'score' => 85],
                        'legal_status' => ['factor' => 'الوضع القانوني', 'weight' => 0.20, 'score' => 75]
                    ]),
                    'overall_score' => 76.5,
                    'risk_level' => 'متوسط',
                    'recommendations' => json_encode(['يحتاج تقييم إضافي', 'السعر مرتفع قليلاً']),
                    'assessed_by' => 1,
                    'created_at' => Carbon::now()->subHours(5),
                    'updated_at' => Carbon::now()->subHours(5)
                ]
            ];

            DB::table('risk_assessments')->insert($assessments);
        }
    }
}
