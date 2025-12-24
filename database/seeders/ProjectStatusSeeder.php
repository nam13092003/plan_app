<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Active',
            'color' => '#008000',
            'is_default' => true,
        ],
        [
            'name' => 'On Hold',
            'color' => '#ff7f00',
            'is_default' => false,
        ],
        [
            'name' => 'Completed',
            'color' => '#00FFFF',
            'is_default' => false,
        ],
        [
            'name' => 'Archived',
            'color' => '#cecece',
            'is_default' => false,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $item) {
            ProjectStatus::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
