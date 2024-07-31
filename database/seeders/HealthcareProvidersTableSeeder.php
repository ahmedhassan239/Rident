<?php
namespace Database\Seeders;
use App\Models\Area;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use App\Models\HealthcareProvider;

class HealthcareProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Assuming Cairo and Alexandria are already in the cities table
        $cairo = Governorate::where('name', 'Cairo')->first();
        $alexandria = Governorate::where('name', 'Alexandria')->first();

        if ($cairo) {
            for ($i = 1; $i <= 10; $i++) {
                HealthcareProvider::create([
                    'name' => "Cairo Provider $i",
                    'slug' => "cairo-provider-$i",
                    'type' => rand(1, 3),
                    'country_id' => $cairo->country_id,
                    'governorate_id' => $cairo->governorate_id,
                    'city_id' => $cairo->id,
                    'specialties' => "Specialties of Cairo Provider $i",
                    'description' => "Description of Cairo Provider $i",
                    'overview' => "Overview of Cairo Provider $i",
                    'seo_title' => "SEO Title for Cairo Provider $i",
                    'seo_keywords' => "SEO Keywords for Cairo Provider $i",
                    'seo_description' => "SEO Description for Cairo Provider $i",
                    'robots' => 'index, follow',
                    'status' => 1,
                    'featured' => rand(0, 1)
                ]);
            }
        }

        if ($alexandria) {
            for ($i = 1; $i <= 10; $i++) {
                HealthcareProvider::create([
                    'name' => "Alexandria Provider $i",
                    'slug' => "alexandria-provider-$i",
                    'type' => rand(1, 3),
                    'country_id' => $alexandria->country_id,
                    'governorate_id' => $alexandria->governorate_id,
                    'city_id' => $alexandria->id,
                    'specialties' => "Specialties of Alexandria Provider $i",
                    'description' => "Description of Alexandria Provider $i",
                    'overview' => "Overview of Alexandria Provider $i",
                    'seo_title' => "SEO Title for Alexandria Provider $i",
                    'seo_keywords' => "SEO Keywords for Alexandria Provider $i",
                    'seo_description' => "SEO Description for Alexandria Provider $i",
                    'robots' => 'index, follow',
                    'status' => 1,
                    'featured' => rand(0, 1)
                ]);
            }
        }
    }
}
