<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;
use League\Csv\Reader;
use App\Models\Artist;
use App\Models\Album;

class AlbumSalesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Path to CSV file in storage/app/data/Data Reference (ALBUM SALES).csv
        $csvPath = storage_path('app/data/Data Reference (ALBUM SALES).csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at $csvPath");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        $artists = [];
        $albums = [];

        foreach ($records as $record) {
            $artistCode = $record['Artist Code'] ?? $faker->unique()->bothify('ART###');
            $artistName = $record['Artist Name'] ?? $faker->name;

            if (!isset($artists[$artistCode])) {
                $artist = Artist::create([
                    'code' => $artistCode,
                    'name' => $artistName,
                ]);
                $artists[$artistCode] = $artist;
            } else {
                $artist = $artists[$artistCode];
            }

            $albumName = $record['Album Name'] ?? $faker->word;
            $albumYear = $record['Year'] ?? $faker->year;
            $albumSales = $record['Sales'] ?? $faker->numberBetween(1000, 100000);
            $albumCoverPath = null; // No cover in CSV, can be added later

            Album::create([
                'artist_id' => $artist->id,
                'year' => $albumYear,
                'name' => $albumName,
                'sales' => $albumSales,
                'album_cover_path' => $albumCoverPath,
            ]);
        }
    }
}
