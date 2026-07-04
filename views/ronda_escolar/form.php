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
 */$pageTitle='Registrar Ronda'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-school"></i> Registrar Ronda Escolar</h2><div class="breadcrumb">Ronda Escolar › Registrar</div></div>
  <a href="<?=BASE_URL?>/ronda-escolar" class="btn btn-secondary">← Voltar</a>
</div>
<form method="POST" action="<?=BASE_URL?>/ronda-escolar/registrar" enctype="multipart/form-data">
  <?=Auth::csrfField()?>
  <div class="grid-2" style="margin-bottom:1rem">

    <div class="table-wrapper" style="padding:1.5rem">
      <div class="form-group"><label class="form-label">Escola *</label>
        <select name="escola_id" class="form-control" required>
          <option value="">— Selecione a escola —</option>
          <?php foreach($escolas as $e): ?><option value="<?=$e['id']?>"><?=htmlspecialchars($e['nome'].' — '.$e['bairro'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Viatura</label>
        <select name="viatura_id" class="form-control">
          <option value="">— Sem viatura —</option>
          <?php foreach($viaturas as $v): ?><option value="<?=$v['id']?>"><?=htmlspecialchars($v['prefixo'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">

        <div class="form-group"><label class="form-label">Latitude</label><input type="text" name="latitude" id="lat" class="form-control"></div>
        <div class="form-group"><label class="form-label">Longitude</label><input type="text" name="longitude" id="lon" class="form-control"></div>
      </div>
      <button type="button" onclick="capturarGPS('lat','lon','btnGps')" id="btnGps" class="btn btn-secondary btn-sm"><i class="fa-solid fa-location-dot"></i> Capturar GPS</button>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <div class="form-group"><label class="form-label">Foto da Ronda</label>
        <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImagem(this,'prev')">
        <img id="prev" style="display:none;width:100%;height:120px;object-fit:cover;border-radius:4px;margin-top:.5rem;border:1px solid var(--border)">
      </div>
      <div class="form-group"><label class="form-label">Observações</label>
        <textarea name="observacoes" class="form-control" rows="6" placeholder="Situação encontrada, ocorrências, etc."></textarea>
      </div>
    </div>
  </div>
  <div class="form-actions">

    <a href="<?=BASE_URL?>/ronda-escolar" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-school"></i> Registrar Ronda</button>
  </div>
</form>
