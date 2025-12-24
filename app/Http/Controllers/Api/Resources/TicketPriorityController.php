<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\TicketPriority;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketPriorityController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketPriority::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        if ($request->boolean('paginate', false)) {
            return response()->json($query->paginate($request->get('per_page', 15)));
        }

        return response()->json($query->get());
    }

    public function show(TicketPriority $ticketPriority)
    {
        return response()->json($ticketPriority);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $ticketPriority = TicketPriority::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'is_default' => $data['is_default'] ?? false,
        ]);

        $this->syncDefaultPriority($ticketPriority);

        return response()->json($ticketPriority, Response::HTTP_CREATED);
    }

    public function update(Request $request, TicketPriority $ticketPriority)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $ticketPriority->update($data);

        $this->syncDefaultPriority($ticketPriority);

        return response()->json($ticketPriority);
    }

    public function destroy(TicketPriority $ticketPriority)
    {
        $ticketPriority->delete();

        return response()->json([
            'message' => 'Ticket priority deleted successfully',
        ]);
    }

    private function syncDefaultPriority(TicketPriority $ticketPriority): void
    {
        if ($ticketPriority->is_default) {
            TicketPriority::where('id', '<>', $ticketPriority->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}

