<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\OffTime;
use App\Models\PlannedOff;

class BookingController extends Controller
{
    public function getAvailableTimeSlots(Request $request)
    {

        // In your controller method
    $date = $request->input('date'); // Get the desired date from the request
    
    if($date){

    }else{
        $availableSlots = [];
        $services = Service::all();
        foreach ($services as $service){
            $startDate = now()->addDay();
            $endDate = $startDate->copy()->addDays($service->availability_upto);

            $currentDate = $startDate;

            while ($currentDate <= $endDate){
                $dayName = $currentDate->format('l');
                $dayName = strtolower($dayName);
                $dayStart = $dayName.'_starttime';
                $dayEnd = $dayName.'_endtime';
                $todayTimeSlot = $service->timeSlot->select($dayStart,$dayEnd)->get();
                dd($service->timeSlot,$todayTimeSlot, $startDate, $endDate, $dayName);

                $currentDate->addDay();
            }

        }
    }
    // $existingAppointments = Appointment::where('date', $date)->get();

    // // Define time slot constraints
    // $startTime = '08:00';
    // $endTime = '20:00';
    // $serviceDuration = 10; // in minutes
    // $cleanupTime = 5; // in minutes
    // $lunchBreakStart = '12:00';
    // $lunchBreakEnd = '13:00';

    // // Calculate available time slots
    // $availableSlots = [];
    // $currentTime = $startTime;

    // while (strtotime($currentTime) + ($serviceDuration + $cleanupTime) * 60 <= strtotime($endTime)) {
    //     // Check if the current time falls within the lunch break
    //     if ($currentTime >= $lunchBreakStart && $currentTime < $lunchBreakEnd) {
    //         $currentTime = date('H:i', strtotime($currentTime) + ($serviceDuration + $cleanupTime) * 60);
    //         continue;
    //     }

    //     // Check if the current time conflicts with any existing appointments
    //     $conflict = $existingAppointments->first(function ($appointment) use ($currentTime, $serviceDuration) {
    //         return $currentTime >= $appointment->start_time && $currentTime < $appointment->end_time
    //             || ($currentTime < $appointment->start_time && strtotime($currentTime) + $serviceDuration * 60 > strtotime($appointment->start_time));
    //     });

    //     if (!$conflict) {
    //         $availableSlots[] = $currentTime;
    //     }

    //     $currentTime = date('H:i', strtotime($currentTime) + ($serviceDuration + $cleanupTime) * 60);
    // }

    // return response()->json($availableSlots);
    //     // Get the upcoming 7 days or 8 days (as per your requirement)
    //     $daysCount = 7;
    //     $endDate = Carbon::now()->addDays($daysCount);

    //     // Get the services with their time slots
    //     $services = Service::with('timeSlots')->get();

    //     $availableTimeSlots = [];

    //     foreach ($services as $service) {
    //         foreach ($service->timeSlots as $timeSlot) {
    //             $currentDate = Carbon::now();

    //             while ($currentDate <= $endDate) {
    //                 $dayOfWeek = $currentDate->dayOfWeek;

    //                 // Check if there is a planned off for this service on the current date
    //                 $plannedOff = PlannedOff::where('service_id', $service->id)
    //                     ->where('date', $currentDate->format('Y-m-d'))
    //                     ->first();

    //                 if (!$plannedOff) {
    //                     // Check if there is an off time (lunch or coffee break) on the current date and time slot
    //                     $offTime = OffTime::where('day_of_week', $dayOfWeek)
    //                         ->where(function ($query) use ($timeSlot) {
    //                             $query->where('start_time', '<=', $timeSlot->start_time)
    //                                 ->where('end_time', '>=', $timeSlot->end_time);
    //                         })
    //                         ->first();

    //                     if (!$offTime) {
    //                         $startTime = Carbon::parse($timeSlot->start_time);
    //                         $endTime = Carbon::parse($timeSlot->end_time);
    //                         $slots = [];

    //                         while ($startTime->addMinutes($timeSlot->duration)->lte($endTime)) {
    //                             $slots[] = $startTime->format('H:i');
    //                             $startTime->addMinutes($timeSlot->cleanup_time);
    //                         }

    //                         $availableTimeSlots[$currentDate->format('Y-m-d')][$service->name] = $slots;
    //                     }
    //                 }

    //                 $currentDate->addDay();
    //             }
    //         }
    //     }

    //     return response()->json($availableTimeSlots);
    }
}
