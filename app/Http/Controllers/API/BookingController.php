<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\OffTime;
use App\Models\PlannedOff;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\DB;
use stdClass;

class BookingController extends Controller
{
    public function getAvailableTimeSlots(Request $request){
        
        DB::disconnect();

        $date = $request->date;
        $service_id = $request->service_id;

        if($service_id != "" && $date != ""){
            
            $availableSlots = [];
            $service = Service::find($service_id);
            $startDate = Carbon::createFromFormat('Y-m-d', $date);

            $endDate = $startDate;
            $currentDate = $startDate;
            $obj = new stdClass();
            $obj->service = $service->name;

            $dayName = $currentDate->format('l');
            $dayName = strtolower($dayName);
            $dayStart = $dayName.'_starttime';
            $dayEnd = $dayName.'_endtime';
            $todayTimeSlot = $service->timeSlot->select($dayStart,$dayEnd)->first();

            $todayDate = $currentDate->format('Y-m-d');
            $holiday = PlannedOff::where('service_id',$service->id)->where('date',$todayDate)->first();
            $timeOffs = OffTime::where('is_active',1)->get();
            $existingBookings = $service->bookings()->where('date',$todayDate)->get();
            $obj->$todayDate = [] ;
            if(!$holiday){
                if($todayTimeSlot[$dayStart] != null && $todayTimeSlot[$dayEnd]){
                    $currentTime = $todayTimeSlot[$dayStart];
                    while(strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60 <= strtotime($todayTimeSlot[$dayEnd])){
                        $timeSlotStart = strtotime($currentTime);
                        $timeSlotEnd = strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60;
                        $serviceEndTime = strtotime($currentTime) + ($service->service_time) * 60;
                        foreach($timeOffs as $break){
                            if (date('H:i', $timeSlotStart) >= $break->start_time && date('H:i', $timeSlotEnd) < $break->end_time) {
                                // dd(date('H:i', $timeSlotStart) , $break->start_time);
                                $currentTime = date('H:i', strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60);
                                continue;
                            }
                        }
                        $serviceDuration = $service->service_time;

                        $conflict = $existingBookings->first(function ($appointment) use ($currentTime, $serviceDuration) {
                            return $currentTime >= $appointment->start_time && $currentTime < $appointment->end_time
                                || ($currentTime < $appointment->start_time && strtotime($currentTime) + $serviceDuration * 60 > strtotime($appointment->start_time));
                        });

                        if (!$conflict) {
                            array_push($obj->$todayDate, date('H:i',$timeSlotStart).'-'.date('H:i',$serviceEndTime));
                        }else if ($conflict && $existingBookings->count() < $service->max_client_limit){
                            array_push($obj->$todayDate, date('H:i',$timeSlotStart).'-'.date('H:i',$serviceEndTime));
                        }

                        $currentTime = date('H:i', strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60);
                    }
                }else{
                    array_push($obj->$todayDate, 'Holiday');
                }
            }else{
                if($holiday->is_fullday == 1){
                    array_push($obj->$todayDate, $holiday->reason);
                }else{

                }

            }
            array_push($availableSlots, $obj);

            return response()->json([
                'error' => false,
                'slots' => $availableSlots,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);

        }else if($service_id){
            $availableSlots = [];
            $service = Service::find($service_id);
            $startDate = now()->addDay();
            $endDate = $startDate->copy()->addDays($service->availability_upto);
            $currentDate = $startDate;
            $obj = new stdClass();
            $obj->service = $service->name;
        
            while ($currentDate <= $endDate){
                $dayName = $currentDate->format('l');
                $dayName = strtolower($dayName);
                $dayStart = $dayName.'_starttime';
                $dayEnd = $dayName.'_endtime';
                $todayTimeSlot = $service->timeSlot->select($dayStart,$dayEnd)->first();

                $todayDate = $currentDate->format('Y-m-d');
                $holiday = PlannedOff::where('service_id',$service->id)->where('date',$todayDate)->first();
                $timeOffs = OffTime::where('is_active',1)->get();
                $existingBookings = $service->bookings()->where('date',$todayDate)->get();
                $obj->$todayDate = [] ;
                if(!$holiday){
                    if($todayTimeSlot[$dayStart] != null && $todayTimeSlot[$dayEnd]){
                        $currentTime = $todayTimeSlot[$dayStart];
                        while(strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60 <= strtotime($todayTimeSlot[$dayEnd])){
                            $timeSlotStart = strtotime($currentTime);
                            $timeSlotEnd = strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60;
                            $serviceEndTime = strtotime($currentTime) + ($service->service_time) * 60;
                            foreach($timeOffs as $break){
                                if (date('H:i', $timeSlotStart) >= $break->start_time && date('H:i', $timeSlotEnd) < $break->end_time) {
                                    // dd(date('H:i', $timeSlotStart) , $break->start_time);
                                    $currentTime = date('H:i', strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60);
                                    continue;
                                }
                            }
                            $serviceDuration = $service->service_time;

                            $conflict = $existingBookings->first(function ($appointment) use ($currentTime, $serviceDuration) {
                                return $currentTime >= $appointment->start_time && $currentTime < $appointment->end_time
                                    || ($currentTime < $appointment->start_time && strtotime($currentTime) + $serviceDuration * 60 > strtotime($appointment->start_time));
                            });

                            if (!$conflict) {
                                array_push($obj->$todayDate, date('H:i',$timeSlotStart).'-'.date('H:i',$serviceEndTime));
                            }else if ($conflict && $existingBookings->count() < $service->max_client_limit){
                                array_push($obj->$todayDate, date('H:i',$timeSlotStart).'-'.date('H:i',$serviceEndTime));
                            }

                            $currentTime = date('H:i', strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60);
                        }
                    }else{
                        array_push($obj->$todayDate, 'Holiday');
                    }
                }else{
                    if($holiday->is_fullday == 1){
                        array_push($obj->$todayDate, $holiday->reason);
                    }else{

                    }

                }
                $currentDate->addDay();
                
            }
            array_push($availableSlots, $obj);

            return response()->json([
                'error' => false,
                'slots' => $availableSlots,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);

        }else{
            $availableSlots = [];
            $services = Service::all();
            foreach ($services as $service){
                $startDate = now()->addDay();
                $endDate = $startDate->copy()->addDays($service->availability_upto);
                $currentDate = $startDate;
                $obj = new stdClass();
                $obj->service = $service->name;
            
                while ($currentDate <= $endDate){
                    $dayName = $currentDate->format('l');
                    $dayName = strtolower($dayName);
                    $dayStart = $dayName.'_starttime';
                    $dayEnd = $dayName.'_endtime';
                    $todayTimeSlot = $service->timeSlot->select($dayStart,$dayEnd)->first();

                    $todayDate = $currentDate->format('Y-m-d');
                    $holiday = PlannedOff::where('service_id',$service->id)->where('date',$todayDate)->first();
                    $timeOffs = OffTime::where('is_active',1)->get();
                    $existingBookings = $service->bookings()->where('date',$todayDate)->get();
                    $obj->$todayDate = [] ;
                    if(!$holiday){
                        if($todayTimeSlot[$dayStart] != null && $todayTimeSlot[$dayEnd]){
                            $currentTime = $todayTimeSlot[$dayStart];
                            while(strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60 <= strtotime($todayTimeSlot[$dayEnd])){
                                $timeSlotStart = strtotime($currentTime);
                                $timeSlotEnd = strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60;
                                $serviceEndTime = strtotime($currentTime) + ($service->service_time) * 60;
                                foreach($timeOffs as $break){
                                    if (date('H:i', $timeSlotStart) >= $break->start_time && date('H:i', $timeSlotEnd) < $break->end_time) {
                                        // dd(date('H:i', $timeSlotStart) , $break->start_time);
                                        $currentTime = date('H:i', strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60);
                                        continue;
                                    }
                                }
                                $serviceDuration = $service->service_time;

                                $conflict = $existingBookings->first(function ($appointment) use ($currentTime, $serviceDuration) {
                                    return $currentTime >= $appointment->start_time && $currentTime < $appointment->end_time
                                        || ($currentTime < $appointment->start_time && strtotime($currentTime) + $serviceDuration * 60 > strtotime($appointment->start_time));
                                });

                                if (!$conflict) {
                                    array_push($obj->$todayDate, date('H:i',$timeSlotStart).'-'.date('H:i',$serviceEndTime));
                                }else if ($conflict && $existingBookings->count() < $service->max_client_limit){
                                    array_push($obj->$todayDate, date('H:i',$timeSlotStart).'-'.date('H:i',$serviceEndTime));
                                }

                                $currentTime = date('H:i', strtotime($currentTime) + ($service->service_time + $service->interval_time) * 60);
                            }
                        }else{
                            array_push($obj->$todayDate, 'Holiday');
                        }
                    }else{
                        if($holiday->is_fullday == 1){
                            array_push($obj->$todayDate, $holiday->reason);
                        }else{

                        }

                    }
                    $currentDate->addDay();
                    
                }
                array_push($availableSlots, $obj);
            }

            return response()->json([
                'error' => false,
                'slots' => $availableSlots,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
            
        }
    }

    public function bookAvailableTimeSlot(Request $request){

        $service = Service::find($request->service_id);
        $existingBookings = $service->bookings()->where('date',$request->date)->get();
        $holiday = PlannedOff::where('service_id',$request->service_id)->where('date',$request->date)->first();
        try{
            if($holiday){
                return response()->json([
                    'error' => true,
                    'reason' => $holiday->reason,
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNPROCESSABLE_ENTITY]
                ], HttpStatusCode::UNPROCESSABLE_ENTITY);
            }
    
            $serviceDuration = $service->service_time;
            $start_time = $request->start_time;
    
            $conflict = $existingBookings->first(function ($appointment) use ($start_time, $serviceDuration) {
                return $start_time >= $appointment->start_time && $start_time < $appointment->end_time
                    || ($start_time < $appointment->start_time && strtotime($start_time) + $serviceDuration * 60 > strtotime($appointment->start_time));
            });
    
            if($conflict && $existingBookings->count() >= $service->max_client_limit){
                return response()->json([
                    'error' => true,
                    'reason' => __('This time Slot is already Booked'),
                    'message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNPROCESSABLE_ENTITY]
                ], HttpStatusCode::UNPROCESSABLE_ENTITY);
            }
            
            $data = [];
            $data['service_id'] = $request->service_id;
            $data['date'] = $request->date;
            $data['start_time'] = $request->start_time;
            $data['end_time'] = $request->end_time;
            $data['first_name'] = $request->first_name;
            $data['last_name'] = $request->last_name;
            $data['email'] = $request->email;
            
            $appointment = Booking::create($data);
    
            return response()->json([
                'error' => false,
                'slots' =>  $appointment,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]
            ], HttpStatusCode::OK);
            
        }catch (\Exception $e) {
            // Log::error(['Api/BookingController -> SlotBook', $e->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
