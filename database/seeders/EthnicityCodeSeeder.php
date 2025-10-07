<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EthnicityCode;

class EthnicityCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ethnicityCodes = [
            // White
            ['code' => 'A', 'description' => 'English, Welsh, Scottish, Northern Irish or British', 'category' => 'White'],
            ['code' => 'B', 'description' => 'Irish', 'category' => 'White'],
            ['code' => 'C', 'description' => 'Gypsy or Irish Traveller', 'category' => 'White'],
            ['code' => 'D', 'description' => 'Roma', 'category' => 'White'],
            ['code' => 'E', 'description' => 'Any other White background', 'category' => 'White'],
            
            // Mixed or Multiple ethnic groups
            ['code' => 'F', 'description' => 'White and Black Caribbean', 'category' => 'Mixed or Multiple ethnic groups'],
            ['code' => 'G', 'description' => 'White and Black African', 'category' => 'Mixed or Multiple ethnic groups'],
            ['code' => 'H', 'description' => 'White and Asian', 'category' => 'Mixed or Multiple ethnic groups'],
            ['code' => 'J', 'description' => 'Any other Mixed or Multiple ethnic background', 'category' => 'Mixed or Multiple ethnic groups'],
            
            // Asian or Asian British
            ['code' => 'K', 'description' => 'Indian', 'category' => 'Asian or Asian British'],
            ['code' => 'L', 'description' => 'Pakistani', 'category' => 'Asian or Asian British'],
            ['code' => 'M', 'description' => 'Bangladeshi', 'category' => 'Asian or Asian British'],
            ['code' => 'N', 'description' => 'Chinese', 'category' => 'Asian or Asian British'],
            ['code' => 'P', 'description' => 'Any other Asian background', 'category' => 'Asian or Asian British'],
            
            // Black, Black British, Caribbean or African
            ['code' => 'R', 'description' => 'African', 'category' => 'Black, Black British, Caribbean or African'],
            ['code' => 'S', 'description' => 'Caribbean', 'category' => 'Black, Black British, Caribbean or African'],
            ['code' => 'T', 'description' => 'Any other Black, Black British, or Caribbean background', 'category' => 'Black, Black British, Caribbean or African'],
            
            // Other ethnic group
            ['code' => 'W', 'description' => 'Arab', 'category' => 'Other ethnic group'],
            ['code' => 'X', 'description' => 'Any other ethnic group', 'category' => 'Other ethnic group'],
            
            // Not stated
            ['code' => 'Z', 'description' => 'Not stated', 'category' => 'Not stated'],
        ];

        foreach ($ethnicityCodes as $code) {
            EthnicityCode::create($code);
        }
    }
}