<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), // Associate with a user
            'organization_name' => $this->faker->company,
            'organization_slug' => $this->faker->slug,
            'organization_bio' => $this->faker->text(200),
            'organization_email' => $this->faker->unique()->companyEmail,
            'img_url_profile' => $this->faker->imageUrl(),
            'img_url_background' => $this->faker->imageUrl(),
        ];
    }
}
