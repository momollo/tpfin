@extends('layouts.app')
@section('title', 'Historique des achats')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h1 style="font-family:'Cinzel Decorative',serif;font-size:1.1rem;color:var(--gold-l);">
        💰 {{ Auth::user()->isAdmin() ? 'Tous les achats' : 'Mes achats' }}
    </h1>
    <div style="font-size:.9rem;color:var(--text-dim);">
        Total dépensé : <span style="color:var(--gold);font-weight:600;">{{ number_format($totalSpent) }} 🪙</span>
    </div>
</div>

<div class="card">
    @if($payments->isEmpty())
        <p style="color:var(--text-dim);font-style:italic;text-align:center;padding:24px 0;">
            Aucun achat effectué. Achetez des améliorations dans le jeu !
        </p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @if(Auth::user()->isAdmin()) <th>Joueur</th> @endif
                    <th>Amélioration</th>
                    <th>Coût (🪙)</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td style="color:var(--text-dim)">{{ $p->id }}</td>
                    @if(Auth::user()->isAdmin())
                        <td>{{ $p->user->name }}</td>
                    @endif
                    <td>{{ $p->upgrade_name }}</td>
                    <td style="color:var(--amber)">{{ number_format($p->amount) }}</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td style="color:var(--text-dim);font-size:.8rem;">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($p->status === 'completed' && $p->user_id === Auth::id())
                            <form method="POST" action="{{ route('payments.refund', $p) }}" onsubmit="return confirm('Demander un remboursement ?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Rembourser</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:14px;">{{ $payments->links() }}</div>
    @endif
</div>

<div style="margin-top:8px;">
    <a href="{{ route('game') }}" class="btn btn-primary btn-sm">← Retour au jeu</a>
</div>
@endsection
