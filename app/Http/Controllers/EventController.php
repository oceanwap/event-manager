<?php

namespace App\Http\Controllers;

use App\Lib\EventService;
use App\Models\Booking;
use Illuminate\Http\Request;

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
}
