@extends('layouts.app')
@section('title', 'Modifier Ticket #' . $ticket->id)

@section('content')
<div style="max-width:640px;margin:0 auto;">
    <h1 style="font-family:'Cinzel Decorative',serif;font-size:1rem;color:var(--gold-l);margin-bottom:16px;">
        ✏️ Modifier le ticket #{{ $ticket->id }}
    </h1>

    <div class="card">
        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf @method('PUT')

            @if(Auth::user()->isAdmin())
                <div class="form-group">
                    <label>Statut</label>
                    <select name="status">
                        <option value="open" @selected($ticket->status=='open')>🟢 Ouvert</option>
                        <option value="in_progress" @selected($ticket->status=='in_progress')>🟡 En cours</option>
                        <option value="closed" @selected($ticket->status=='closed')>🔴 Fermé</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Réponse admin</label>
                    <textarea name="admin_reply">{{ old('admin_reply', $ticket->admin_reply) }}</textarea>
                </div>
            @else
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="category">
                        <option value="bug" @selected($ticket->category=='bug')>🐛 Bug</option>
                        <option value="suggestion" @selected($ticket->category=='suggestion')>💡 Suggestion</option>
                        <option value="refund" @selected($ticket->category=='refund')>💰 Remboursement</option>
                        <option value="other" @selected($ticket->category=='other')>📋 Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Titre</label>
                    <input type="text" name="title" value="{{ old('title', $ticket->title) }}">
                    @error('title') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description">{{ old('description', $ticket->description) }}</textarea>
                    @error('description') <div class="error">{{ $message }}</div> @enderror
                </div>
            @endif

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('tickets.show', $ticket) }}" class="btn" style="background:var(--bg3);border-color:var(--border);color:var(--text-dim);">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
