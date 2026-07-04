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
 */$pageTitle='Novo Processo'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-scale-balanced"></i> <?=$processo?'Processo '.$processo['numero']:'Novo Processo'?></h2><div class="breadcrumb">Corregedoria › Novo</div></div>
  <a href="<?=BASE_URL?>/corregedoria" class="btn btn-secondary">← Voltar</a>
</div>
<form method="POST" action="<?=BASE_URL?>/corregedoria/novo">
  <?=Auth::csrfField()?>
  <div class="grid-2" style="margin-bottom:1rem">

    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem">Identificação</h3>
      <div class="form-group"><label class="form-label">Número do Processo *</label>
        <input type="text" name="numero" class="form-control" value="<?=htmlspecialchars($processo['numero']??$numero??'')?>" required>
      </div>
      <div class="form-group"><label class="form-label">Tipo *</label>
        <select name="tipo" class="form-control" required>
          <option value="sindicancia" <?=($processo['tipo']??'')==='sindicancia'?'selected':''?>>Sindicância</option>
          <option value="pad" <?=($processo['tipo']??'')==='pad'?'selected':''?>>PAD — Processo Adm. Disciplinar</option>
          <option value="investigacao_preliminar" <?=($processo['tipo']??'')==='investigacao_preliminar'?'selected':''?>>Investigação Preliminar</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Data de Abertura *</label>
        <input type="date" name="data_abertura" class="form-control" value="<?=htmlspecialchars($processo['data_abertura']??date('Y-m-d'))?>" required>
      </div>
      <div class="form-group"><label class="form-label">Prazo Legal</label>
        <input type="date" name="prazo_legal" class="form-control" value="<?=htmlspecialchars($processo['prazo_legal']??'')?>">
      </div>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem">Investigado</h3>
      <div class="form-group"><label class="form-label">Servidor Investigado</label>
        <select name="investigado_id" class="form-control">
          <option value="">— Selecione (opcional) —</option>
          <?php foreach($agentes as $a): ?>
          <option value="<?=$a['id']?>" <?=($processo['investigado_id']??'')==$a['id']?'selected':''?>><?=htmlspecialchars($a['nome'].($a['matricula']?' ('.$a['matricula'].')':''))?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Descrição do Fato *</label>
        <textarea name="fato_descricao" class="form-control" rows="8" required placeholder="Descreva detalhadamente o fato apurado..."><?=htmlspecialchars($processo['fato_descricao']??'')?></textarea>
      </div>
    </div>
  </div>
  <div class="form-actions">

    <a href="<?=BASE_URL?>/corregedoria" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-scale-balanced"></i> Abrir Processo</button>
  </div>
</form>
