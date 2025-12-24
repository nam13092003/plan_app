<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketComment>
 */
class TicketCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TicketComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'content' => $this->faker->sentence(),
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
        ];
    }
}
