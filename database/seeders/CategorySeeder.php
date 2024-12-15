<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Music',
            'Art',
            'IT',
            'Sports',
            'Health',
            'Business',
            'Education',
            'Food & Drinks',
            'Technology',
            'Fashion',
            'Travel',
            'Photography',
            'Film & Media',
            'Networking',
            'Community',
            'Spirituality',
            'Gaming',
            'Dance',
            'Literature',
            'Comedy'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
