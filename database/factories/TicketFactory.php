<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
        {
            // Tạo ngày ngẫu nhiên trong khoảng tháng hiện tại
            $start = $this->faker->dateTimeBetween('-1month', '+1 month');
            $due = (clone $start)->modify('+' . rand(2, 10) . ' days');

            return [
                'name' => ucfirst($this->faker->words(4, true)),
                'content' => $this->faker->paragraphs(1, true),
                'project_id' => Project::factory(),
                'owner_id' => User::factory(),
                'responsible_id' => null, // Sẽ gán trong Seeder
                'status_id' => TicketStatus::inRandomOrder()->first()->id ?? 1,
                'priority_id' => TicketPriority::inRandomOrder()->first()->id ?? 2,
                'type_id' => TicketType::inRandomOrder()->first()->id ?? 1,
                'start_date' => $start->format('Y-m-d'), // QUAN TRỌNG
                'due_date' => $due->format('Y-m-d'),   // QUAN TRỌNG
            ];
        }
}
