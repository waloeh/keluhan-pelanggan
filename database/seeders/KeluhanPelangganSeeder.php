<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class KeluhanPelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            // Buat tanggal keluhan dalam 6 bulan terakhir
            $keluhanCreatedAt = Carbon::now()
                ->subDays(rand(0, 180))
                ->subHours(rand(0, 23))
                ->subMinutes(rand(0, 59));

            $finalStatus = rand(0, 2); 
            $keluhanId = DB::table('keluhan_pelanggan')->insertGetId([
                'nama'           => $faker->name(),
                'email'          => $faker->unique()->safeEmail(),
                'nomor_hp'       => $faker->optional()->numerify('08##########'),
                'status_keluhan' => strval($finalStatus),
                'keluhan'        => $faker->realTextBetween(50, 255),
                'created_at'     => $keluhanCreatedAt,
            ]);

            $statusTimestamp = clone $keluhanCreatedAt;
            for ($s = 0; $s <= $finalStatus; $s++) {
                $statusTimestamp->addMinutes(rand(10, 1440)); // tambah 10 menit sampai dengan 1 hari
                DB::table('keluhan_status_his')->insert([
                    'keluhan_id'     => $keluhanId,
                    'status_keluhan' => strval($s),
                    'updated_at'     => clone $statusTimestamp,
                ]);
            }
        }
    }
}
