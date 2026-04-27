<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /** Historique des achats de l'utilisateur */
    public function index()
    {
        $payments = Auth::user()->isAdmin()
            ? Payment::with('user')->latest()->paginate(20)
            : Auth::user()->payments()->latest()->paginate(20);

        $totalSpent = Auth::user()->isAdmin()
            ? Payment::completed()->sum('amount')
            : Auth::user()->payments()->completed()->sum('amount');

        return view('payments.index', compact('payments', 'totalSpent'));
    }

    /** Détail d'un paiement */
    public function show(Payment $payment)
    {
        if (!Auth::user()->isAdmin() && $payment->user_id !== Auth::id()) {
            abort(403);
        }
        return view('payments.show', compact('payment'));
    }

    /** Demander un remboursement (crée un ticket automatiquement) */
    public function refund(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) abort(403);
        if ($payment->status === 'refunded') {
            return back()->with('error', '⚠️ Déjà remboursé.');
        }

        $payment->update(['status' => 'refunded']);

        // Créer automatiquement un ticket de remboursement
        Auth::user()->tickets()->create([
            'title'       => "Remboursement : {$payment->upgrade_name}",
            'description' => "Remboursement demandé pour l'achat #{$payment->id} ({$payment->amount} pièces).",
            'category'    => 'refund',
            'status'      => 'open',
        ]);

        return redirect()->route('payments.index')
            ->with('success', '💰 Remboursement demandé — un ticket a été créé.');
    }
}
