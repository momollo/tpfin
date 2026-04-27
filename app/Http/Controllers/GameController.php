<?php

namespace App\Http\Controllers;

use App\Models\GameSave;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /** Catalogue des améliorations disponibles */
    const UPGRADES = [
        'apprenti'  => ['name' => '🧪 Apprenti Alchimiste', 'baseCost' => 15,      'baseCps' => 0.1,  'clickBonus' => 0],
        'alambic'   => ['name' => '⚗️ Alambic de Cuivre',   'baseCost' => 100,     'baseCps' => 0.5,  'clickBonus' => 1],
        'grimoire'  => ['name' => '📜 Grimoire Ancien',      'baseCost' => 500,     'baseCps' => 2,    'clickBonus' => 3],
        'four'      => ['name' => '🔥 Four à Balefire',      'baseCost' => 2000,    'baseCps' => 8,    'clickBonus' => 5],
        'golem'     => ['name' => '🗿 Golem de Pierre',      'baseCost' => 8000,    'baseCps' => 25,   'clickBonus' => 10],
        'portail'   => ['name' => '🌀 Portail Dimensionnel', 'baseCost' => 30000,   'baseCps' => 80,   'clickBonus' => 20],
        'dragon'    => ['name' => '🐉 Dragon Gardien',       'baseCost' => 100000,  'baseCps' => 250,  'clickBonus' => 50],
    ];

    /** Afficher le jeu */
    public function index()
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $save = $user->gameSave ?? null;

    $recentPayments = $user->payments()->latest()->take(5)->get();

    // Prépare le tableau avec l'id inclus pour le JS
    $upgradesForJs = array_map(
        fn($k, $v) => array_merge(['id' => $k], $v),
        array_keys(self::UPGRADES),
        array_values(self::UPGRADES)
    );

    return view('game.index', [
        'save'           => $save,
        'upgrades'       => self::UPGRADES,
        'upgradesForJs'  => $upgradesForJs,
        'recentPayments' => $recentPayments,
    ]);
}

    /** Sauvegarde automatique de la partie */
    public function save(Request $request)
    {
        $data = $request->validate([
            'coins'           => 'required|numeric|min:0',
            'total_coins'     => 'required|numeric|min:0',
            'total_clicks'    => 'required|numeric|min:0',
            'best_cps'        => 'required|numeric|min:0',
            'owned_upgrades'  => 'required|array',
        ]);

        GameSave::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return response()->json(['success' => true]);
    }

    /** Acheter une amélioration → crée un Payment */
    public function buyUpgrade(Request $request)
    {
        $validated = $request->validate([
            'upgrade_id'   => 'required|string|in:' . implode(',', array_keys(self::UPGRADES)),
            'amount'       => 'required|integer|min:1',
        ]);

        $upgradeId = $validated['upgrade_id'];
        $upgrade   = self::UPGRADES[$upgradeId];

        // Créer le payment (trace de l'achat)
        $payment = Payment::create([
            'user_id'      => Auth::id(),
            'upgrade_id'   => $upgradeId,
            'upgrade_name' => $upgrade['name'],
            'amount'       => $validated['amount'],
            'status'       => 'completed',
        ]);

        return response()->json([
            'success'    => true,
            'payment_id' => $payment->id,
            'message'    => "Achat enregistré : {$upgrade['name']}",
        ]);
    }

    /** Statistiques globales pour le leaderboard */
    public function leaderboard()
    {
        $leaders = \App\Models\GameSave::with('user:id,name')
            ->orderBy('total_coins', 'desc')
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'name'         => $s->user->name,
                'total_coins'  => $s->total_coins,
                'total_clicks' => $s->total_clicks,
                'best_cps'     => round($s->best_cps, 1),
            ]);

        return response()->json($leaders);
    }
}
