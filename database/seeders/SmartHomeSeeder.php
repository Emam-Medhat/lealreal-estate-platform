<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\SmartProperty;
use App\Models\IoTDevice;

class SmartHomeSeeder extends Seeder
{
    public function run(): void
    {
        // Get 5 random properties
        $properties = Property::inRandomOrder()->limit(5)->get();

        if ($properties->isEmpty()) {
            $this->command->warn('No properties found. Please seed properties first.');
            return;
        }

        foreach ($properties as $property) {
            // Check if smart property already exists
            $smartProperty = SmartProperty::firstOrCreate(
                ['property_id' => $property->id],
                [
                    'has_energy_monitoring' => true,
                    'has_smart_thermostat' => true,
                    'has_smart_lighting' => true,
                    'has_smart_security' => true,
                    'energy_efficiency_score' => rand(80, 100),
                    'smart_features' => 'Advanced Automation, Energy Monitoring, Security System',
                    'smart_home_description' => 'A fully integrated smart home with energy monitoring capabilities.',
                    'automation_system' => 'HomeAssistant',
                    'security_system' => 'Ring',
                    'energy_management_system' => 'Sense',
                ]
            );

            // Create IoT Devices for this property
            $this->createDevicesForProperty($smartProperty);
        }

        $this->command->info('Smart Home data seeded successfully!');
    }

    private function createDevicesForProperty(SmartProperty $smartProperty)
    {
        // Thermostat
        IoTDevice::create([
            'smart_property_id' => $smartProperty->id,
            'device_type' => 'thermostat',
            'brand' => 'Nest',
            'model' => 'Learning Thermostat',
            'serial_number' => 'NST-' . uniqid(),
            'mac_address' => $this->generateMacAddress(),
            'ip_address' => '192.168.1.' . rand(10, 200),
            'status' => 'active',
            'room_name' => 'Living Room',
            'location_within_property' => 'Living Room',
            'power_consumption_watts' => 5,
            'installation_date' => now()->subMonths(rand(1, 12)),
        ]);

        // Smart Plug
        IoTDevice::create([
            'smart_property_id' => $smartProperty->id,
            'device_type' => 'plug',
            'brand' => 'TP-Link',
            'model' => 'Kasa Smart Plug',
            'serial_number' => 'TPL-' . uniqid(),
            'mac_address' => $this->generateMacAddress(),
            'ip_address' => '192.168.1.' . rand(10, 200),
            'status' => 'active',
            'room_name' => 'Kitchen',
            'location_within_property' => 'Kitchen',
            'power_consumption_watts' => 1,
            'installation_date' => now()->subMonths(rand(1, 12)),
        ]);

        // Energy Meter
        IoTDevice::create([
            'smart_property_id' => $smartProperty->id,
            'device_type' => 'meter',
            'brand' => 'Sense',
            'model' => 'Energy Monitor',
            'serial_number' => 'SNS-' . uniqid(),
            'mac_address' => $this->generateMacAddress(),
            'ip_address' => '192.168.1.' . rand(10, 200),
            'status' => 'active',
            'room_name' => 'Garage',
            'location_within_property' => 'Garage',
            'power_consumption_watts' => 2,
            'installation_date' => now()->subMonths(rand(1, 12)),
        ]);
    }

    private function generateMacAddress()
    {
        return implode(':', array_map(function() {
            return sprintf('%02X', mt_rand(0, 255));
        }, range(0, 5)));
    }
}
