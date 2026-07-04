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
 */$pageTitle='Nova Vítima'; ?>
<div class="page-header">
  <div><h2><i class='fa-solid fa-shield-halved'></i> <?=$vitima?'Editar Vítima':'Nova Vítima'?></h2><div class="breadcrumb">Maria da Penha › Cadastro</div></div>
  <a href="<?=BASE_URL?>/maria-penha" class="btn btn-secondary">← Voltar</a>
</div>
<form method="POST" action="<?=BASE_URL?>/maria-penha/nova">
  <?=Auth::csrfField()?>
  <input type="hidden" name="id" value="<?=htmlspecialchars($vitima['id']??'')?>">
  <div class="grid-2" style="margin-bottom:1rem">

    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-user"></i> Dados da Vítima</h3>
      <div class="form-group"><label class="form-label">Nome Completo *</label><input type="text" name="nome" class="form-control" value="<?=htmlspecialchars($vitima['nome']??'')?>" required></div>
      <div class="form-group"><label class="form-label">CPF</label><input type="text" name="cpf" class="form-control" value="<?=htmlspecialchars($vitima['cpf']??'')?>" placeholder="000.000.000-00"></div>
      <div class="form-group"><label class="form-label">Data de Nascimento</label><input type="date" name="data_nascimento" class="form-control" value="<?=htmlspecialchars($vitima['data_nascimento']??'')?>"></div>
      <div class="form-group"><label class="form-label">Telefone</label><input type="text" name="telefone" class="form-control" value="<?=htmlspecialchars($vitima['telefone']??'')?>" placeholder="(00) 90000-0000"></div>
      <div class="form-group"><label class="form-label">Nível de Prioridade</label>
        <select name="prioridade" class="form-control">
          <?php foreach([1=>'P1 — Máxima',2=>'P2 — Alta',3=>'P3 — Média',4=>'P4 — Baixa',5=>'P5 — Mínima'] as $p=>$l): ?>
          <option value="<?=$p?>" <?=($vitima['prioridade']??3)==$p?'selected':''?>><?=$l?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Endereço</label><input type="text" name="endereco" class="form-control" value="<?=htmlspecialchars($vitima['endereco']??'')?>"></div>
      <div class="form-group"><label class="form-label">Bairro</label><input type="text" name="bairro" class="form-control" value="<?=htmlspecialchars($vitima['bairro']??'')?>"></div>
      <div class="form-row">

        <div class="form-group"><label class="form-label">Latitude</label><input type="text" name="latitude" id="lat" class="form-control" value="<?=htmlspecialchars($vitima['latitude']??'')?>"></div>
        <div class="form-group"><label class="form-label">Longitude</label><input type="text" name="longitude" id="lon" class="form-control" value="<?=htmlspecialchars($vitima['longitude']??'')?>"></div>
      </div>
      <button type="button" onclick="capturarGPS('lat','lon','btnGps')" id="btnGps" class="btn btn-secondary btn-sm"><i class="fa-solid fa-location-dot"></i> GPS</button>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-circle-exclamation" style="color:var(--danger)"></i> Dados do Agressor</h3>
      <div class="form-group"><label class="form-label">Nome do Agressor</label><input type="text" name="nome_agressor" class="form-control" value="<?=htmlspecialchars($vitima['nome_agressor']??'')?>"></div>
      <div class="form-group"><label class="form-label">CPF do Agressor</label><input type="text" name="cpf_agressor" class="form-control" value="<?=htmlspecialchars($vitima['cpf_agressor']??'')?>"></div>
      <div class="form-group"><label class="form-label">Vínculo com a Vítima</label><input type="text" name="vinculo_agressor" class="form-control" value="<?=htmlspecialchars($vitima['vinculo_agressor']??'')?>" placeholder="Ex: Ex-marido, Companheiro"></div>
      <div class="form-group"><label class="form-label">Contato Emergência</label><input type="text" name="telefone_emergencia" class="form-control" value="<?=htmlspecialchars($vitima['telefone_emergencia']??'')?>" placeholder="Telefone de um familiar"></div>
      <div class="form-group"><label class="form-label">Nome (Emergência)</label><input type="text" name="nome_emergencia" class="form-control" value="<?=htmlspecialchars($vitima['nome_emergencia']??'')?>"></div>
      <div class="form-group"><label class="form-label">Histórico de Violência</label><textarea name="historico_violencia" class="form-control" rows="4" placeholder="Descreva o histórico..."><?=htmlspecialchars($vitima['historico_violencia']??'')?></textarea></div>
      <div class="form-group"><label class="form-label">Observações</label><textarea name="observacoes" class="form-control" rows="3"><?=htmlspecialchars($vitima['observacoes']??'')?></textarea></div>
    </div>
  </div>
  <div class="form-actions">

    <a href="<?=BASE_URL?>/maria-penha" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class='fa-solid fa-shield-halved'></i> Salvar Cadastro</button>
  </div>
</form>
