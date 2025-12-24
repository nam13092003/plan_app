<?php

namespace App\Http\Controllers\Api\Pages;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TicketPriority;
use Illuminate\Http\Request;

class RoadMapController extends Controller
{
    public function getRoadmap(Project $project)
    {
        if ($project->owner_id != auth()->id() && !$project->users->where('id', auth()->id())->count()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tickets = $project->tickets()
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->with(['status', 'priority', 'responsible'])
            ->orderBy('start_date', 'asc')
            ->get();

        $tasks = [];
        foreach ($tickets as $ticket) {
            $assigneeName = $ticket->responsible ? $ticket->responsible->name : 'Unassigned';
            
            $tasks[] = [
                'id' => (string)$ticket->id,
                // HIỂN THỊ: [Tên Task] - (Người phụ trách) để hiện thẳng lên biểu đồ
                'name' => htmlspecialchars($ticket->name) . " - (" . htmlspecialchars($assigneeName) . ")",
                'start' => $ticket->start_date->format('Y-m-d'),
                'end' => $ticket->due_date->format('Y-m-d'),
                'progress' => $ticket->status?->is_final ? 100 : ($ticket->progress ?? 30),
                'status_color' => $ticket->status->color ?? '#64748b',
                'priority_color' => $ticket->priority->color ?? '#cbd5e1',
                'custom_popup_html' => $this->buildModernPopup($ticket, $assigneeName)
            ];
        }

        $usedStatuses = $tickets->pluck('status')
        ->filter() // Loại bỏ các ticket không có status
        ->unique('id')
        ->map(function ($status) {
            return [
                'name' => $status->name,
                'color' => $status->color,
            ];
        })->values();

        return response()->json([
            'tasks' => $tasks,
            'legend' => [
                'statuses' => $usedStatuses, // Sử dụng danh sách đã trích xuất
                'priorities' => \App\Models\TicketPriority::get(['name', 'color'])
            ]
        ]);
    }

    private function buildModernPopup($ticket, $assignee): string
    {
        return "
        <div class='p-4 min-w-[260px] bg-white rounded-xl shadow-2xl'>
            <div class='flex items-center gap-3 mb-3 border-b pb-3 border-slate-100'>
                <div class='w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-indigo-200 shadow-lg'>
                    " . strtoupper(substr($assignee, 0, 1)) . "
                </div>
                <div>
                    <div class='text-[10px] text-slate-400 font-bold uppercase tracking-widest'>Assignee</div>
                    <div class='text-sm font-extrabold text-slate-800'>$assignee</div>
                </div>
            </div>
            <div class='space-y-3'>
                <div class='flex justify-between items-center'>
                    <span class='text-xs text-slate-500'>Task Status</span>
                    <span class='px-2 py-1 rounded-md bg-slate-100 text-[10px] font-black text-slate-700 uppercase'>" . ($ticket->status->name ?? 'N/A') . "</span>
                </div>
                <div class='flex items-center gap-2 text-xs text-slate-400'>
                    <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/></svg>
                    <span>" . $ticket->start_date->format('d M') . " - " . $ticket->due_date->format('d M, Y') . "</span>
                </div>
            </div>
        </div>";
    }
}