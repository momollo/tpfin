<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /** Liste des tickets de l'utilisateur (admin voit tout) */
    public function index()
    {
        $tickets = Auth::user()->isAdmin()
            ? Ticket::with('user')->latest()->paginate(15)
            : Auth::user()->tickets()->latest()->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    /** Formulaire de création */
    public function create()
    {
        return view('tickets.create');
    }

    /** Enregistrer un nouveau ticket */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'required|string|min:10|max:2000',
            'category'    => 'required|in:bug,suggestion,refund,other',
        ]);

        Auth::user()->tickets()->create($validated);

        return redirect()->route('tickets.index')
            ->with('success', '✅ Votre ticket a bien été soumis !');
    }

    /** Détail d'un ticket */
    public function show(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        return view('tickets.show', compact('ticket'));
    }

    /** Formulaire d'édition (propriétaire + admin) */
    public function edit(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        return view('tickets.edit', compact('ticket'));
    }

    /** Mise à jour */
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($ticket);

        if (Auth::user()->isAdmin()) {
            $validated = $request->validate([
                'status'      => 'required|in:open,in_progress,closed',
                'admin_reply' => 'nullable|string|max:2000',
            ]);
        } else {
            abort_if($ticket->status === 'closed', 403, 'Ce ticket est fermé.');
            $validated = $request->validate([
                'title'       => 'required|string|max:120',
                'description' => 'required|string|min:10|max:2000',
                'category'    => 'required|in:bug,suggestion,refund,other',
            ]);
        }

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', '✅ Ticket mis à jour.');
    }

    /** Supprimer un ticket */
    public function destroy(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', '🗑️ Ticket supprimé.');
    }

    /** Helper : l'utilisateur peut-il accéder à ce ticket ? */
    private function authorizeTicket(Ticket $ticket): void
    {
        if (!Auth::user()->isAdmin() && $ticket->user_id !== Auth::id()) {
            abort(403, 'Accès interdit.');
        }
    }
}
