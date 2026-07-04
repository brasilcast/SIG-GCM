<?php
/**
 * =========================================
 * SIG-GCM — Sistema Integrado de Gestão
 * Guarda Civil Municipal
 * =========================================
 * SISTEMA DE TESTE — Projeto acadêmico (TG/TC)
 * Desenvolvido 100% por:
 * Autor   : Rafaela Cristina Alcântara da Silva
 * GitHub  : https://github.com/rafaela13c
 * LinkedIn: https://www.linkedin.com/in/rafaela-cas1308/
 * =========================================
 * Este sistema NÃO é oficial e NÃO será
 * utilizado pela GCM. Uso exclusivo para
 * fins de estudo acadêmico.
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
  <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo-gcm.png?v=<?= time() ?>" type="image/png">
  <style>
    .auth-particles { position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0; }
    .particle { position:absolute;width:4px;height:4px;background:rgba(59,130,246,0.35);border-radius:50%;animation:float linear infinite; }
    @keyframes float {
      0%   { transform:translateY(100vh) rotate(0deg);opacity:0; }
      10%  { opacity:1; }
      90%  { opacity:1; }
      100% { transform:translateY(-100px) rotate(720deg);opacity:0; }
    }
    .auth-wrapper { position:relative;z-index:1; }
    .auth-card { animation:fadeInUp .5s ease; }
    @keyframes fadeInUp {
      from { opacity:0;transform:translateY(30px); }
      to   { opacity:1;transform:translateY(0); }
    }
  </style>
</head>
<body>

<div class="auth-particles" id="particles"></div>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-icon">
        <img src="<?= BASE_URL ?>/assets/img/logo-gcm.png?v=<?= time() ?>" alt="Logo GCM" style="width:100px;height:100px;object-fit:contain">
      </div>
      <h1 style="font-size:1.6rem;margin-bottom:0.2rem"><?= APP_NAME ?></h1>
      <div style="font-size:0.95rem;font-weight:600;color:var(--primary);margin-bottom:0.2rem">Sistema Integrado de Gestão da GCM</div>
      <span>Guarda Civil Municipal de <?= APP_CITY ?></span>
    </div>

    <!-- Aviso de Sistema Teste -->
    <div style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.3); border-radius: var(--radius-sm); padding: 0.85rem; margin-bottom: 1.25rem; font-size: 0.82rem; line-height: 1.4; color: var(--text);">
      <div style="display:flex; align-items:center; gap:0.5rem; font-weight:700; color:var(--info); margin-bottom:0.3rem">
        <i class="fa-solid fa-graduation-cap"></i>
        <span>AMBIENTE DE TESTE ACADÊMICO</span>
      </div>
      <p style="margin: 0; color: var(--text-muted); margin-bottom: 0.5rem;">
        Desenvolvido 100% por <strong>Rafaela Cristina Alcântara da Silva</strong> como objeto de estudo para seu TG/TC.
        Este sistema é experimental, com dados fictícios.
      </p>
      <div style="font-size: 0.78rem;">
        <a href="https://github.com/rafaela13c" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
          <i class="fa-brands fa-github"></i> Código no GitHub
        </a>
      </div>
    </div>

    <?php $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
        <i class="fa-solid fa-<?= $flash['type'] === 'error' ? 'triangle-exclamation' : 'circle-check' ?>"></i>
        <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/login" id="loginForm">
      <?= Auth::csrfField() ?>

      <div class="form-group">
        <label class="form-label" for="username">Usuário / Matrícula</label>
        <div class="input-group">
          <span class="input-icon"><i class="fa-solid fa-user"></i></span>
          <input type="text" name="username" id="username" class="form-control"
            placeholder="Digite seu usuário"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            autocomplete="username" required autofocus>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Senha</label>
        <div class="input-group">
          <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
          <input type="password" name="password" id="password" class="form-control"
            placeholder="Digite sua senha" autocomplete="current-password" required>
          <button type="button" class="toggle-password" id="btnToggle" onclick="toggleSenha('password','btnToggle')">
            <i class="fa-solid fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg" id="btnLogin">
        <span class="btn-text"><i class="fa-solid fa-right-to-bracket"></i> Entrar no Sistema</span>
        <span class="btn-loading" style="display:none"><i class="fa-solid fa-spinner fa-spin"></i> Aguarde...</span>
      </button>

      <div style="text-align:center;margin-top:1rem;font-size:0.85rem">
        <p style="color:var(--text-muted);margin:0">
          <i class="fa-solid fa-circle-info"></i> Esqueceu a senha ou é seu 1º acesso?<br>
          <span style="font-weight:600;color:var(--text)">Solicite sua senha ao administrador do sistema.</span>
        </p>
      </div>
    </form>

    <!-- Acesso Rápido de Teste -->
    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed var(--border);">
      <h3 style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 0.6rem; text-align: center;">
        <i class="fa-solid fa-key"></i> Credenciais de Teste
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem;">
        <button type="button" class="btn btn-secondary btn-sm" style="justify-content: center; padding: 0.35rem 0.5rem; font-size: 0.75rem;" onclick="preencherLogin('admin', 'Admin@2025')">
          <i class="fa-solid fa-user-shield" style="color:var(--danger)"></i> Admin
        </button>
        <button type="button" class="btn btn-secondary btn-sm" style="justify-content: center; padding: 0.35rem 0.5rem; font-size: 0.75rem;" onclick="preencherLogin('operador.teste', 'Teste@2025')">
          <i class="fa-solid fa-headset" style="color:var(--primary-light)"></i> Operador
        </button>
        <button type="button" class="btn btn-secondary btn-sm" style="justify-content: center; padding: 0.35rem 0.5rem; font-size: 0.75rem;" onclick="preencherLogin('inspetor.teste', 'Teste@2025')">
          <i class="fa-solid fa-star" style="color:var(--warning)"></i> Inspetor
        </button>
        <button type="button" class="btn btn-secondary btn-sm" style="justify-content: center; padding: 0.35rem 0.5rem; font-size: 0.75rem;" onclick="preencherLogin('subcmd.teste', 'Teste@2025')">
          <i class="fa-solid fa-chess-knight" style="color:var(--info)"></i> Subcmd.
        </button>
        <button type="button" class="btn btn-secondary btn-sm" style="justify-content: center; padding: 0.35rem 0.5rem; font-size: 0.75rem;" onclick="preencherLogin('comandante.teste', 'Teste@2025')">
          <i class="fa-solid fa-chess-king" style="color:var(--success)"></i> Comandante
        </button>
        <button type="button" class="btn btn-secondary btn-sm" style="justify-content: center; padding: 0.35rem 0.5rem; font-size: 0.75rem;" onclick="preencherLogin('corregedor.teste', 'Teste@2025')">
          <i class="fa-solid fa-gavel" style="color:#a855f7"></i> Corregedor
        </button>
      </div>
    </div>

    <p style="text-align:center;margin-top:1.5rem;font-size:.78rem;color:var(--text-muted)">
      <i class="fa-solid fa-shield-halved"></i> <?= APP_NAME ?> v<?= APP_VERSION ?> &nbsp;|&nbsp; <?= APP_CITY ?>
    </p>
  </div>
</div>

<script>
(function() {
  const c = document.getElementById('particles');
  for (let i = 0; i < 25; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.cssText = `left:${Math.random()*10}%;`; // Safe initialization or let defaults
    // Wait, let's keep the original beautiful particles:
    p.style.cssText = `left:${Math.random()*100}%;width:${Math.random()*4+2}px;height:${Math.random()*4+2}px;animation-duration:${Math.random()*15+10}s;animation-delay:${Math.random()*10}s;opacity:${Math.random()*.5+.2}`;
    c.appendChild(p);
  }
})();

function toggleSenha(id, btnId) {
  const inp = document.getElementById(id);
  const ico = document.getElementById('eyeIcon');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.className = 'fa-solid fa-eye-slash';
  } else {
    inp.type = 'password';
    ico.className = 'fa-solid fa-eye';
  }
}

function preencherLogin(u, s) {
  document.getElementById('username').value = u;
  document.getElementById('password').value = s;
}

document.getElementById('loginForm').addEventListener('submit', function() {
  const btn = document.getElementById('btnLogin');
  btn.querySelector('.btn-text').style.display = 'none';
  btn.querySelector('.btn-loading').style.display = 'inline';
  btn.disabled = true;
});
</script>
</body>
</html>
