<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialties = [
            [
                'name' => json_encode(['en' => 'Dentistry', 'ar' => 'طب الأسنان']),
                'slug' => json_encode(['en' => 'dentistry', 'ar' => 'طب-الأسنان']),
                'description' => json_encode(['en' => 'Dentistry focuses on oral health.', 'ar' => 'طب الأسنان يركز على صحة الفم.']),
                'country_id'=>1
            ],
            // Add more specialties here
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }
}
