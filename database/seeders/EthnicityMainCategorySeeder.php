<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EthnicityMainCategory;

class EthnicityMainCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainCategories = [
            [
                'code' => 'A',
                'name' => 'White',
                'description' => 'White ethnic groups',
                'sort_order' => 1,
            ],
            [
                'code' => 'B',
                'name' => 'Mixed or Multiple ethnic groups',
                'description' => 'Mixed or Multiple ethnic groups',
                'sort_order' => 2,
            ],
            [
                'code' => 'C',
                'name' => 'Asian or Asian British',
                'description' => 'Asian or Asian British ethnic groups',
                'sort_order' => 3,
            ],
            [
                'code' => 'D',
                'name' => 'Black, Black British, Caribbean or African',
                'description' => 'Black, Black British, Caribbean or African ethnic groups',
                'sort_order' => 4,
            ],
            [
                'code' => 'E',
                'name' => 'Other ethnic group',
                'description' => 'Other ethnic groups',
                'sort_order' => 5,
            ],
        ];

        foreach ($mainCategories as $category) {
            EthnicityMainCategory::create($category);
        }
    }
}
