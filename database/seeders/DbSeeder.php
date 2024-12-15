<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 users, each having 1 organization and each organization having 5 events.
        User::factory(100)
            ->has(
                Organization::factory() // Create 1 organization for each user
                    ->has(Event::factory(5)) // Each organization will have 5 events
            )
            ->create();
    }
}
