<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmartProperty;
use App\Models\Property;
use App\Models\IoTDevice;

class SmartPropertySeeder extends Seeder
{
    public function run(): void
    {
        // Create sample IoT devices only (without smart property constraint)
        IoTDevice::create([
            'smart_property_id' => null,
            'device_type' => 'thermostat',
            'brand' => 'Nest',
            'model' => 'Learning Thermostat',
            'serial_number' => 'NST001',
            'mac_address' => '00:11:22:33:44:55',
            'ip_address' => '192.168.1.100',
            'firmware_version' => '5.9.3',
            'status' => 'active'
        ]);

        IoTDevice::create([
            'smart_property_id' => null,
            'device_type' => 'sensor',
            'brand' => 'Philips',
            'model' => 'Hue Motion Sensor',
            'serial_number' => 'PHS001',
            'mac_address' => '00:11:22:33:44:66',
            'ip_address' => '192.168.1.101',
            'firmware_version' => '2.4.1',
            'status' => 'active'
        ]);

        IoTDevice::create([
            'smart_property_id' => null,
            'device_type' => 'lock',
            'brand' => 'August',
            'model' => 'Smart Lock Pro',
            'serial_number' => 'AUG001',
            'mac_address' => '00:11:22:33:44:77',
            'ip_address' => '192.168.1.102',
            'firmware_version' => '3.7.2',
            'status' => 'active'
        ]);

        $this->command->info('Sample IoT devices created successfully!');
    }
}
