<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate dynamic tax data with realistic variations
        $taxTypes = [
            [
                'base_name' => 'ضريبة القيمة المضافة',
                'base_description' => 'ضريبة القيمة المضافة القياسية على السلع والخدمات',
                'base_rate' => 15.0000,
                'type' => 'vat',
                'category' => 'sales',
                'frequency' => 'monthly',
                'deadline' => '15th_of_month'
            ],
            [
                'base_name' => 'ضريبة الدخل للأفراد',
                'base_description' => 'ضريبة الدخل على رواتب وأجور الأفراد',
                'base_rate' => 20.0000,
                'type' => 'income',
                'category' => 'personal',
                'frequency' => 'annual',
                'deadline' => 'april_30th'
            ],
            [
                'base_name' => 'ضريبة العقارات السكنية',
                'base_description' => 'ضريبة سنوية على تملك العقارات السكنية',
                'base_rate' => 2.5000,
                'type' => 'property',
                'category' => 'real_estate',
                'frequency' => 'annual',
                'deadline' => 'march_31st'
            ],
            [
                'base_name' => 'ضريبة العقارات التجارية',
                'base_description' => 'ضريبة سنوية على العقارات التجارية والصناعية',
                'base_rate' => 4.0000,
                'type' => 'property',
                'category' => 'real_estate',
                'frequency' => 'annual',
                'deadline' => 'march_31st'
            ],
            [
                'base_name' => 'ضريبة الشركات',
                'base_description' => 'ضريبة على أرباح الشركات والمؤسسات التجارية',
                'base_rate' => 22.0000,
                'type' => 'corporate',
                'category' => 'business',
                'frequency' => 'annual',
                'deadline' => 'april_30th'
            ],
            [
                'base_name' => 'ضريبة الدمغة',
                'base_description' => 'ضريبة على العقود والمعاملات الرسمية',
                'base_rate' => 0.5000,
                'type' => 'stamp_duty',
                'category' => 'transaction',
                'frequency' => 'as_needed',
                'deadline' => 'at_registration'
            ],
            [
                'base_name' => 'ضريبة الاستقطاع',
                'base_description' => 'ضريبة استقطاع على المدفوعات للمقاولين والموردين',
                'base_rate' => 5.0000,
                'type' => 'withholding',
                'category' => 'payment',
                'frequency' => 'monthly',
                'deadline' => '15th_of_month'
            ],
            [
                'base_name' => 'ضريبة السياحة',
                'base_description' => 'ضريبة على الخدمات السياحية والفنادق',
                'base_rate' => 10.0000,
                'type' => 'tourism',
                'category' => 'service',
                'frequency' => 'monthly',
                'deadline' => '15th_of_month'
            ],
            [
                'base_name' => 'ضريبة التنمية الحضرية',
                'base_description' => 'ضريبة لدعم مشاريع البنية التحتية الحضرية',
                'base_rate' => 1.0000,
                'type' => 'development',
                'category' => 'infrastructure',
                'frequency' => 'as_needed',
                'deadline' => 'at_permit_issuance'
            ],
            [
                'base_name' => 'ضريبة البيئة',
                'base_description' => 'ضريبة بيئية على الأنشطة المؤثرة على البيئة',
                'base_rate' => 0.2500,
                'type' => 'environmental',
                'category' => 'environment',
                'frequency' => 'quarterly',
                'deadline' => 'end_of_quarter'
            ]
        ];

        $taxes = [];
        $currentYear = now()->year;

        foreach ($taxTypes as $index => $taxType) {
            // Add dynamic variations
            $rateVariation = (rand(-10, 10) / 100); // ±10% variation
            $adjustedRate = $taxType['base_rate'] * (1 + $rateVariation);
            
            // Random effective date within last 3 years
            $effectiveYear = $currentYear - rand(0, 3);
            $effectiveMonth = rand(1, 12);
            $effectiveDay = rand(1, 28);
            $effectiveDate = "{$effectiveYear}-{$effectiveMonth}-{$effectiveDay}";

            // Random expiry date (some taxes don't expire)
            $expiryDate = null;
            if (rand(1, 10) <= 2) { // 20% chance to have expiry date
                $expiryYear = $currentYear + rand(1, 5);
                $expiryMonth = rand(1, 12);
                $expiryDay = rand(1, 28);
                $expiryDate = "{$expiryYear}-{$expiryMonth}-{$expiryDay}";
            }

            // Random active status
            $isActive = true;
            if ($taxType['type'] === 'environmental') {
                $isActive = false; // Environmental tax starts in 2025
            } elseif (rand(1, 20) <= 1) { // 5% chance to be inactive
                $isActive = false;
            }

            // Generate dynamic metadata
            $metadata = $this->generateDynamicMetadata($taxType, $index);

            $taxes[] = [
                'name' => $taxType['base_name'],
                'description' => $taxType['base_description'],
                'rate' => round($adjustedRate, 4),
                'type' => $taxType['type'],
                'is_active' => $isActive,
                'effective_date' => $effectiveDate,
                'expiry_date' => $expiryDate,
                'metadata' => json_encode($metadata),
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        DB::table('taxes')->insert($taxes);
    }

    /**
     * Generate dynamic metadata for each tax type
     */
    private function generateDynamicMetadata($taxType, $index): array
    {
        $baseMetadata = [
            'category' => $taxType['category'],
            'filing_frequency' => $taxType['frequency'],
            'payment_deadline' => $taxType['deadline'],
            'created_by' => 'system',
            'version' => '1.' . rand(0, 9),
            'last_reviewed' => now()->subDays(rand(1, 180))->format('Y-m-d'),
            'compliance_score' => rand(85, 100),
        ];

        // Add specific metadata based on tax type
        switch ($taxType['type']) {
            case 'vat':
                return array_merge($baseMetadata, [
                    'applicable_to' => ['goods', 'services'],
                    'exemptions' => ['basic_food', 'education', 'healthcare'],
                    'threshold_amount' => rand(50000, 100000),
                    'digital_services' => true,
                    'cross_border' => rand(0, 1) === 1,
                ]);

            case 'income':
                return array_merge($baseMetadata, [
                    'brackets' => 'progressive',
                    'deductions' => ['personal_exemption', 'dependents', 'charity'],
                    'tax_brackets' => [
                        ['min' => 0, 'max' => 5000, 'rate' => 10],
                        ['min' => 5001, 'max' => 20000, 'rate' => 15],
                        ['min' => 20001, 'max' => 50000, 'rate' => 20],
                        ['min' => 50001, 'max' => null, 'rate' => 25]
                    ],
                    'personal_exemption' => rand(8000, 12000),
                    'dependent_exemption' => rand(2000, 4000),
                ]);

            case 'property':
                $isResidential = $index === 2; // First property tax is residential
                return array_merge($baseMetadata, [
                    'frequency' => 'annual',
                    'property_types' => $isResidential 
                        ? ['residential', 'apartment', 'villa']
                        : ['commercial', 'industrial', 'office', 'retail'],
                    'exemptions' => $isResidential 
                        ? ['primary_residence_up_to_100k', 'senior_citizens', 'disabled']
                        : ['government_buildings', 'educational_institutions'],
                    'calculation_method' => $isResidential 
                        ? 'assessed_value_percentage' 
                        : 'market_value_percentage',
                    'assessment_factor' => rand(60, 80) / 100,
                ]);

            case 'corporate':
                return array_merge($baseMetadata, [
                    'company_types' => ['llc', 'corporation', 'partnership'],
                    'deductions' => ['business_expenses', 'depreciation', 'rd_costs'],
                    'tax_credits' => ['investment_incentive', 'job_creation', 'green_energy'],
                    'small_business_threshold' => rand(250000, 500000),
                    'rd_credit_rate' => rand(10, 25),
                ]);

            case 'stamp_duty':
                return array_merge($baseMetadata, [
                    'applicable_to' => ['contracts', 'property_deeds', 'agreements'],
                    'payment_timing' => 'at_registration',
                    'exemptions' => ['government_contracts', 'charity_transfers'],
                    'calculation_basis' => 'transaction_value',
                    'min_amount' => rand(100, 500),
                ]);

            case 'withholding':
                return array_merge($baseMetadata, [
                    'applicable_to' => ['contractors', 'suppliers', 'consultants'],
                    'payment_types' => ['services', 'supplies', 'consultation'],
                    'exemptions' => ['government_entities', 'certified_small_businesses'],
                    'threshold_amount' => rand(1000, 5000),
                ]);

            case 'tourism':
                return array_merge($baseMetadata, [
                    'applicable_to' => ['hotels', 'resorts', 'tour_guides', 'restaurants'],
                    'exemptions' => ['eco_tourism', 'heritage_sites', 'educational_tours'],
                    'seasonal_rates' => true,
                    'peak_season_surcharge' => rand(2, 5),
                ]);

            case 'development':
                return array_merge($baseMetadata, [
                    'applicable_to' => ['construction_permits', 'building_licenses'],
                    'payment_timing' => 'at_permit_issuance',
                    'fund_allocation' => ['roads', 'sewage', 'parks', 'lighting'],
                    'exemptions' => ['affordable_housing', 'public_facilities'],
                    'area_based_rates' => true,
                ]);

            case 'environmental':
                return array_merge($baseMetadata, [
                    'applicable_to' => ['industrial_emissions', 'waste_disposal', 'water_usage'],
                    'green_incentives' => ['renewable_energy', 'waste_recycling', 'water_conservation'],
                    'penalty_rates' => ['high_emissions' => 0.5, 'excessive_waste' => 0.75],
                    'carbon_credit_system' => true,
                ]);

            default:
                return $baseMetadata;
        }
    }
}
