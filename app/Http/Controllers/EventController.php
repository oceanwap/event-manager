<?php

namespace App\Http\Controllers;

use App\Lib\EventService;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventTiming;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventService = EventService::getInstance();
        return $eventService->getAllEvents();
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBookings(Request $request, $eventId)
    {
        return Booking::where('event_id', $eventId)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postBooking(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'event_id' => 'required|exists:events,id',
            'email' => 'required|email:filter',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'booking_time' => ['required', 'date']
        ], [
            'event_id.exists' => 'Invalid event_id passed'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 2, 'message' => $validator->errors() ], 400);
        }

        try {
            EventService::getInstance()->validateBookingRequestWithCache($request);
        }  catch (Exception $e) {
            return response()->json(['status' => $e->getCode(), 'message' => $e->getMessage() ], 400);
        }

        try {
            $booking = new Booking;
            $booking->event_id = $request->event_id; 
            $booking->email = $request->email;
            $booking->first_name = $request->first_name;
            $booking->last_name = $request->last_name;
            $booking->booking_time= $request->booking_time;
            $booking->save();
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'message' => "Booking couldn't be created", "exception" => $e->getMessage()], 400);
        }

        return response()->json(['status' => 0, 'message' => "Booking created"], 200);
    }
}
