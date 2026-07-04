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
 */$pageTitle='Nova Viatura'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-car"></i> <?=$viatura?'Editar Viatura':'Nova Viatura'?></h2><div class="breadcrumb">Frota › Viaturas</div></div>
  <a href="<?=BASE_URL?>/frota/viaturas" class="btn btn-secondary">← Voltar</a>
</div>
<form method="POST" action="<?=BASE_URL?>/frota/viaturas/<?=$viatura ? $viatura['id'].'/editar' : 'nova'?>">
  <?=Auth::csrfField()?>
  <div class="grid-2" style="margin-bottom:1rem">

    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem">Identificação</h3>
      <div class="form-group"><label class="form-label">Prefixo *</label><input type="text" name="prefixo" class="form-control" value="<?=htmlspecialchars($viatura['prefixo']??'')?>" required placeholder="Ex: GU-01"></div>
      <div class="form-group"><label class="form-label">Placa</label><input type="text" name="placa" class="form-control" value="<?=htmlspecialchars($viatura['placa']??'')?>" placeholder="AAA0A000"></div>
      <div class="form-group"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" value="<?=htmlspecialchars($viatura['modelo']??'')?>" placeholder="Ex: Strada"></div>
      <div class="form-group"><label class="form-label">Marca</label><input type="text" name="marca" class="form-control" value="<?=htmlspecialchars($viatura['marca']??'')?>" placeholder="Ex: Fiat"></div>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem">Dados Técnicos</h3>
      <div class="form-group"><label class="form-label">Ano</label><input type="number" name="ano" class="form-control" value="<?=htmlspecialchars($viatura['ano']??'')?>" min="1990" max="<?=date('Y')+1?>"></div>
      <div class="form-group"><label class="form-label">Cor</label><input type="text" name="cor" class="form-control" value="<?=htmlspecialchars($viatura['cor']??'')?>" placeholder="Ex: Branca"></div>
      <div class="form-group"><label class="form-label">KM Atual</label><input type="number" step="0.1" name="km_atual" class="form-control" value="<?=htmlspecialchars($viatura['km_atual']??'0')?>"></div>
      <div class="form-group"><label class="form-label">Status</label>
        <select name="status" class="form-control">
          <?php foreach(['disponivel'=>'Disponível','em_servico'=>'Em Serviço','manutencao'=>'Manutenção','inativa'=>'Inativa'] as $v=>$l): ?>
          <option value="<?=$v?>" <?=($viatura['status']??'disponivel')===$v?'selected':''?>><?=$l?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Observações</label><textarea name="observacoes" class="form-control" rows="3"><?=htmlspecialchars($viatura['observacoes']??'')?></textarea></div>
    </div>
  </div>
  <div class="form-actions">

    <a href="<?=BASE_URL?>/frota/viaturas" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Salvar Viatura</button>
  </div>
</form>
