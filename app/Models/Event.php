<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    use HasFactory, Notifiable;

    protected $dispatchesEvents = [
        'saved' => \App\Events\EventCreated::class,
    ];

    function timings() {
        return $this->hasMany(EventTiming::class);
    }
}
