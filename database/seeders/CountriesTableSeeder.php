<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'Egypt',
            'slug' => 'egypt',
            'description' => 'Egypt, a country linking northeast Africa with the Middle East, dates to the time of the pharaohs.',
            'seo_title' => 'Egypt',
            'seo_keywords' => 'Egypt, Africa, Middle East, Pharaohs',
            'seo_description' => 'Discover Egypt, a country known for its rich history and cultural heritage.',
            'robots' => 'index, follow',
            'status' => 1
        ]);

        Country::create([
            'name' => 'United Arab Emirates',
            'slug' => 'united-arab-emirates',
            'description' => 'The United Arab Emirates is a federation of seven emirates on the eastern side of the Arabian peninsula.',
            'seo_title' => 'United Arab Emirates',
            'seo_keywords' => 'UAE, Emirates, Middle East, Dubai, Abu Dhabi',
            'seo_description' => 'Explore the United Arab Emirates, a federation known for its modern cities and rich cultural heritage.',
            'robots' => 'index, follow',
            'status' => 1
        ]);

        // Add more countries as needed
    }
}

