<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DefiSeeder extends Seeder
{
    public function run()
    {
        // Create sample crowdfunding campaigns
        $campaigns = [
            [
                'property_id' => 1,
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
                'property_id' => 2,
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
                'property_id' => 3,
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
                'created_by' => 2,
                'created_at' => Carbon::now()->subDays(65),
                'updated_at' => Carbon::now()->subDays(5)
            ]
        ];

        DB::table('crowdfunding_campaigns')->insert($campaigns);

        // Create sample investments
        $investments = [
            [
                'campaign_id' => 1,
                'user_id' => 1,
                'amount' => 25000,
                'shares' => 2.5,
                'share_price' => 10000,
                'status' => 'confirmed',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2)
            ],
            [
                'campaign_id' => 2,
                'user_id' => 2,
                'amount' => 15000,
                'shares' => 1.0,
                'share_price' => 15000,
                'status' => 'confirmed',
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5)
            ],
            [
                'campaign_id' => 1,
                'user_id' => 3,
                'amount' => 10000,
                'shares' => 1.0,
                'share_price' => 10000,
                'status' => 'confirmed',
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(8)
            ]
        ];

        DB::table('crowdfunding_investments')->insert($investments);

        // Create sample loans
        $loans = [
            [
                'property_id' => 4,
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
                'property_id' => 5,
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

        // Create sample loan repayments
        $repayments = [];
        $loan1Id = 1;
        $loan2Id = 2;

        // Add some repayments for loan 1
        for ($i = 1; $i <= 6; $i++) {
            $repayments[] = [
                'loan_id' => $loan1Id,
                'amount' => 11850,
                'principal_amount' => 9250,
                'interest_amount' => 2600,
                'due_date' => Carbon::now()->addMonths($i - 6),
                'paid_date' => $i <= 3 ? Carbon::now()->addMonths($i - 6) : null,
                'status' => $i <= 3 ? 'paid' : 'pending',
                'created_at' => Carbon::now()->subMonths(6 - $i),
                'updated_at' => $i <= 3 ? Carbon::now()->addMonths($i - 6) : Carbon::now()
            ];
        }

        // Add some repayments for loan 2
        for ($i = 1; $i <= 4; $i++) {
            $repayments[] = [
                'loan_id' => $loan2Id,
                'amount' => 11250,
                'principal_amount' => 8750,
                'interest_amount' => 2500,
                'due_date' => Carbon::now()->addMonths($i - 4),
                'paid_date' => $i <= 2 ? Carbon::now()->addMonths($i - 4) : null,
                'status' => $i <= 2 ? 'paid' : 'pending',
                'created_at' => Carbon::now()->subMonths(4 - $i),
                'updated_at' => $i <= 2 ? Carbon::now()->addMonths($i - 4) : Carbon::now()
            ];
        }

        DB::table('loan_repayments')->insert($repayments);

        // Create sample risk assessments
        $assessments = [
            [
                'property_id' => 1,
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
                'property_id' => 2,
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
