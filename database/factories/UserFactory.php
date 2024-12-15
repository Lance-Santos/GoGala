<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => 2, // Fixed role_id as specified
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->firstName(), // Optional middle name
            'last_name' => fake()->lastName(),
            'suffix' => fake()->optional()->word(), // Optional suffix (e.g., Jr., Sr.)
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'contact_number' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'), // Default password
            'bio' => fake()->optional()->text(200), // Optional bio
            'profile_img_url' => fake()->imageUrl(), // Random image URL
            'banner_img_url' => fake()->imageUrl(), // Random banner image URL
            'gender' => fake()->randomElement(['male', 'female']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
