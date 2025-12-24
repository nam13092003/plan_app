<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketHour;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create a main user to act as owner/manager
        $mainUser = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@planora.app',
            'password' => Hash::make('password'),
        ]);
        $mainUser->assignRole('Default role');

        // 2. Create a pool of other users
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->assignRole('Default role');
        }

        $allUsers = $users->merge([$mainUser]);

        // 3. Create a few projects
        $project1 = Project::factory()->create([
            'name' => 'Planora App Development',
            'owner_id' => $mainUser->id,
            'type' => 'scrum'
        ]);

        $project2 = Project::factory()->create([
            'name' => 'Marketing Website Relaunch',
            'owner_id' => $mainUser->id,
            'type' => 'kanban'
        ]);

        $project3 = Project::factory()->create([
            'name' => 'Internal IT Support',
            'owner_id' => $mainUser->id,
            'type' => 'kanban'
        ]);

        $projects = [$project1, $project2, $project3];

        // 4. Assign users to projects and create tickets
        foreach ($projects as $project) {
            // Assign main user as manager
            $project->users()->attach($mainUser->id, ['role' => 'administrator']);

            // Assign 3-5 random users as members
            $projectMembers = $users->random(rand(3, 5));
            foreach ($projectMembers as $member) {
                $project->users()->attach($member->id, ['role' => 'employee']);
            }
            $projectTeam = $projectMembers->merge([$mainUser]);

            // Create 20-30 tickets for each project
            Ticket::factory(rand(20, 30))->create([
                'project_id' => $project->id,
            ])->each(function ($ticket) use ($projectTeam) {
                // Assign owner and responsible
                $ticket->owner_id = $projectTeam->random()->id;
                $ticket->responsible_id = $projectTeam->random()->id;
                $ticket->save();

                // Create comments for some tickets
                if (rand(0, 1)) {
                    TicketComment::factory(rand(1, 5))->create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $projectTeam->random()->id
                    ]);
                }

                // Create time logs for some tickets
                if (rand(0, 1)) {
                    TicketHour::factory(rand(2, 8))->create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $projectTeam->random()->id
                    ]);
                }
            });
        }
    }
}
