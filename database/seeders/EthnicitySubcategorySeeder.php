<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EthnicityMainCategory;
use App\Models\EthnicitySubcategory;

class EthnicitySubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get main categories
        $mainCategories = EthnicityMainCategory::all()->keyBy('code');

        $subcategories = [
            // A - White
            ['main_category_code' => 'A', 'code' => 'A1', 'name' => 'English, Welsh, Scottish, Northern Irish or British', 'sort_order' => 1],
            ['main_category_code' => 'A', 'code' => 'A2', 'name' => 'Irish', 'sort_order' => 2],
            ['main_category_code' => 'A', 'code' => 'A3', 'name' => 'Gypsy or Irish Traveller', 'sort_order' => 3],
            ['main_category_code' => 'A', 'code' => 'A4', 'name' => 'Roma', 'sort_order' => 4],
            ['main_category_code' => 'A', 'code' => 'A5', 'name' => 'Any other White background', 'sort_order' => 5],

            // B - Mixed or Multiple ethnic groups
            ['main_category_code' => 'B', 'code' => 'B1', 'name' => 'White and Black Caribbean', 'sort_order' => 1],
            ['main_category_code' => 'B', 'code' => 'B2', 'name' => 'White and Black African', 'sort_order' => 2],
            ['main_category_code' => 'B', 'code' => 'B3', 'name' => 'White and Asian', 'sort_order' => 3],
            ['main_category_code' => 'B', 'code' => 'B4', 'name' => 'Any other Mixed or Multiple ethnic background', 'sort_order' => 4],

            // C - Asian or Asian British
            ['main_category_code' => 'C', 'code' => 'C1', 'name' => 'Indian', 'sort_order' => 1],
            ['main_category_code' => 'C', 'code' => 'C2', 'name' => 'Pakistani', 'sort_order' => 2],
            ['main_category_code' => 'C', 'code' => 'C3', 'name' => 'Bangladeshi', 'sort_order' => 3],
            ['main_category_code' => 'C', 'code' => 'C4', 'name' => 'Chinese', 'sort_order' => 4],
            ['main_category_code' => 'C', 'code' => 'C5', 'name' => 'Any other Asian background', 'sort_order' => 5],

            // D - Black, Black British, Caribbean or African
            ['main_category_code' => 'D', 'code' => 'D1', 'name' => 'African', 'sort_order' => 1],
            ['main_category_code' => 'D', 'code' => 'D2', 'name' => 'Caribbean', 'sort_order' => 2],
            ['main_category_code' => 'D', 'code' => 'D3', 'name' => 'Any other Black, Black British, or Caribbean background', 'sort_order' => 3],

            // E - Other ethnic group
            ['main_category_code' => 'E', 'code' => 'E1', 'name' => 'Arab', 'sort_order' => 1],
            ['main_category_code' => 'E', 'code' => 'E2', 'name' => 'Any other ethnic group', 'sort_order' => 2],
        ];

        foreach ($subcategories as $subcategory) {
            $mainCategory = $mainCategories->get($subcategory['main_category_code']);
            if ($mainCategory) {
                EthnicitySubcategory::create([
                    'main_category_id' => $mainCategory->id,
                    'code' => $subcategory['code'],
                    'name' => $subcategory['name'],
                    'sort_order' => $subcategory['sort_order'],
                ]);
            }
        }
    }
}
