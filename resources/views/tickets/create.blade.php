@extends('layouts.app')
@section('title', 'Nouveau Ticket')

@section('content')
<div style="max-width:640px;margin:0 auto;">
    <h1 style="font-family:'Cinzel Decorative',serif;font-size:1rem;color:var(--gold-l);margin-bottom:16px;">🎫 Ouvrir un ticket</h1>

    <div class="card">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf

            <div class="form-group">
                <label for="category">Catégorie</label>
                <select name="category" id="category">
                    <option value="bug" @selected(old('category')=='bug')>🐛 Bug / Problème technique</option>
                    <option value="suggestion" @selected(old('category')=='suggestion')>💡 Suggestion</option>
                    <option value="refund" @selected(old('category')=='refund')>💰 Remboursement</option>
                    <option value="other" @selected(old('category')=='other')>📋 Autre</option>
                </select>
                @error('category') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Résumé en quelques mots…">
                @error('title') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Décrivez votre problème ou demande en détail…">{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Soumettre le ticket</button>
                <a href="{{ route('tickets.index') }}" class="btn" style="background:var(--bg3);border-color:var(--border);color:var(--text-dim);">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
