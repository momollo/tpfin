@extends('layouts.app')
@section('title', 'Mes Tickets')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h1 style="font-family:'Cinzel Decorative',serif;font-size:1.1rem;color:var(--gold-l);">
        🎫 {{ Auth::user()->isAdmin() ? 'Tous les tickets' : 'Mes tickets' }}
    </h1>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">+ Nouveau ticket</a>
</div>

<div class="card">
    @if($tickets->isEmpty())
        <p style="color:var(--text-dim);font-style:italic;text-align:center;padding:24px 0;">
            Aucun ticket pour l'instant. Tout va bien ! 🎉
        </p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @if(Auth::user()->isAdmin()) <th>Joueur</th> @endif
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td style="color:var(--text-dim)">{{ $ticket->id }}</td>
                    @if(Auth::user()->isAdmin())
                        <td>{{ $ticket->user->name }}</td>
                    @endif
                    <td><a href="{{ route('tickets.show', $ticket) }}" style="color:var(--gold-l);text-decoration:none;">{{ $ticket->title }}</a></td>
                    <td style="color:var(--text-dim);font-size:.82rem;">{{ ucfirst($ticket->category) }}</td>
                    <td><span class="badge badge-{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span></td>
                    <td style="color:var(--text-dim);font-size:.8rem;">{{ $ticket->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">Voir</a>
                        @if($ticket->isOpen() || Auth::user()->isAdmin())
                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm" style="background:var(--bg3);border-color:var(--border);color:var(--text-dim);">Éditer</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:14px;">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection
