<?php

// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'user',
                'description' => 'Standard user role with limited access.',
            ],
            [
                'name' => 'manager',
                'description' => 'Manager role with elevated access.',
            ],
            [
                'name' => 'business',
                'description' => 'Business role for commercial access.',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

