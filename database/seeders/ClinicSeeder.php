<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinics = [
            [
                'name' => 'NHS Health Centre - London Bridge',
                'address' => '1 London Bridge, London SE1 9RT',
                'postcode' => 'SE1 9RT',
                'phone' => '020 7946 0958',
                'email' => 'reception@londonbridge.nhs.uk',
                'type' => 'NHS',
                'latitude' => 51.5074,
                'longitude' => -0.0877,
                'is_active' => true,
            ],
            [
                'name' => 'St Thomas Hospital GP Practice',
                'address' => 'Westminster Bridge Road, London SE1 7EH',
                'postcode' => 'SE1 7EH',
                'phone' => '020 7188 7188',
                'email' => 'gp@stthomas.nhs.uk',
                'type' => 'NHS',
                'latitude' => 51.4994,
                'longitude' => -0.1195,
                'is_active' => true,
            ],
            [
                'name' => 'The Harley Street Clinic',
                'address' => '35 Weymouth Street, London W1G 8BJ',
                'postcode' => 'W1G 8BJ',
                'phone' => '020 7034 8181',
                'email' => 'info@harleystreetclinic.com',
                'type' => 'Private',
                'latitude' => 51.5200,
                'longitude' => -0.1500,
                'is_active' => true,
            ],
            [
                'name' => 'Manchester Royal Infirmary GP',
                'address' => 'Oxford Road, Manchester M13 9WL',
                'postcode' => 'M13 9WL',
                'phone' => '0161 276 1234',
                'email' => 'gp@mri.nhs.uk',
                'type' => 'NHS',
                'latitude' => 53.4808,
                'longitude' => -2.2426,
                'is_active' => true,
            ],
            [
                'name' => 'Birmingham Health Centre',
                'address' => '5 Priory Queensway, Birmingham B4 6BS',
                'postcode' => 'B4 6BS',
                'phone' => '0121 200 2000',
                'email' => 'reception@birminghamhealth.nhs.uk',
                'type' => 'NHS',
                'latitude' => 52.4862,
                'longitude' => -1.8904,
                'is_active' => true,
            ],
        ];

        foreach ($clinics as $clinic) {
            Clinic::create($clinic);
        }
    }
}