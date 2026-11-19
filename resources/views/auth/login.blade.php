<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — JeCMAP Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="stylesheet" href="{{ asset('css/jecmap.css') }}">
<style>
  body{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(160deg, var(--navy) 0%, #0A1E3D 100%);
    padding:24px;
  }
  .login-card{
    background:#fff;
    border-radius:20px;
    padding:44px 40px;
    width:100%;
    max-width:400px;
    box-shadow:0 24px 60px -20px rgba(0,0,0,0.4);
  }
  .login-logo{
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:28px;
  }
  .login-logo img{
    width:64px; height:64px; object-fit:contain;
    margin-bottom:12px;
  }
  .login-logo h1{
    font-size:1.3rem;
    color:var(--navy);
    margin-bottom:4px;
  }
  .login-logo p{
    font-size:0.85rem;
    color:var(--ink-soft);
  }
  .login-field{ margin-bottom:18px; }
  .login-field label{
    display:block;
    font-size:0.85rem;
    font-weight:600;
    color:var(--navy);
    margin-bottom:6px;
  }
  .login-field input{
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    border:1.5px solid var(--line);
    font-family:'Inter', sans-serif;
    font-size:0.95rem;
    transition:border-color .2s ease;
  }
  .login-field input:focus{
    outline:none;
    border-color:var(--green);
  }
  .login-remember{
    display:flex; align-items:center; gap:8px;
    margin-bottom:24px;
    font-size:0.88rem;
    color:var(--ink-soft);
  }
  .login-error{
    background:#FCEBEA;
    color:#B3261E;
    padding:10px 14px;
    border-radius:8px;
    font-size:0.85rem;
    margin-bottom:18px;
  }
  .login-status{
    background:var(--green-light);
    color:var(--green);
    padding:10px 14px;
    border-radius:8px;
    font-size:0.85rem;
    margin-bottom:18px;
  }
  .login-submit{
    width:100%;
    background:var(--green);
    color:#fff;
    border:none;
    padding:14px;
    border-radius:10px;
    font-weight:600;
    font-size:0.95rem;
    cursor:pointer;
    transition: background .2s ease, transform .2s ease;
  }
  .login-submit:hover{
    background:#166B47;
    transform:translateY(-1px);
  }
  .password-wrap{
  position:relative;
}
.password-wrap input{
  padding-right:44px;
}
#toggle-password{
  position:absolute;
  right:12px; top:50%;
  transform:translateY(-50%);
  background:none;
  border:none;
  cursor:pointer;
  color:var(--ink-soft);
  padding:4px;
  display:flex;
  transition:color .2s ease;
}
#toggle-password:hover{
  color:var(--green);
}
#toggle-password svg{
  width:20px; height:20px;
}
</style>
</head>
<body>

<div class="login-card">
  <div class="login-logo">
    <img src="{{ asset('images/logo.png') }}" alt="JeCMAP">
    <h1>Espace administrateur</h1>
    <p>Connecte-toi pour accéder au back-office JeCMAP</p>
  </div>

  @if(session('status'))
    <div class="login-status">{{ session('status') }}</div>
  @endif

  @if($errors->any())
    <div class="login-error">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="login-field">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    </div>

<div class="login-field">
  <label for="password">Mot de passe</label>
  <div class="password-wrap">
    <input id="password" type="password" name="password" required autocomplete="current-password">
    <button type="button" id="toggle-password" aria-label="Afficher le mot de passe">
      <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
    </button>
  </div>
</div>

    <label class="login-remember">
      <input type="checkbox" name="remember" style="width:auto;">
      Se souvenir de moi
    </label>

    <button type="submit" class="login-submit">Se connecter</button>
  </form>
</div>
<script>
  const togglePassword = document.getElementById('toggle-password');
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eye-icon');

  togglePassword.addEventListener('click', () => {
    const isVisible = passwordInput.type === 'text';
    passwordInput.type = isVisible ? 'password' : 'text';

    eyeIcon.innerHTML = isVisible
      ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/>'
      : '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>';
  });
</script>
</body>
</html>