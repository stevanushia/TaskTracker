<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Based exactly on the ERD legend
        $categories = ['Todo', 'InProgress', 'Testing', 'Done', 'Pending'];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category]);
        }
    }
}
