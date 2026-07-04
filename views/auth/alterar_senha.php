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
  <title>Alterar Senha — SIG-GCM</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-icon"><i class="fa-solid fa-lock"></i></div>
      <h1>Alterar Senha</h1>
      <span>
        <?php if ($_SESSION['senha_temporaria'] ?? false): ?>
          Sua senha é temporária. Defina uma nova senha para continuar.
        <?php else: ?>
          Redefina sua senha de acesso.
        <?php endif; ?>
      </span>
    </div>

    <?php $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
        <?= $flash['type'] === 'error' ? '<i class="fa-solid fa-triangle-exclamation"></i>' : '<i class="fa-solid fa-circle-check" style="color:var(--success)"></i>' ?>
        <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/alterar-senha" id="formSenha">
      <?= Auth::csrfField() ?>

      <div class="form-group">
        <label class="form-label">Nova Senha</label>
        <div class="input-group">
          <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
          <input type="password" name="nova_senha" id="nova_senha" class="form-control"
                 placeholder="Mínimo 8 caracteres (letras e números)"
                 autocomplete="new-password" required minlength="8"
                 oninput="checkSenha(this.value)">
          <button type="button" class="toggle-password" onclick="toggleSenha('nova_senha', this)"><i class='fa-solid fa-eye'></i></button>
        </div>
        <div class="password-strength" id="strengthBar">
          <div class="password-strength-bar" id="strengthFill"></div>
        </div>
        <div id="strengthLabel" style="font-size:0.75rem; margin-top:0.3rem; color:var(--text-muted)"></div>
      </div>

      <div class="form-group">
        <label class="form-label">Confirmar Nova Senha</label>
        <div class="input-group">
          <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
          <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control"
                 placeholder="Repita a nova senha"
                 autocomplete="new-password" required>
          <button type="button" class="toggle-password" onclick="toggleSenha('confirmar_senha', this)"><i class='fa-solid fa-eye'></i></button>
        </div>
        <div id="matchLabel" style="font-size:0.75rem; margin-top:0.3rem;"></div>
      </div>

      <div style="background:var(--surface-2); border-radius:var(--radius-sm); padding:0.85rem; margin-bottom:1.25rem; font-size:0.8rem; color:var(--text-muted);">
        <strong style="color:var(--text); display:block; margin-bottom:0.4rem;"><i class="fa-solid fa-file-lines"></i> Requisitos:</strong>
        <div id="req-len"  class="req-item">○ Mínimo de <?= PASSWORD_MIN_LENGTH ?> caracteres</div>
        <div id="req-lett" class="req-item">○ Pelo menos uma letra</div>
        <div id="req-num"  class="req-item">○ Pelo menos um número</div>
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg" id="btnSalvar">
        <i class='fa-solid fa-key'></i> Definir Nova Senha
      </button>

      <?php if (!($_SESSION['senha_temporaria'] ?? false)): ?>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary btn-block" style="margin-top:0.75rem;">
          ← Cancelar
        </a>
      <?php endif; ?>
    </form>
  </div>
</div>
<style>
  .req-item { padding: 0.15rem 0; transition: color 0.2s; }
  .req-item.ok { color: var(--success); }
  .req-item.ok::before { content: '✓ '; }
</style>
<script>
function toggleSenha(id, btn) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.innerHTML = el.type === 'password' ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
}

function checkSenha(val) {
  const bar    = document.getElementById('strengthBar');
  const fill   = document.getElementById('strengthFill');
  const label  = document.getElementById('strengthLabel');
  const reqLen = document.getElementById('req-len');
  const reqLet = document.getElementById('req-lett');
  const reqNum = document.getElementById('req-num');

  bar.style.display = 'block';

  const hasLen  = val.length >= <?= PASSWORD_MIN_LENGTH ?>;
  const hasLett = /[a-zA-Z]/.test(val);
  const hasNum  = /[0-9]/.test(val);

  reqLen.className = 'req-item' + (hasLen  ? ' ok' : '');
  reqLet.className = 'req-item' + (hasLett ? ' ok' : '');
  reqNum.className = 'req-item' + (hasNum  ? ' ok' : '');

  let score = 0;
  if (hasLen)  score += 30;
  if (hasLett) score += 30;
  if (hasNum)  score += 20;
  if (val.length >= 12) score += 10;
  if (/[^a-zA-Z0-9]/.test(val)) score += 10;

  fill.style.width = score + '%';
  if (score < 40)      { fill.style.background = '#ef4444'; label.innerHTML = '<i class="fa-solid fa-circle"></i> Fraca'; label.style.color = '#ef4444'; }
  else if (score < 70) { fill.style.background = '#f59e0b'; label.innerHTML = '<i class="fa-solid fa-circle"></i> Média'; label.style.color = '#f59e0b'; }
  else                 { fill.style.background = '#22c55e'; label.innerHTML = '<i class="fa-solid fa-circle"></i> Forte'; label.style.color = '#22c55e'; }
}

document.getElementById('confirmar_senha').addEventListener('input', function() {
  const nova = document.getElementById('nova_senha').value;
  const ml   = document.getElementById('matchLabel');
  if (this.value === '') { ml.textContent = ''; return; }
  if (this.value === nova) { ml.textContent = '✓ Senhas iguais'; ml.style.color = '#22c55e'; }
  else                     { ml.textContent = '✗ Senhas diferentes'; ml.style.color = '#ef4444'; }
});
</script>
</body>
</html>
