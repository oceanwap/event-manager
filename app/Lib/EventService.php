<?php

namespace App\Lib;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventTiming;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventService {
    protected static $instance = null;

    protected $event_cache_key = 'events-cache';

    public static function getInstance () {
        if (self::$instance == null) {
            self::$instance = new EventService;
        }

        return self::$instance;
    }

    function getAllEvents () {
        if (Cache::has($this->event_cache_key)) {
            return Cache::get($this->event_cache_key);
        }

        $soldSlots = Event::leftJoin('bookings', 'events.id', '=', 'bookings.event_id')
            ->groupBy('bookings.booking_time', 'events.id')
            ->orderBy('bookings.booking_time', 'asc')
            ->orderBy('events.id')
            ->select(DB::raw('events.id, DATE_FORMAT(bookings.booking_time, "%Y-%m-%d %h:%i") as booking_time, (count(bookings.id) >= events.participants_per_slot) as isSlotFull'))->get();

        $events = Event::with([ 'timings:event_id,availability,days,from,until' ])
        ->where('bookable_from', '<=', Carbon::now()->format('Y-m-d H:i:s'))
        ->where('bookable_until', '>=', Carbon::now()->format('Y-m-d H:i:s'))
        ->get([
            'id',
            'title',
            'duration_minutes',
            'preparation_minutes',
            'bookable_in_advance_days',
            DB::raw('DATE_FORMAT(`bookable_from`, "%Y-%m-%d") as bookable_from'),
            DB::raw('DATE_FORMAT(`bookable_until`, "%Y-%m-%d") as bookable_until'),
        ])->map(function($item) use($soldSlots) {
            $item['soldSlots'] = $soldSlots->where('id', $item->id)->pluck('booking_time')->filter();

            foreach ($item['timings'] as $timings) 
                unset ($timings['event_id']);
            return $item;
        });

        Cache::put($this->event_cache_key, $events);

        return $events;
    }

    function validateBookingRequestWithCache ($request) {
        if (!Cache::has($this->event_cache_key)) {
            return $this->reCacheAllEvents();
        }

        $events = collect(Cache::get($this->event_cache_key));
        $event = $events->where('id', data_get($request, 'event_id'))->first();
        $eventTimings = data_get($event, 'timings');
        $soldSlots = data_get($event, 'soldSlots');
        
        $bookingdatetime = new Carbon(data_get($request, 'booking_time'));

        $isSlotFull = in_array($bookingdatetime->format("Y-m-d H:i"), $soldSlots->toArray());
        $beyondAdvanceBookingPeriod = Carbon::now()->addDay($event->bookable_in_advance_days)->lessThan($bookingdatetime);

        $NotInWindow = 0;
        
        $bookingDate = $bookingdatetime->format('Y-m-d');
        $bookingDay = strtolower($bookingdatetime->format('D'));

        foreach ($eventTimings as $window) {
            $windowFrom = new Carbon("$bookingDate {$window->from}");
            $windowUntil = new Carbon("$bookingDate {$window->until}");
            $withinTimeWindow = $bookingdatetime->greaterThanOrEqualTo($windowFrom) && $bookingdatetime->lessThan($windowUntil);
            $inWindowDays = str_contains($window->days, $bookingDay);

            $isAllowed = (boolean) $window->availability == ($withinTimeWindow && $inWindowDays);

            if (!$isAllowed) {
                $NotInWindow = 1;
                break;
            }
        }

        if (    $isSlotFull
                || $beyondAdvanceBookingPeriod
                || $NotInWindow) {
            throw new Exception ("Booking not allowed for this time slot", 2);
        }
    }

    function validateBookingRequest ($request) {
        $event = Event::find(data_get($request, 'event_id'));
        $eventTimings = EventTiming::where('event_id', data_get($request, 'event_id'))->get();

        $booking = Booking::leftJoin('events', 'events.id', '=', 'bookings.event_id')
            ->where('bookings.booking_time', '=', data_get($request, 'booking_time'))
            ->where('events.id', '=', data_get($request, 'event_id'))
            ->groupBy('bookings.booking_time', 'events.participants_per_slot')
            ->select(DB::raw('(count(bookings.id) >= events.participants_per_slot) as isSlotFull'))->first();
        
        $bookingdatetime = new Carbon(data_get($request, 'booking_time'));

        $isSlotFull = $booking ? $booking->isSlotFull : 0;
        $beyondAdvanceBookingPeriod = Carbon::now()->addDay($event->bookable_in_advance_days)->lessThan($bookingdatetime);

        $NotInWindow = 0;
        
        $bookingDate = $bookingdatetime->format('Y-m-d');
        $bookingDay = strtolower($bookingdatetime->format('D'));

        foreach ($eventTimings as $window) {
            $windowFrom = new Carbon("$bookingDate {$window->from}");
            $windowUntil = new Carbon("$bookingDate {$window->until}");
            $withinTimeWindow = $bookingdatetime->greaterThanOrEqualTo($windowFrom) && $bookingdatetime->lessThan($windowUntil);
            $inWindowDays = str_contains($window->days, $bookingDay);

            $isAllowed = (boolean) $window->availability == ($withinTimeWindow && $inWindowDays);

            if (!$isAllowed) {
                $NotInWindow = 1;
                break;
            }
        }

        if (    $isSlotFull
                || $beyondAdvanceBookingPeriod
                || $NotInWindow) {
                throw new Exception ("Booking not allowed for this time slot", 2);
        }
    }

    function reCacheAllEvents() {
        Cache::forget($this->event_cache_key);
        $this->getAllEvents();
    }
}