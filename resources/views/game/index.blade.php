@extends('layouts.app')
@section('title', 'Jeu')

@push('styles')
<style>
.game-layout{display:grid;grid-template-columns:250px 1fr 260px;gap:16px;align-items:start;}
.orb-wrapper{position:relative;width:180px;height:180px;margin:0 auto;}
@keyframes orbit{from{transform:rotate(0deg) translateX(96px) rotate(0deg)}to{transform:rotate(360deg) translateX(96px) rotate(-360deg)}}
@keyframes orbit2{from{transform:rotate(120deg) translateX(96px) rotate(-120deg)}to{transform:rotate(480deg) translateX(96px) rotate(-480deg)}}
@keyframes orbit3{from{transform:rotate(240deg) translateX(96px) rotate(-240deg)}to{transform:rotate(600deg) translateX(96px) rotate(-600deg)}}
.ring-dot{position:absolute;top:50%;left:50%;width:8px;height:8px;margin:-4px;border-radius:50%;background:var(--gold);box-shadow:0 0 6px var(--gold);}
.ring-dot:nth-child(1){animation:orbit 4s linear infinite}
.ring-dot:nth-child(2){animation:orbit2 4s linear infinite}
.ring-dot:nth-child(3){animation:orbit3 4s linear infinite}
@keyframes pulse-orb{0%,100%{box-shadow:0 0 30px var(--glow),0 0 60px var(--glow-s)}50%{box-shadow:0 0 50px var(--glow),0 0 100px var(--glow-s)}}
.orb{position:absolute;top:0;left:0;width:180px;height:180px;border-radius:50%;
    background:radial-gradient(circle at 35% 35%,#3d2a0a,#1a1005);
    border:2px solid var(--gold);cursor:pointer;display:flex;align-items:center;
    justify-content:center;font-size:3.5rem;animation:pulse-orb 3s ease-in-out infinite;
    transition:transform .08s;user-select:none;}
.orb:active{transform:scale(.93);}
.coins-value{font-family:'Cinzel Decorative',serif;font-size:2.2rem;color:var(--gold-xl);
    text-shadow:0 0 30px var(--glow);text-align:center;line-height:1;}
.coins-label{font-size:.82rem;color:var(--text-dim);text-align:center;letter-spacing:.06em;margin-top:4px;}
.cps-info{text-align:center;font-size:.82rem;color:var(--text-dim);margin-top:6px;}
.cps-info span{color:var(--gold);}
.clicker-center{display:flex;flex-direction:column;align-items:center;gap:18px;padding:16px 0;}
.upgrade-card{background:var(--bg3);border:1px solid var(--border);border-radius:6px;
    padding:9px 12px;margin-bottom:7px;cursor:pointer;transition:.2s;position:relative;overflow:hidden;}
.upgrade-card:hover:not(.locked){border-color:var(--gold);box-shadow:0 0 10px var(--glow-s);transform:translateX(2px);}
.upgrade-card.locked{opacity:.4;cursor:not-allowed;}
.u-name{font-size:.88rem;font-weight:600;color:var(--gold-l);}
.u-desc{font-size:.76rem;color:var(--text-dim);font-style:italic;margin:2px 0 5px;}
.u-cost{font-size:.8rem;color:var(--amber);font-weight:600;}
.u-owned{float:right;font-size:.73rem;color:var(--text-dim);}
.affordable{border-color:var(--gold)!important;background:linear-gradient(105deg,var(--bg3) 40%,#231b0e 60%,var(--bg3) 80%);background-size:200% auto;animation:shimmer 2.5s linear infinite;}
@keyframes shimmer{0%{background-position:-200% center}100%{background-position:200% center}}
@keyframes float-up{0%{opacity:1;transform:translateY(0) scale(1)}100%{opacity:0;transform:translateY(-70px) scale(1.3)}}
.float-num{position:fixed;pointer-events:none;font-family:'Cinzel Decorative',serif;
    font-size:1.1rem;color:var(--gold-xl);text-shadow:0 0 10px var(--glow);
    animation:float-up .9s ease-out forwards;z-index:9999;}
@keyframes banner-in{0%{opacity:0;transform:translateX(-50%) translateY(-20px) scale(.9)}
    15%{opacity:1;transform:translateX(-50%) translateY(0) scale(1)}80%{opacity:1}100%{opacity:0;transform:translateX(-50%) translateY(-10px)}}
.milestone-banner{position:fixed;top:70px;left:50%;transform:translateX(-50%);
    background:linear-gradient(135deg,#2a1d08,#3d2c0e);border:1px solid var(--gold);
    border-radius:8px;padding:10px 24px;font-family:'Cinzel Decorative',serif;
    font-size:.85rem;color:var(--gold-xl);text-shadow:0 0 15px var(--glow);
    box-shadow:0 0 30px var(--glow);animation:banner-in 3s ease forwards;z-index:500;white-space:nowrap;}
.save-indicator{font-size:.75rem;color:var(--text-dim);text-align:center;}
.save-indicator.saving{color:var(--amber);}
.save-indicator.saved{color:var(--green);}
</style>
@endpush

@section('content')
<div class="game-layout">

    {{-- ===== AMÉLIORATIONS ===== --}}
    <div>
        <div class="card">
            <div class="card-title">✦ Améliorations</div>
            <div id="upgrades-list"></div>
        </div>
        <div class="card">
            <div class="card-title">💰 Derniers achats</div>
            @forelse($recentPayments as $p)
                <div style="font-size:.8rem;padding:4px 0;border-bottom:1px solid var(--border);color:var(--text-dim);">
                    {{ $p->upgrade_name }} — <span style="color:var(--amber)">{{ number_format($p->amount) }} 🪙</span>
                </div>
            @empty
                <div style="font-size:.8rem;color:var(--text-dim);font-style:italic;">Aucun achat encore.</div>
            @endforelse
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-primary" style="margin-top:10px;">Voir tout</a>
        </div>
    </div>

    {{-- ===== ORB CLICKER ===== --}}
    <div>
        <div class="card clicker-center">
            <div>
                <div class="coins-value" id="coins-display">0</div>
                <div class="coins-label">Pièces d'Or</div>
                <div class="cps-info">Production : <span id="cps-display">0</span> /sec</div>
            </div>
            <div class="orb-wrapper">
                <div class="ring-dot"></div>
                <div class="ring-dot"></div>
                <div class="ring-dot"></div>
                <div class="orb" id="main-orb">⚗️</div>
            </div>
            <div style="font-size:.82rem;color:var(--text-dim);text-align:center;">
                <span id="click-power" style="color:var(--gold)">+1</span> par clic &nbsp;|&nbsp; Total : <span id="total-clicks">0</span> clics
            </div>
            <div class="save-indicator" id="save-indicator">✓ Sauvegarde automatique active</div>
        </div>
    </div>

    {{-- ===== LEADERBOARD ===== --}}
    <div>
        <div class="card">
            <div class="card-title">✦ Classement</div>
            <div id="leaderboard-list" style="font-size:.83rem;">Chargement…</div>
        </div>
        <div class="card">
            <div class="card-title">🎫 Support</div>
            <p style="font-size:.83rem;color:var(--text-dim);margin-bottom:10px;">Un problème ? Ouvrez un ticket.</p>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">Nouveau ticket</a>
            <a href="{{ route('tickets.index') }}" class="btn btn-sm" style="background:var(--bg3);border-color:var(--border);color:var(--text-dim);margin-top:6px;">Mes tickets</a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const SAVE_URL    = "{{ route('game.save') }}";
const UPGRADE_URL = "{{ route('game.upgrade') }}";
const LB_URL      = "{{ route('game.leaderboard') }}";

const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const SAVE_URL    = "{{ route('game.save') }}";
const UPGRADE_URL = "{{ route('game.upgrade') }}";
const LB_URL      = "{{ route('game.leaderboard') }}";

const UPGRADES = @json($upgradesForJs);

/* ── STATE ── */
@php
    $savedData = $save ? [
        'coins'          => $save->coins,
        'total_coins'    => $save->total_coins,
        'total_clicks'   => $save->total_clicks,
        'best_cps'       => $save->best_cps,
        'owned_upgrades' => $save->owned_upgrades ?? [],
    ] : null;
@endphp
const saved = @json($savedData);

let state = {
    coins:       saved?.coins       ?? 0,
    totalCoins:  saved?.total_coins ?? 0,
    totalClicks: saved?.total_clicks ?? 0,
    clickPower:  1,
    cps:         0,
    bestCps:     saved?.best_cps    ?? 0,
    owned:       {},
};
UPGRADES.forEach(u => state.owned[u.id] = saved?.owned_upgrades?.[u.id] ?? 0);

function upgradeCost(u) {
    return Math.floor(u.baseCost * Math.pow(1.15, state.owned[u.id]));
}

function recompute() {
    let cp = 1, cps = 0;
    UPGRADES.forEach(u => {
        const n = state.owned[u.id];
        cp  += u.clickBonus * n;
        cps += u.baseCps    * n;
    });
    state.clickPower = Math.max(1, cp);
    state.cps = cps;
}

function fmt(n) {
    if (n >= 1e9) return (n/1e9).toFixed(2)+' G';
    if (n >= 1e6) return (n/1e6).toFixed(2)+' M';
    if (n >= 1e3) return (n/1e3).toFixed(1)+' k';
    return Math.floor(n).toString();
}

function renderCoins() {
    document.getElementById('coins-display').textContent  = fmt(state.coins);
    document.getElementById('cps-display').textContent    = fmt(state.cps);
    document.getElementById('total-clicks').textContent   = fmt(state.totalClicks);
    document.getElementById('click-power').textContent    = '+'+fmt(state.clickPower);
}

function renderUpgrades() {
    const list = document.getElementById('upgrades-list');
    list.innerHTML = '';
    UPGRADES.forEach(u => {
        const cost   = upgradeCost(u);
        const owned  = state.owned[u.id];
        const canBuy = state.coins >= cost;
        const card   = document.createElement('div');
        card.className = 'upgrade-card' + (canBuy ? ' affordable' : ' locked');
        card.innerHTML = `
            <div class="u-name">${u.name} <span class="u-owned">×${owned}</span></div>
            <div class="u-desc">+${u.baseCps}/s &nbsp; +${u.clickBonus} clic</div>
            <div class="u-cost">💰 ${fmt(cost)}</div>`;
        if (canBuy) card.addEventListener('click', () => buyUpgrade(u, cost));
        list.appendChild(card);
    });
}

const shownMilestones = new Set();
const MILESTONES = [
    {coins:100,'msg':'⚗️ Première transmutation !'},
    {coins:1000,'msg':'✨ Mille pièces — vous progressez !'},
    {coins:10000,'msg':'🔥 Dix mille ! L\'alchimie vous sourit.'},
    {coins:100000,'msg':'🌙 Cent mille ! Le Grand Œuvre approche…'},
    {coins:1000000,'msg':'🐉 UN MILLION ! Vous êtes légendaire !'},
];

function checkMilestones() {
    MILESTONES.forEach(m => {
        if (!shownMilestones.has(m.coins) && state.totalCoins >= m.coins) {
            shownMilestones.add(m.coins);
            showBanner(m.msg);
        }
    });
}

function showBanner(msg) {
    const el = document.createElement('div');
    el.className = 'milestone-banner';
    el.textContent = msg;
    document.body.appendChild(el);
    el.addEventListener('animationend', () => el.remove());
}

function spawnFloat(x, y, val) {
    const el = document.createElement('div');
    el.className = 'float-num';
    el.textContent = '+'+fmt(val);
    el.style.left = (x-20)+'px';
    el.style.top  = (y-20)+'px';
    document.body.appendChild(el);
    el.addEventListener('animationend', () => el.remove());
}

/* ── CLICK ── */
document.getElementById('main-orb').addEventListener('click', e => {
    state.coins      += state.clickPower;
    state.totalCoins += state.clickPower;
    state.totalClicks++;
    spawnFloat(e.clientX, e.clientY, state.clickPower);
    checkMilestones();
    renderCoins();
    renderUpgrades();
});

/* ── BUY ── */
function buyUpgrade(u, cost) {
    if (state.coins < cost) return;
    state.coins -= cost;
    state.owned[u.id]++;
    recompute();
    renderCoins();
    renderUpgrades();
    showBanner(`${u.name} acquis !`);

    // Enregistrer le paiement en BDD
    fetch(UPGRADE_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({upgrade_id: u.id, amount: cost}),
    }).catch(console.error);
}

/* ── AUTO-PRODUCTION ── */
setInterval(() => {
    if (state.cps > 0) {
        const g = state.cps / 10;
        state.coins      += g;
        state.totalCoins += g;
        if (state.cps > state.bestCps) state.bestCps = state.cps;
        checkMilestones();
        renderCoins();
        renderUpgrades();
    }
}, 100);

/* ── AUTO-SAVE (toutes les 10s) ── */
const indicator = document.getElementById('save-indicator');
setInterval(() => {
    indicator.className = 'save-indicator saving';
    indicator.textContent = '⏳ Sauvegarde…';
    fetch(SAVE_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
            coins:           Math.floor(state.coins),
            total_coins:     Math.floor(state.totalCoins),
            total_clicks:    Math.floor(state.totalClicks),
            best_cps:        Math.round(state.bestCps * 10) / 10,
            owned_upgrades:  state.owned,
        }),
    }).then(() => {
        indicator.className = 'save-indicator saved';
        indicator.textContent = '✓ Sauvegardé !';
    }).catch(() => {
        indicator.className = 'save-indicator';
        indicator.textContent = '⚠️ Erreur sauvegarde';
    });
}, 10000);

/* ── LEADERBOARD ── */
function loadLeaderboard() {
    fetch(LB_URL).then(r => r.json()).then(data => {
        const list = document.getElementById('leaderboard-list');
        if (!data.length) { list.innerHTML = '<em style="color:var(--text-dim)">Aucun joueur encore.</em>'; return; }
        list.innerHTML = data.map((p, i) => `
            <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border);">
                <span style="color:${i===0?'var(--gold-xl)':i===1?'#c0c0c0':i===2?'#cd7f32':'var(--text-dim)'}">
                    #${i+1} ${p.name}
                </span>
                <span style="color:var(--gold)">${Number(p.total_coins).toLocaleString('fr-FR')} 🪙</span>
            </div>`).join('');
    });
}
loadLeaderboard();
setInterval(loadLeaderboard, 30000);

/* ── INIT ── */
recompute();
renderCoins();
renderUpgrades();
</script>
@endpush
