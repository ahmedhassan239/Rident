<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

use App\Models\ScheduleDay;
use App\Models\ScheduleDayTime;
use App\Models\ScheduleDayTimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateInterval;
use DateTime;


class ScheduleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getDaySlots']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::get(); // Assuming you have a 'days' relationship defined in your Schedule model
        return response()->json($schedules);
    }


    /**
     * Show the form for creating a new resource.
     */


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|integer',
            //  'doctor_id' => 'required|integer',
            'schedule_gap' => 'required|integer',
            'schedule_meeting_time' => 'required',
            'days' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // try {
        DB::beginTransaction();

        $schedule = Schedule::updateOrCreate(
            [
                'provider_id' => 1,
                //  'doctor_id' => $request->doctor_id
            ],
            [
                'schedule_gap' => $request->schedule_gap,
                'schedule_meeting_time' => $request->schedule_meeting_time
            ]
        );

        foreach ($request->days as $day) {

            $scheduleDay = ScheduleDay::updateOrCreate([
                'schedule_id' => $schedule->id,
                'day_name' => $day['day_name'],
            ], [
                'day_number' => $day['day_number'],
                'status' => $day['status']
            ]);

            ScheduleDayTime::where('schedule_day_id', $scheduleDay->id)->delete();

            foreach ($day['times'] as $time) {

                $timeStartFromObj = new DateTime($time['start_from']);
                $timeEndToObj = new DateTime($time['end_to']);

                $timeStartFrom = $timeStartFromObj->format("H:i");
                $timeEndTo = $timeEndToObj->format("H:i");


                $scheduleDayTime = ScheduleDayTime::create([
                    'schedule_day_id' => $scheduleDay->id,
                    'start_from' => $timeStartFrom,
                    'end_to' => $timeEndTo
                ]);

                $number_of_slots = round((abs($timeStartFromObj->getTimestamp() - $timeEndToObj->getTimestamp()) / 60) / ($request->schedule_gap + $request->schedule_meeting_time));

                $slotStartFromObj = $timeStartFromObj; //10
                for ($i = 0; $i < $number_of_slots; $i++) {

                    ScheduleDayTimeSlot::create([
                        'schedule_day_time_id' => $scheduleDayTime->id,
                        'start_from' => $slotStartFromObj->format("H:i"),
                        'end_to' => $slotStartFromObj->add(new DateInterval('PT' . (int) $request->schedule_meeting_time . 'M'))->format("H:i"),
                    ]);

                    $slotStartFromObj->add(new DateInterval('PT' . (int) $request->schedule_gap . 'M'));
                }
            }
        }

        DB::commit();

        return response()->json(['message' => 'Schedule created successfully'], 200);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json(['error' => 'Failed to create schedule: ' . $e->getMessage()], 500);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Retrieve the schedule with its related 'ScheduleDay' and nested 'ScheduleDayTimes'
            $schedule = Schedule::with(['scheduleDays.scheduleDayTimes'])->findOrFail($id);

            // Transform the schedule data into the desired structure
            $result = [
                'provider_id' => $schedule->provider_id,  // Assuming 'provider_id' exists in 'Schedule'
                'schedule_gap' => $schedule->schedule_gap,  // Assuming 'schedule_gap' exists
                'schedule_meeting_time' => $schedule->schedule_meeting_time,  // Assuming 'schedule_meeting_time' exists
                'days' => $schedule->scheduleDays->map(function ($day) {
                    return [
                        'day_number' => $day->day_number,  // Assuming 'day_number' exists in 'ScheduleDay'
                        'day_name' => $day->day_name,  // Assuming 'day_name' exists
                        'status' => $day->status,  // Assuming 'status' exists
                        'times' => $day->scheduleDayTimes->map(function ($time) {
                            return [
                                'start_from' => $time->start_from->isoFormat('H:mm'),  // Assuming 'start_from' exists in 'ScheduleDayTime'
                                'end_to' => $time->end_to->isoFormat('H:mm')  // Assuming 'end_to' exists
                            ];
                        })
                    ];
                })
            ];

            // Format and return the response
            return response()->json($result);
        } catch (\Exception $e) {
            // Handle the case where the schedule is not found or other exceptions
            return response()->json(['error' => 'Error retrieving schedule: ' . $e->getMessage()], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $scheduleId)
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|integer',
            // 'doctor_id' => 'required|integer',
            'schedule_gap' => 'required|integer',
            'schedule_meeting_time' => 'required',
            'days' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // try {
        DB::beginTransaction();
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->provider_id = 1;
        // $schedule->doctor_id = $request->doctor_id;
        $schedule->schedule_gap = $request->schedule_gap;
        $schedule->schedule_meeting_time = $request->schedule_meeting_time;

        $schedule->save();

        foreach ($request->days as $day) {

            $scheduleDay = ScheduleDay::updateOrCreate([
                'schedule_id' => $schedule->id,
                'day_name' => $day['day_name'],
            ], [
                'day_number' => $day['day_number'],
                'status' => $day['status']
            ]);

            ScheduleDayTime::where('schedule_day_id', $scheduleDay->id)->delete();

            foreach ($day['times'] as $time) {

                $timeStartFromObj = new DateTime($time['start_from']);
                $timeEndToObj = new DateTime($time['end_to']);

                $timeStartFrom = $timeStartFromObj->format("H:i");
                $timeEndTo = $timeEndToObj->format("H:i");


                $scheduleDayTime = ScheduleDayTime::create([
                    'schedule_day_id' => $scheduleDay->id,
                    'start_from' => $timeStartFrom,
                    'end_to' => $timeEndTo
                ]);

                $number_of_slots = round((abs($timeStartFromObj->getTimestamp() - $timeEndToObj->getTimestamp()) / 60) / ($request->schedule_gap + $request->schedule_meeting_time));

                //set the time of the first slot
                $slotStartFromObj = $timeStartFromObj; //10
                for ($i = 0; $i < $number_of_slots; $i++) {

                    ScheduleDayTimeSlot::create([
                        'schedule_day_time_id' => $scheduleDayTime->id,
                        'start_from' => $slotStartFromObj->format("H:i"),
                        'end_to' => $slotStartFromObj->add(new DateInterval('PT' . (int) $request->schedule_meeting_time . 'M'))->format("H:i"),
                    ]);

                    $slotStartFromObj->add(new DateInterval('PT' . (int) $request->schedule_gap . 'M'));
                }
            }
        }

        DB::commit();

        return response()->json(['message' => 'Schedule created successfully'], 200);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json(['error' => 'Failed to create schedule: ' . $e->getMessage()], 500);
        // }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            // Optional: Delete related entries first if there are dependencies
            // Assuming there's a one-to-many relationship defined as 'scheduleDays'
            // This step is necessary if your foreign key constraints prevent deletion of a schedule without first removing the related entries
            $schedule->scheduleDays()->delete();

            // Now delete the schedule itself
            $schedule->delete();

            return response()->json(['message' => 'Schedule deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the schedule is not found
            return response()->json(['error' => 'Schedule not found'], 404);
        } catch (\Exception $e) {
            // Handle other possible exceptions
            return response()->json(['error' => 'Failed to delete schedule: ' . $e->getMessage()], 500);
        }
    }

    public function getDaySlots($day_name)
    {
        // Retrieve day slots based on the day_name and include the day status
        $days = ScheduleDay::where('day_name', $day_name)
            ->with('scheduleDayTimes.scheduleDayTimeSlots')
            ->get();

        // Check if there are any days fetched
        if ($days->isEmpty()) {
            return response()->json(['message' => 'No days found for the given day name.'], 404);
        }

        // Check if any day has status of 0 and return a custom message
        if ($days->contains('status', 0)) {
            return response()->json(['message' => "This day doesn't have a schedule"], 404);
        }

        // Extract day times and time slots only if status is not 0
        $daySlots = $days->pluck('scheduleDayTimes') // Pluck all day times
            ->collapse() // Flatten the collection of day times
            ->pluck('scheduleDayTimeSlots') // Pluck all time slots
            ->collapse(); // Flatten the collection of time slots

        // Check if any day slots are found after status check
        if ($daySlots->isEmpty()) {
            return response()->json(['message' => 'No slots found for the active day.'], 404);
        }

        return response()->json($daySlots);
    }
}
