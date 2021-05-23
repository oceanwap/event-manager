<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed Events
        $event_id_1 = DB::table('events')->insertGetId([
            'title' => 'Event 1',
            'duration_minutes' => 5,
            'preparation_minutes' => 2,
            'bookable_in_advance_days' => 15,
            'participants_per_slot' => 1,
            'bookable_from' => Carbon::now()->format('Y-m-d H:m:s'),
            'bookable_until' => Carbon::now()->addDay(rand(2, 5))->format('Y-m-d H:m:s'),
        ]);

        $event_id_2 = DB::table('events')->insertGetId([
            'title' => 'Event 2',
            'duration_minutes' => 15,
            'preparation_minutes' => 0,
            'bookable_in_advance_days' => 30,
            'participants_per_slot' => 2,
            'bookable_from' => Carbon::now()->format('Y-m-d H:m:s'),
            'bookable_until' => Carbon::now()->addDay(rand(2, 5))->format('Y-m-d H:m:s'),
        ]);

        $event_id_3 = DB::table('events')->insertGetId([
            'title' => 'Event 3',
            'duration_minutes' => 30,
            'preparation_minutes' => 0,
            'bookable_in_advance_days' => 5,
            'participants_per_slot' => 3,
            'bookable_from' => Carbon::now()->format('Y-m-d H:m:s'),
            'bookable_until' => Carbon::now()->addDay(rand(2, 5))->format('Y-m-d H:m:s'),
        ]);

        // Seed Event Timings
        DB::table('event_timings')->insert([
            'event_id' => $event_id_1,
            'availabilty' => 1,
            'days' => 'mon,tue,wed,thu,fri',
            'from' => '08:00:00',
            'until' => '20:00:00'
        ]);

        DB::table('event_timings')->insert([
            'event_id' => $event_id_1,
            'availabilty' => 0,
            'days' => 'mon,tue,wed,thu,fri',
            'from' => '13:00:00',
            'until' => '14:00:00'
        ]);


        DB::table('event_timings')->insert([
            'event_id' => $event_id_2,
            'availabilty' => 1,
            'days' => 'mon,tue,wed,thu',
            'from' => '10:00:00',
            'until' => '18:00:00'
        ]);

        DB::table('event_timings')->insert([
            'event_id' => $event_id_2,
            'availabilty' => 0,
            'days' => 'mon,tue,wed,thu',
            'from' => '13:00:00',
            'until' => '14:00:00'
        ]);


        DB::table('event_timings')->insert([
            'event_id' => $event_id_3,
            'availabilty' => 1,
            'days' => 'mon,tue,wed,thu',
            'from' => '12:00:00',
            'until' => '19:00:00'
        ]);

        DB::table('event_timings')->insert([
            'event_id' => $event_id_3,
            'availabilty' => 0,
            'days' => 'mon,tue,wed,thu',
            'from' => '14:00:00',
            'until' => '15:00:00'
        ]);
        
    }
}
