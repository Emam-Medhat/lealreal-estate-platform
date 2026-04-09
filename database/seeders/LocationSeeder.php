<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $egypt = Country::create([
            'name' => 'Egypt',
            'name_ar' => 'مصر',
            'code' => 'EG'
        ]);

        $cairo = Governorate::create([
            'country_id' => $egypt->id,
            'name' => 'Cairo',
            'name_ar' => 'القاهرة'
        ]);

        $giza = Governorate::create([
            'country_id' => $egypt->id,
            'name' => 'Giza',
            'name_ar' => 'الجيزة'
        ]);

        // Cities in Cairo
        $newCairo = City::create([
            'governorate_id' => $cairo->id,
            'name' => 'New Cairo',
            'name_ar' => 'القاهرة الجديدة'
        ]);

        $maadi = City::create([
            'governorate_id' => $cairo->id,
            'name' => 'Maadi',
            'name_ar' => 'المعادي'
        ]);

        // Districts in New Cairo
        District::create([
            'city_id' => $newCairo->id,
            'name' => 'Fifth Settlement',
            'name_ar' => 'التجمع الخامس'
        ]);

        District::create([
            'city_id' => $newCairo->id,
            'name' => 'First Settlement',
            'name_ar' => 'التجمع الأول'
        ]);

        // Districts in Maadi
        District::create([
            'city_id' => $maadi->id,
            'name' => 'Degla',
            'name_ar' => 'دجلة'
        ]);

        // Cities in Giza
        $october = City::create([
            'governorate_id' => $giza->id,
            'name' => '6th of October',
            'name_ar' => '6 أكتوبر'
        ]);

        District::create([
            'city_id' => $october->id,
            'name' => 'Sheikh Zayed',
            'name_ar' => 'الشيخ زايد'
        ]);
    }
}
