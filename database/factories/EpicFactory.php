<?php

namespace Database\Factories;

use App\Models\Epic;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpicFactory extends Factory
{
    protected $model = Epic::class;

    public function definition()
    {
        // Tạo ngày bắt đầu ngẫu nhiên trong khoảng 1 tháng qua đến 1 tháng tới
        $startsAt = $this->faker->dateTimeBetween('-1 month', '+1 month');
        // Ngày kết thúc ngẫu nhiên sau ngày bắt đầu từ 2 đến 6 tuần
        $endsAt = (clone $startsAt)->modify('+' . rand(14, 42) . ' days');

        return [
            'name' => $this->faker->sentence(rand(3, 5)),
            'description' => $this->faker->paragraph(),
            'starts_at' => $startsAt->format('Y-m-d'),
            'ends_at' => $endsAt->format('Y-m-d'),
            'project_id' => Project::factory(), // Tự động tạo Project nếu không chỉ định
        ];
    }
}