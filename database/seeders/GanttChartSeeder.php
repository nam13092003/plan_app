<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class GanttChartSeeder extends Seeder
{
    public function run()
    {
        $mainUser = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@planora.app',
            'password' => Hash::make('password'),
        ]);
        $mainUser->assignRole('Default role');
        
        // 1. Create target project
        $project = Project::factory()->create([
            'name' => 'PLANORA Management System v2.0',
            'type' => 'scrum',
            'ticket_prefix' => 'PLN',
        ]);

        // 2. Create a diverse team (4 Developers)
        $devs = collect(['Le Nam', 'Tran Hung', 'Nguyen Lan', 'Pham Minh'])
            ->map(function ($name) use ($project) {
                $user = User::factory()->create(['name' => $name]);
                $project->users()->attach($user->id, ['role' => 'employee']);
                return $user;
            });

        // 3. Realistic task list (Task name, start offset (days), duration (days))
        $taskDefinitions = [
            ['Set up Cloud Server infrastructure', -5, 10],
            ['Analyze system requirements', -3, 7],
            ['Design detailed Database Schema', 0, 5],
            ['Build Authentication Module', 2, 8],
            ['Develop API for Roadmap', 5, 12],
            ['Integrate Frappe Gantt library', 8, 6],
            ['Design Dashboard UI', 10, 10],
            ['Implement multilingual logic (i18n)', 15, 7],
            ['Test CSRF/Auth security', 18, 5],
            ['Write API documentation', 20, 10],
            ['Optimize SQL query performance', 22, 6],
            ['Fix mobile UI bugs', 25, 4],
            ['Deploy internal Beta Test', 28, 14],
            ['Package source code (Release)', 40, 3],
        ];

        // Get Status and Priority IDs for random assignment
        $statusIds = TicketStatus::pluck('id')->toArray();
        $priorityIds = TicketPriority::pluck('id')->toArray();

        foreach ($taskDefinitions as $index => $data) {
            $startDate = Carbon::now()->addDays($data[1]);
            $dueDate = (clone $startDate)->addDays($data[2]);

            Ticket::factory()->create([
                'project_id' => $project->id,
                'name' => $data[0],
                'responsible_id' => $devs->random()->id, // Random assignment within team
                'status_id' => $this->getRandomStatus($index, $statusIds), // Time-based status logic
                'priority_id' => $priorityIds[array_rand($priorityIds)], // Random priority
                'start_date' => $startDate,
                'due_date' => $dueDate,
                'epic_id' => null, // Completely disable Epic as requested
            ]);
        }
    }

    // Helper function to make status progression more realistic
    // (Earlier tasks are more likely to be Done)
    private function getRandomStatus($index, $statusIds)
    {
        if ($index < 3) return 3; // Done (Assuming ID 3 is Done)
        if ($index < 7) return rand(2, 3); // In Progress or Done
        return $statusIds[array_rand($statusIds)]; // Random for remaining tasks
    }
}
