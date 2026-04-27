@extends('layouts.app')
@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div style="max-width:700px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h1 style="font-family:'Cinzel Decorative',serif;font-size:1rem;color:var(--gold-l);">
            🎫 Ticket #{{ $ticket->id }}
        </h1>
        <span class="badge badge-{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
    </div>

    <div class="card">
        <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
            <div>
                <div style="font-size:.8rem;color:var(--text-dim);">Catégorie</div>
                <div>{{ ucfirst($ticket->category) }}</div>
            </div>
            <div>
                <div style="font-size:.8rem;color:var(--text-dim);">Soumis par</div>
                <div style="color:var(--gold);">{{ $ticket->user->name }}</div>
            </div>
            <div>
                <div style="font-size:.8rem;color:var(--text-dim);">Date</div>
                <div>{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="card-title">{{ $ticket->title }}</div>
        <p style="white-space:pre-wrap;line-height:1.6;color:var(--text);">{{ $ticket->description }}</p>
    </div>

    @if($ticket->admin_reply)
    <div class="card" style="border-color:var(--gold);background:#1a1508;">
        <div class="card-title">👑 Réponse de l'équipe</div>
        <p style="white-space:pre-wrap;line-height:1.6;">{{ $ticket->admin_reply }}</p>
    </div>
    @endif

    {{-- Formulaire admin pour répondre --}}
    @if(Auth::user()->isAdmin())
    <div class="card">
        <div class="card-title">👑 Répondre / Changer le statut</div>
        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="status">Statut</label>
                <select name="status" id="status">
                    <option value="open"        @selected($ticket->status=='open')>🟢 Ouvert</option>
                    <option value="in_progress" @selected($ticket->status=='in_progress')>🟡 En cours</option>
                    <option value="closed"      @selected($ticket->status=='closed')>🔴 Fermé</option>
                </select>
            </div>
            <div class="form-group">
                <label for="admin_reply">Réponse</label>
                <textarea name="admin_reply" id="admin_reply">{{ old('admin_reply', $ticket->admin_reply) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
    @endif

    <div style="display:flex;gap:10px;margin-top:8px;">
        <a href="{{ route('tickets.index') }}" class="btn" style="background:var(--bg3);border-color:var(--border);color:var(--text-dim);">← Retour</a>
        @if($ticket->isOpen() && !Auth::user()->isAdmin())
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary btn-sm">Modifier</a>
            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Supprimer ce ticket ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
            </form>
        @endif
    </div>
</div>
@endsection
