<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultUserSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(ProjectStatusSeeder::class);
        $this->call(TicketTypeSeeder::class);
        $this->call(TicketPrioritySeeder::class);
        $this->call(TicketStatusSeeder::class);
        $this->call(ActivitySeeder::class);

        if (app()->environment() !== 'production') {
            $this->call(DemoDataSeeder::class);
            $this->call(GanttChartSeeder::class);
        }
    }
}
