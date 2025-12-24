<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company() . ' ' . $this->faker->bs(),
            'description' => $this->faker->paragraphs(3, true),
            'owner_id' => User::factory(),
            'status_id' => ProjectStatus::inRandomOrder()->first()->id ?? ProjectStatus::factory(),
            'ticket_prefix' => strtoupper($this->faker->unique()->lexify('???')),
            'type' => $this->faker->randomElement(['kanban', 'scrum']),
            'status_type' => 'default',
        ];
    }
}
