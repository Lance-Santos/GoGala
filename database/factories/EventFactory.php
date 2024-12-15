<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'organization_id' => \App\Models\Organization::factory(),
            'event_name' => $this->faker->sentence(3),
            'event_slug' => $this->faker->slug,
            'event_date_start' => $this->faker->dateTimeBetween('now', '+1 month'),
            'event_date_end' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'event_time_start' => $this->faker->time(),
            'event_time_end' => $this->faker->time(),
            'event_latitude' => $this->faker->latitude,
            'event_longitude' => $this->faker->longitude,
            'event_address_string' => $this->faker->address,
            'event_description' => $this->faker->text(300),
            'event_img_url' => $this->faker->imageUrl(),
            'event_img_banner_url' => $this->faker->imageUrl(),
            'event_status' => $this->faker->randomElement(['Private', 'Public', 'Unlisted']),
            'event_type' => $this->faker->randomElement(['ticket', 'seating', 'free']),
            'hasEnded' => $this->faker->boolean(100), // 30% chance of being true
        ];
    }
}
