<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TicketStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketStatus::query();

        if ($projectId = $request->get('project_id')) {
            $query->where(function ($q) use ($projectId) {
                $q->whereNull('project_id')->orWhere('project_id', $projectId);
            });
        }

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('order');

        $statuses = $query->get();

        Log::info('Fetched Ticket Statuses:', $statuses->toArray());

        if ($request->boolean('paginate', false)) {
            // Note: This part is not hit by the referential store, but kept for other potential uses.
            // We are returning the unpaginated result for the store.
            return response()->json($query->paginate($request->get('per_page', 15)));
        }

        return response()->json($statuses);
    }

    public function show(TicketStatus $ticketStatus)
    {
        return response()->json($ticketStatus);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'is_default' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:1',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $order = $data['order'] ?? $this->getNextOrder($data['project_id'] ?? null);

        $ticketStatus = TicketStatus::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'is_default' => $data['is_default'] ?? false,
            'order' => $order,
            'project_id' => $data['project_id'] ?? null,
        ]);

        return response()->json($ticketStatus->fresh(), Response::HTTP_CREATED);
    }

    public function update(Request $request, TicketStatus $ticketStatus)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:255',
            'is_default' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:1',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        if (!array_key_exists('order', $data)) {
            // Keep current order if not provided
            $data['order'] = $ticketStatus->order;
        } elseif (is_null($data['order'])) {
            $data['order'] = $this->getNextOrder($data['project_id'] ?? $ticketStatus->project_id);
        }

        $ticketStatus->update($data);

        return response()->json($ticketStatus->fresh());
    }

    public function destroy(TicketStatus $ticketStatus)
    {
        $ticketStatus->delete();

        return response()->json([
            'message' => 'Ticket status deleted successfully',
        ]);
    }

    private function getNextOrder(?int $projectId): int
    {
        $query = TicketStatus::query();
        if ($projectId) {
            $query->where('project_id', $projectId);
        } else {
            $query->whereNull('project_id');
        }

        return (int) $query->max('order') + 1;
    }
}

