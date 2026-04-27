<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚗️ AlchiClick — Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&family=Crimson+Pro:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#0a0806;--bg2:#110e09;--bg3:#1a1510;--border:#3a2e1a;--gold:#c8922a;--gold-l:#e8b84b;--gold-xl:#f5d87a;--text:#e8d9b8;--text-dim:#8a7a5a;--glow:rgba(200,146,42,.35);}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{min-height:100vh;background:var(--bg);color:var(--text);font-family:'Crimson Pro',serif;display:flex;align-items:center;justify-content:center;}
        .auth-box{background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:36px 40px;width:100%;max-width:400px;}
        .auth-logo{font-family:'Cinzel Decorative',serif;font-size:1.3rem;color:var(--gold-xl);text-shadow:0 0 20px var(--glow);text-align:center;margin-bottom:24px;}
        .form-group{margin-bottom:14px;}
        label{display:block;font-size:.85rem;color:var(--text-dim);margin-bottom:4px;}
        input{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:4px;padding:9px 12px;color:var(--text);font-family:'Crimson Pro',serif;font-size:.95rem;outline:none;transition:border-color .2s;}
        input:focus{border-color:var(--gold);}
        .btn{width:100%;padding:10px;border-radius:5px;font-family:'Cinzel Decorative',serif;font-size:.7rem;letter-spacing:.07em;cursor:pointer;border:1px solid var(--gold);background:linear-gradient(135deg,#3d2a08,#5a3e0e);color:var(--gold-l);transition:.2s;margin-top:6px;}
        .btn:hover{box-shadow:0 0 15px var(--glow);}
        .error{color:#e89090;font-size:.8rem;margin-top:3px;}
        .auth-link{text-align:center;margin-top:16px;font-size:.88rem;color:var(--text-dim);}
        .auth-link a{color:var(--gold);text-decoration:none;}
    </style>
</head>
<body>
<div class="auth-box">
    <div class="auth-logo">⚗️ AlchiClick</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label>Nom d'alchimiste</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn">Rejoindre l'Athanor</button>
    </form>

    <div class="auth-link">
        Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
    </div>
</div>
</body>
</html>
