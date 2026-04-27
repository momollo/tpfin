<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    /**
     * GET /api/tickets/open
     * Retourne tous les tickets ouverts
     */
    public function openTickets()
    {
        $tickets = Ticket::with('user:id,name,email')
            ->open()
            ->latest()
            ->get();

        return response()->json([
            'count'   => $tickets->count(),
            'tickets' => $tickets,
        ]);
    }

    /**
     * GET /api/tickets/closed
     * Retourne tous les tickets fermés
     */
    public function closedTickets()
    {
        $tickets = Ticket::with('user:id,name,email')
            ->closed()
            ->latest()
            ->get();

        return response()->json([
            'count'   => $tickets->count(),
            'tickets' => $tickets,
        ]);
    }

    /**
     * GET /api/users/{email}/tickets
     * Retourne les tickets d'un utilisateur par email
     */
    public function userTickets(string $email)
    {
        $user = User::where('email', $email)->firstOrFail();

        return response()->json([
            'user'    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'count'   => $user->tickets()->count(),
            'tickets' => $user->tickets()->latest()->get(),
        ]);
    }

    /**
     * GET /api/tickets/stats
     * Statistiques globales des tickets
     */
    public function stats()
    {
        return response()->json([
            'total'       => Ticket::count(),
            'open'        => Ticket::open()->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'closed'      => Ticket::closed()->count(),
            'by_category' => Ticket::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category'),
            'avg_resolution_hours' => round(
                Ticket::closed()
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
                    ->value('avg_hours') ?? 0, 1
            ),
        ]);
    }
}
