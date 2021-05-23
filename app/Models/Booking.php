<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Booking extends Model
{
    use HasFactory, Notifiable;

    protected $dispatchesEvents = [
        'saved' => \App\Events\BookingCreated::class,
    ];

    function event() {
        return $this->belongsTo(Event::class);
    }
}
