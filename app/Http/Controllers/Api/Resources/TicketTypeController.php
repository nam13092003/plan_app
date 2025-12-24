<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketType::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        if ($request->boolean('paginate', false)) {
            return response()->json($query->paginate($request->get('per_page', 15)));
        }

        return response()->json($query->get());
    }

    public function show(TicketType $ticketType)
    {
        return response()->json($ticketType);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $ticketType = TicketType::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'icon' => $data['icon'] ?? null,
            'is_default' => $data['is_default'] ?? false,
        ]);

        $this->syncDefaultType($ticketType);

        return response()->json($ticketType, Response::HTTP_CREATED);
    }

    public function update(Request $request, TicketType $ticketType)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $ticketType->update($data);

        $this->syncDefaultType($ticketType);

        return response()->json($ticketType);
    }

    public function destroy(TicketType $ticketType)
    {
        $ticketType->delete();

        return response()->json([
            'message' => 'Ticket type deleted successfully',
        ]);
    }

    private function syncDefaultType(TicketType $ticketType): void
    {
        if ($ticketType->is_default) {
            TicketType::where('id', '<>', $ticketType->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}

