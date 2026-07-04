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
 */$pageTitle='Novo Comunicado'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-bullhorn"></i> Novo Comunicado</h2><div class="breadcrumb">Comunicados › Novo</div></div>
  <a href="<?=BASE_URL?>/comunicados" class="btn btn-secondary">← Voltar</a>
</div>
<form method="POST" action="<?=BASE_URL?>/comunicados/novo">
  <?=Auth::csrfField()?>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
    <div class="table-wrapper" style="padding:1.5rem">
      <div class="form-group"><label class="form-label">Título *</label><input type="text" name="titulo" class="form-control" required placeholder="Título do comunicado"></div>
      <div class="form-group"><label class="form-label">Conteúdo *</label><textarea name="conteudo" class="form-control" rows="8" required placeholder="Mensagem detalhada..."></textarea></div>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <div class="form-group"><label class="form-label">Tipo</label>
        <select name="tipo" class="form-control">
          <option value="informacao"><i class="fa-solid fa-circle-info"></i> Informação</option>
          <option value="atencao"><i class="fa-solid fa-triangle-exclamation"></i> Atenção</option>
          <option value="urgente"><i class="fa-solid fa-circle-exclamation" style="color:var(--danger)"></i> Urgente</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Destino</label>
        <select name="destino" class="form-control" id="destinoSel" onchange="toggleDestino(this.value)">
          <option value="todos">Todos os usuários</option>
          <option value="perfil">Perfil específico</option>
          <option value="usuario">Usuário específico</option>
        </select>
      </div>
      <div id="perfilBox" class="form-group" style="display:none">
        <label class="form-label">Perfil de destino</label>
        <select name="perfil_destino" class="form-control">
          <?php foreach(['operador','inspetor','subcomandante','comandante','corregedor','admin'] as $p): ?>
          <option value="<?=$p?>"><?=ucfirst($p)?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div id="usuarioBox" class="form-group" style="display:none">
        <label class="form-label">Usuário de destino</label>
        <select name="usuario_destino_id" class="form-control">
          <?php foreach($usuarios as $u): ?><option value="<?=$u['id']?>"><?=htmlspecialchars($u['nome'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Válido até (opcional)</label><input type="date" name="valido_ate" class="form-control"></div>
      <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem">
        <input type="checkbox" name="requer_confirmacao" value="1" style="accent-color:var(--primary-light)">
        Exigir confirmação de leitura
      </label>
    </div>
  </div>
  <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem">
    <a href="<?=BASE_URL?>/comunicados" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-bullhorn"></i> Publicar Comunicado</button>
  </div>
</form>
<script>
function toggleDestino(v){
  document.getElementById('perfilBox').style.display  = v==='perfil' ?'block':'none';
  document.getElementById('usuarioBox').style.display = v==='usuario'?'block':'none';
}
</script>
