<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function index(Request $request, $ticketId)
    {
        $query = TicketComment::with(['user'])
            ->where('ticket_id', $ticketId);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $comments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($comments);
    }

    public function show($ticketId, TicketComment $comment)
    {
        // Verify comment belongs to ticket
        if ($comment->ticket_id != $ticketId) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->load(['user', 'ticket']);
        return response()->json($comment);
    }

    public function store(Request $request, $ticketId)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $validated['ticket_id'] = $ticketId;
        $validated['user_id'] = auth()->id();

        $comment = TicketComment::create($validated);
        $comment->load(['user', 'ticket']);

        return response()->json($comment, 201);
    }

    public function update(Request $request, $ticketId, TicketComment $comment)
    {
        // Verify comment belongs to ticket
        if ($comment->ticket_id != $ticketId) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Only allow author to update
        if ($comment->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update($validated);
        $comment->load(['user', 'ticket']);

        return response()->json($comment);
    }

    public function destroy($ticketId, TicketComment $comment)
    {
        // Verify comment belongs to ticket
        if ($comment->ticket_id != $ticketId) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Only allow author or admin to delete
        if ($comment->user_id != auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}


