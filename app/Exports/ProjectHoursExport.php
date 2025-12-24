<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectHoursExport implements FromCollection, WithHeadings, WithMapping
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->project->tickets()
            ->with(['hours.user', 'hours.activity'])
            ->get()
            ->flatMap(function ($ticket) {
                return $ticket->hours->map(function ($hour) use ($ticket) {
                    return [
                        'ticket' => $ticket,
                        'hour' => $hour,
                    ];
                });
            });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Ticket Code',
            'Ticket Name',
            'User',
            'Hours',
            'Activity',
            'Comment',
            'Date',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $ticket = $row['ticket'];
        $hour = $row['hour'];

        return [
            $ticket->code ?? '',
            $ticket->name ?? '',
            $hour->user->name ?? '',
            $hour->value ?? 0,
            $hour->activity->name ?? '',
            $hour->comment ?? '',
            $hour->created_at ? $hour->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}

