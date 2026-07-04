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
  <title>Acesso Negado — SIG-GCM</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card" style="text-align:center; max-width:480px">
    <div style="font-size:4rem; margin-bottom:1rem"><i class='fa-solid fa-ban'></i></div>
    <h1 style="font-size:1.5rem; color:var(--danger); margin-bottom:.5rem">Acesso Negado</h1>
    <p style="color:var(--text-muted); margin-bottom:1.5rem">Você não tem permissão para acessar esta funcionalidade.</p>
    <p style="color:var(--text-muted); font-size:.85rem; margin-bottom:1.5rem">Se acredita que isso é um erro, contate seu superior hierárquico ou o administrador do sistema.</p>
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">← Voltar ao Dashboard</a>
  </div>
</div>
</body>
</html>
