<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gamification\Reward;

class RewardSeeder extends Seeder
{
    public function run()
    {
        $rewards = [
            [
                'name' => 'خصم 10% على الإعلان المميز',
                'description' => 'احصل على خصم 10% عند ترقية إعلانك إلى إعلان مميز',
                'points_required' => 500,
                'reward_type' => 'discount',
                'reward_value' => 10.00,
                'category' => 'listing',
                'icon' => 'fas fa-percentage',
                'is_active' => true,
            ],
            [
                'name' => 'إعلان مجاني لمدة أسبوع',
                'description' => 'انشر عقارك مجاناً لمدة أسبوع واحد',
                'points_required' => 300,
                'reward_type' => 'voucher',
                'reward_value' => 7.00,
                'category' => 'listing',
                'icon' => 'fas fa-home',
                'is_active' => true,
            ],
            [
                'name' => 'تقييم عقاري مجاني',
                'description' => 'احصل على تقييم احترافي مجاني لعقارك',
                'points_required' => 750,
                'reward_type' => 'service',
                'reward_value' => 0.00,
                'category' => 'service',
                'icon' => 'fas fa-chart-line',
                'is_active' => true,
            ],
            [
                'name' => 'حزمة التصوير الاحترافي',
                'description' => 'تصوير احترافي لعقارك مع 15 صورة عالية الجودة',
                'points_required' => 1000,
                'reward_type' => 'service',
                'reward_value' => 0.00,
                'category' => 'service',
                'icon' => 'fas fa-camera',
                'is_active' => true,
            ],
            [
                'name' => 'قسيمة شرائية بقيمة 100 ريال',
                'description' => 'قسيمة شرائية يمكن استخدامها في أي خدمة على المنصة',
                'points_required' => 1500,
                'reward_type' => 'voucher',
                'reward_value' => 100.00,
                'category' => 'general',
                'icon' => 'fas fa-gift',
                'is_active' => true,
            ],
            [
                'name' => 'عضوية بلاتينية لمدة شهر',
                'description' => 'استمتع بجميع مميزات العضوية البلاتينية لمدة شهر واحد',
                'points_required' => 2000,
                'reward_type' => 'item',
                'reward_value' => 0.00,
                'category' => 'membership',
                'icon' => 'fas fa-crown',
                'is_active' => true,
            ],
            [
                'name' => 'ترويج على وسائل التواصل الاجتماعي',
                'description' => 'ترويج إعلانك على حساباتنا في وسائل التواصل الاجتماعي',
                'points_required' => 800,
                'reward_type' => 'service',
                'reward_value' => 0.00,
                'category' => 'marketing',
                'icon' => 'fas fa-share-alt',
                'is_active' => true,
            ],
            [
                'name' => 'خصم 25% على حزمة التسويق',
                'description' => 'احصل على خصم 25% على أي حزمة تسويق',
                'points_required' => 1200,
                'reward_type' => 'discount',
                'reward_value' => 25.00,
                'category' => 'marketing',
                'icon' => 'fas fa-bullhorn',
                'is_active' => true,
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }
}
