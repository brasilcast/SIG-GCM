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
 * =========================================
 */$pageTitle = $escola['id'] ? 'Editar Escola' : 'Nova Escola'; ?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-school"></i> <?= $pageTitle ?></h2>
    <div class="breadcrumb">Operacional › Ronda Escolar › Escolas › Formulário</div>
  </div>
  <a href="<?= BASE_URL ?>/ronda-escolar" class="btn btn-secondary">← Voltar</a>
</div>

<div class="table-wrapper" style="max-width:800px">
  <form method="POST" action="<?= BASE_URL ?>/escolas/salvar" style="padding:1.5rem">
    <?= Auth::csrfField() ?>
    <input type="hidden" name="id" value="<?= $escola['id'] ?>">

    <div class="form-group">
      <label class="form-label">Nome da Escola</label>
      <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($escola['nome']) ?>" placeholder="Ex: EMEF Machado de Assis">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
      <div class="form-group">
        <label class="form-label">Endereço</label>
        <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($escola['endereco']) ?>" placeholder="Rua, Número">
      </div>
      <div class="form-group">
        <label class="form-label">Bairro</label>
        <input type="text" name="bairro" class="form-control" value="<?= htmlspecialchars($escola['bairro']) ?>" required>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Diretor(a) Responsável</label>
        <input type="text" name="diretor" class="form-control" value="<?= htmlspecialchars($escola['diretor']) ?>">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Telefone de Contato</label>
        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($escola['telefone']) ?>">
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:1rem;border-top:1px solid var(--border);padding-top:1.5rem">
      <a href="<?= BASE_URL ?>/ronda-escolar" class="btn btn-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Salvar Escola</button>
    </div>
  </form>
</div>
