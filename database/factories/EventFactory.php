<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(),
            'duration_minutes' => $this->faker->unique()->safeEmail(),
            'preparation_minutes' => 2,
            'bookable_in_advance_days' => rand(2, 5),
            'bookable_from' => Carbon::now()->format('Y-m-d H:m:s'),
            'bookable_until' => Carbon::now()->addDay(rand(2, 5))->format('Y-m-d H:m:s'),
        ];
    }
}
