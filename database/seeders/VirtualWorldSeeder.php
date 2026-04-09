<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Metaverse\VirtualWorld;

class VirtualWorldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $worlds = [
            [
                'name' => 'Decentraland',
                'description' => 'A 3D virtual world browser-based platform. Users may buy virtual plots of land in the platform as NFTs via the MANA cryptocurrency.',
                'world_type' => 'open_world',
                'theme' => 'realistic',
                'access_level' => 'public',
                'max_avatars' => 10000,
                'status' => 'active',
                'is_active' => true,
                'creator_id' => 1,
            ],
            [
                'name' => 'The Sandbox',
                'description' => 'A virtual world where players can build, own, and monetize their gaming experiences in the Ethereum blockchain.',
                'world_type' => 'voxel',
                'theme' => 'voxel',
                'access_level' => 'public',
                'max_avatars' => 20000,
                'status' => 'active',
                'is_active' => true,
                'creator_id' => 1,
            ],
            [
                'name' => 'Somnium Space',
                'description' => 'An open, social & persistent Virtual Reality world. Buy land, build or import objects, instantly monetize and share.',
                'world_type' => 'vr',
                'theme' => 'realistic',
                'access_level' => 'public',
                'max_avatars' => 5000,
                'status' => 'active',
                'is_active' => true,
                'creator_id' => 1,
            ],
            [
                'name' => 'Cryptovoxels',
                'description' => 'A virtual world powered by the Ethereum blockchain. Players can buy land and build stores and art galleries.',
                'world_type' => 'voxel',
                'theme' => 'voxel',
                'access_level' => 'public',
                'max_avatars' => 8000,
                'status' => 'active',
                'is_active' => true,
                'creator_id' => 1,
            ]
        ];

        foreach ($worlds as $world) {
            if (VirtualWorld::where('name', $world['name'])->doesntExist()) {
                VirtualWorld::create($world);
            }
        }
    }
}
