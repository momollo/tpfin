<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>⚗️ AlchiClick — @yield('title', 'Jeu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#0a0806;--bg2:#110e09;--bg3:#1a1510;
            --border:#3a2e1a;--gold:#c8922a;--gold-l:#e8b84b;--gold-xl:#f5d87a;
            --amber:#ff8c00;--red:#c0392b;--green:#27ae60;
            --text:#e8d9b8;--text-dim:#8a7a5a;
            --glow:rgba(200,146,42,.35);--glow-s:rgba(200,146,42,.15);
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html,body{min-height:100%;background:var(--bg);color:var(--text);
            font-family:'Crimson Pro',Georgia,serif;font-size:16px;}

        /* NAV */
        nav{background:var(--bg2);border-bottom:1px solid var(--border);
            padding:0 24px;height:52px;display:flex;align-items:center;
            justify-content:space-between;position:sticky;top:0;z-index:200;}
        .nav-logo{font-family:'Cinzel Decorative',serif;font-size:1.05rem;
            color:var(--gold-l);text-shadow:0 0 20px var(--glow);text-decoration:none;}
        .nav-links{display:flex;gap:20px;align-items:center;}
        .nav-links a{color:var(--text-dim);text-decoration:none;font-size:.88rem;
            transition:color .2s;letter-spacing:.03em;}
        .nav-links a:hover,.nav-links a.active{color:var(--gold-l);}
        .nav-user{font-size:.82rem;color:var(--text-dim);}
        .nav-user strong{color:var(--gold);}

        /* FLASH MESSAGES */
        .flash{padding:10px 20px;border-radius:5px;margin:12px auto;
            max-width:900px;font-size:.9rem;border:1px solid;}
        .flash-success{background:#0d2a1a;border-color:#27ae60;color:#a8e6c0;}
        .flash-error{background:#2a0d0d;border-color:#c0392b;color:#e6a8a8;}

        /* CONTAINER */
        .container{max-width:1100px;margin:0 auto;padding:24px 16px;}

        /* CARD */
        .card{background:var(--bg2);border:1px solid var(--border);border-radius:8px;
            padding:20px 24px;margin-bottom:16px;}
        .card-title{font-family:'Cinzel Decorative',serif;font-size:.82rem;
            letter-spacing:.1em;color:var(--gold);text-transform:uppercase;
            margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);}

        /* BUTTONS */
        .btn{display:inline-block;padding:8px 18px;border-radius:5px;
            font-family:'Cinzel Decorative',serif;font-size:.68rem;letter-spacing:.07em;
            cursor:pointer;text-decoration:none;border:1px solid;transition:.2s;
            text-align:center;}
        .btn-primary{background:linear-gradient(135deg,#3d2a08,#5a3e0e);
            border-color:var(--gold);color:var(--gold-l);}
        .btn-primary:hover{box-shadow:0 0 12px var(--glow-s);transform:translateY(-1px);}
        .btn-danger{background:#2a0808;border-color:var(--red);color:#e89090;}
        .btn-danger:hover{box-shadow:0 0 12px rgba(192,57,43,.3);}
        .btn-sm{padding:5px 12px;font-size:.62rem;}

        /* TABLE */
        table{width:100%;border-collapse:collapse;font-size:.88rem;}
        th{text-align:left;padding:8px 12px;background:var(--bg3);
            color:var(--text-dim);font-weight:600;border-bottom:1px solid var(--border);}
        td{padding:8px 12px;border-bottom:1px solid var(--border);}
        tr:hover td{background:var(--bg3);}

        /* FORMS */
        .form-group{margin-bottom:14px;}
        label{display:block;font-size:.85rem;color:var(--text-dim);margin-bottom:4px;}
        input[type=text],input[type=email],input[type=password],
        textarea,select{
            width:100%;background:var(--bg3);border:1px solid var(--border);
            border-radius:4px;padding:8px 12px;color:var(--text);
            font-family:'Crimson Pro',serif;font-size:.95rem;outline:none;
            transition:border-color .2s;}
        input:focus,textarea:focus,select:focus{border-color:var(--gold);}
        textarea{resize:vertical;min-height:100px;}
        .error{color:#e89090;font-size:.8rem;margin-top:3px;}

        /* BADGES */
        .badge{display:inline-block;padding:2px 10px;border-radius:99px;
            font-size:.75rem;font-weight:600;letter-spacing:.04em;}
        .badge-open{background:#0d2a1a;color:#5dcea0;border:1px solid #27ae60;}
        .badge-in_progress{background:#2a2200;color:#f5d87a;border:1px solid var(--gold);}
        .badge-closed{background:#2a0d0d;color:#e89090;border:1px solid var(--red);}
        .badge-completed{background:#0d2a1a;color:#5dcea0;border:1px solid #27ae60;}
        .badge-refunded{background:#2a0d0d;color:#e89090;border:1px solid var(--red);}

        ::-webkit-scrollbar{width:4px;}
        ::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px;}
    </style>
    @stack('styles')
</head>
<body>

<nav>
    <a href="{{ route('game') }}" class="nav-logo">⚗️ AlchiClick</a>
    @auth
    <div class="nav-links">
        <a href="{{ route('game') }}" class="{{ request()->routeIs('game') ? 'active' : '' }}">🎮 Jeu</a>
        <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.*') ? 'active' : '' }}">🎫 Tickets</a>
        <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">💰 Achats</a>
    </div>
    <div class="nav-user">
        Connecté : <strong>{{ Auth::user()->name }}</strong>
        @if(Auth::user()->isAdmin()) <span style="color:var(--amber)">👑 Admin</span> @endif
        &nbsp;|&nbsp;
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf <button type="submit" style="background:none;border:none;color:var(--text-dim);cursor:pointer;font-size:.82rem;">Déconnexion</button>
        </form>
    </div>
    @else
    <div class="nav-links">
        <a href="{{ route('login') }}">Connexion</a>
        <a href="{{ route('register') }}">Inscription</a>
    </div>
    @endauth
</nav>

<div class="container">
    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
