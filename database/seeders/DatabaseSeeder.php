<?php

namespace Database\Seeders;

use App\Models\PlannedOff;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\OffTime;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

     protected function seedServicesRelated() {
        $service1 = Service::create([
            'availability_upto' => '7',
            'service_time' => '10',
            'interval_time' => '5',
            'max_client_limit' => 3,

        ]);

        $thirdDayDate = Carbon::now()->addDays(2)->format('Y-m-d');

        $plannedOff1 = PlannedOff::create([
            'service_id' => $service1->id,
            'reason' => 'public holiday',
            'date' => $thirdDayDate,
            'is_fullday' => 1,

        ]);

        $timeSlot1 = TimeSlot::create([
            'service_id' => $service1->id,
            'monday_starttime' => '08:00',
            'monday_endtime' => '20:00',
            'tuesday_starttime' => '08:00',
            'tuesday_endtime' => '20:00',
            'wednesday_starttime' => '08:00',
            'wednesday_endtime' => '20:00',
            'thursday_starttime' => '08:00',
            'thursday_endtime' => '20:00',
            'friday_starttime' => '08:00',
            'friday_endtime' => '20:00',
            'saturday_starttime' => '08:00',
            'saturday_endtime' => '20:00',
            'sunday_starttime' => null,
            'sunday_endtime' => null,
        ]);

        $service2 = Service::create([
            'availability_upto' => '7',
            'service_time' => '60',
            'interval_time' => '10',
            'max_client_limit' => 3,

        ]);

        $plannedOff2 = PlannedOff::create([
            'service_id' => $service2->id,
            'reason' => 'public holiday',
            'date' => $thirdDayDate,
            'is_fullday' => 1,

        ]);

        $timeSlot2 = TimeSlot::create([
            'service_id' => $service2->id,
            'monday_starttime' => '08:00',
            'monday_endtime' => '20:00',
            'tuesday_starttime' => '08:00',
            'tuesday_endtime' => '20:00',
            'wednesday_starttime' => '08:00',
            'wednesday_endtime' => '20:00',
            'thursday_starttime' => '08:00',
            'thursday_endtime' => '20:00',
            'friday_starttime' => '08:00',
            'friday_endtime' => '20:00',
            'saturday_starttime' => '08:00',
            'saturday_endtime' => '20:00',
            'sunday_starttime' => null,
            'sunday_endtime' => null,
        ]);
    }

    protected function seedOffTimes() {
        $timeOff1 = OffTime::create([
            'reason' => 'Lunch Break',
            'start_time' => '12:00',
            'end_time' => '13:00',
            'is_active' => 1,
        ]);

        $timeOff2 = OffTime::create([
            'reason' => 'Cleanup Break',
            'start_time' => '15:00',
            'end_time' => '16:00',
            'is_active' => 1,
        ]);


    }

    public function run()
    {
        DB::transaction(function($table) {
            $this->seedServicesRelated();
            $this->seedOffTimes();
        });
    }
}
