<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SubSpecialty;

class SubSpecialtiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubSpecialty::create([
            'name' => 'Example Sub Specialty',
            'slug' => 'example-sub-specialty',
            'description' => 'Description of the sub specialty',
            'country_id' => 1, // Assume a valid ID
            'specialtie_id' => 1, // Assume a valid ID
           'overview' => 'Overview of the sub specialty',
            'seo_title' => 'SEO Title',
            'seo_keywords' => 'SEO, Keywords',
            'seo_description' => 'SEO Description',
            'robots' => 'index, follow',
            'status' => 1
        ]);

        // Add more entries as needed
    }
}
