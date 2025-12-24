<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Ticket;
use App\Models\TicketHour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketHour>
 */
class TicketHourFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TicketHour::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'value' => $this->faker->randomFloat(2, 0.5, 8),
            'comment' => $this->faker->optional()->sentence(),
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'activity_id' => Activity::inRandomOrder()->first()->id ?? Activity::factory(),
        ];
    }
}
