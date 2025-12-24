<?php

namespace App\Http\Controllers\Api\Pages;

use App\Http\Controllers\Controller;
use App\Models\TicketHour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimesheetDashboardController extends Controller
{
    /**
     * Get monthly report data
     */
    public function monthlyReport(Request $request)
    {
        $year = $request->get('year', Carbon::now()->format('Y'));

        $collection = TicketHour::select([
            DB::raw("DATE_FORMAT(created_at,'%m') as month"),
            DB::raw('SUM(value) as value'),
        ])
            ->whereRaw(DB::raw("YEAR(created_at)=" . $year))
            ->where('user_id', auth()->id())
            ->groupBy(DB::raw("DATE_FORMAT(created_at,'%m')"))
            ->get();

        $months = [
            1 => ['January', 0],
            2 => ['February', 0],
            3 => ['March', 0],
            4 => ['April', 0],
            5 => ['May', 0],
            6 => ['June', 0],
            7 => ['July', 0],
            8 => ['August', 0],
            9 => ['September', 0],
            10 => ['October', 0],
            11 => ['November', 0],
            12 => ['December', 0]
        ];

        foreach ($collection as $value) {
            if (isset($months[(int)$value->month])) {
                $months[(int)$value->month][1] = (float)$value->value;
            }
        }

        $datasets = [
            'sets' => [],
            'labels' => []
        ];

        foreach ($months as $data) {
            $datasets['sets'][] = $data[1];
            $datasets['labels'][] = $data[0];
        }

        return response()->json([
            'datasets' => [
                [
                    'label' => __('Total time logged'),
                    'data' => $datasets['sets'],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $datasets['labels'],
        ]);
    }

    /**
     * Get weekly report data
     */
    public function weeklyReport(Request $request)
    {
        $weekStartDate = $request->get('week_start_date');
        $weekEndDate = $request->get('week_end_date');

        // If not provided, use current week
        if (!$weekStartDate || !$weekEndDate) {
            $now = Carbon::now();
            $weekStartDate = $now->startOfWeek()->format('Y-m-d');
            $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        }

        $collection = TicketHour::select([
            DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d') as day"),
            DB::raw('SUM(value) as value'),
        ])
            ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->where('user_id', auth()->id())
            ->groupBy(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"))
            ->get();

        $dates = $this->buildDatesRange($weekStartDate, $weekEndDate);
        $template = $this->createReportTemplate($dates);

        foreach ($collection as $item) {
            if (isset($template[$item->day])) {
                $template[$item->day]['value'] = $item->value;
            }
        }

        $data = collect($template)->pluck('value')->toArray();

        return response()->json([
            'datasets' => [
                [
                    'label' => __('Weekly time logged'),
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $dates,
        ]);
    }

    /**
     * Get activities report data
     */
    public function activitiesReport(Request $request)
    {
        $year = $request->get('year', Carbon::now()->format('Y'));

        $collection = TicketHour::with('activity')
            ->select([
                'activity_id',
                DB::raw('SUM(value) as value'),
            ])
            ->whereRaw(DB::raw("YEAR(created_at)=" . $year))
            ->where('user_id', auth()->id())
            ->groupBy('activity_id')
            ->get();

        $datasets = [
            'sets' => [],
            'labels' => []
        ];

        foreach ($collection as $item) {
            $datasets['sets'][] = $item->value;
            $datasets['labels'][] = $item->activity?->name ?? __('No activity');
        }

        return response()->json([
            'datasets' => [
                [
                    'label' => __('Total time logged'),
                    'data' => $datasets['sets'],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $datasets['labels'],
        ]);
    }

    /**
     * Build dates range for weekly report
     */
    private function buildDatesRange($weekStartDate, $weekEndDate): array
    {
        $period = CarbonPeriod::create($weekStartDate, $weekEndDate);
        $dates = [];
        foreach ($period as $item) {
            $dates[] = $item->format('Y-m-d');
        }
        return $dates;
    }

    /**
     * Create report template with zero values
     */
    private function createReportTemplate(array $dates): array
    {
        $template = [];
        foreach ($dates as $date) {
            $template[$date]['value'] = 0;
        }
        return $template;
    }
}



