<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Governorate;
use App\Models\Country;

class GovernoratesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Assuming Egypt already exists in the countries table
        $egypt = Country::where('name', 'Egypt')->first();

        if ($egypt) {
            $governorates = [
                ['name' => 'Cairo', 'slug' => 'cairo'],
                ['name' => 'Alexandria', 'slug' => 'alexandria'],
                ['name' => 'Giza', 'slug' => 'giza'],
                ['name' => 'Luxor', 'slug' => 'luxor'],
                ['name' => 'Aswan', 'slug' => 'aswan'],
            ];

            foreach ($governorates as $gov) {
                Governorate::create([
                    'name' => $gov['name'],
                    'slug' => $gov['slug'],
                    'country_id' => $egypt->id,
                    'description' => 'Description of ' . $gov['name'],
                    'seo_title' => $gov['name'],
                    'seo_keywords' => $gov['name'] . ', Egypt',
                    'seo_description' => 'Information about ' . $gov['name'],
                    'robots' => 'index, follow',
                    'status' => 1
                ]);
            }
        }
    }
}
