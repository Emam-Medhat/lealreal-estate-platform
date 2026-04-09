<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'البطاقة البنكية (Visa / Mastercard)',
                'slug' => 'credit-card',
                'gateway' => 'paymob',
                'is_active' => true,
                'icon' => 'fas fa-credit-card',
                'order' => 1,
            ],
            [
                'name' => 'فودافون كاش (Vodafone Cash)',
                'slug' => 'vodafone-cash',
                'gateway' => 'paymob',
                'is_active' => true,
                'icon' => 'fas fa-mobile-alt',
                'order' => 2,
            ],
            [
                'name' => 'إنستا باي (InstaPay)',
                'slug' => 'instapay',
                'gateway' => 'bank',
                'is_active' => true,
                'icon' => 'fas fa-university',
                'order' => 3,
            ],
            [
                'name' => 'تحويل بنكي (Bank Transfer)',
                'slug' => 'bank-transfer',
                'gateway' => 'bank',
                'is_active' => true,
                'icon' => 'fas fa-money-check-alt',
                'order' => 4,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(['slug' => $method['slug']], $method);
        }
    }
}
