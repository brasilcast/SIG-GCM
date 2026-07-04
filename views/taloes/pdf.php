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
 */// Layout especial para impressão/PDF
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Talão <?=htmlspecialchars($talao['numero'])?> — SIG-GCM</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:Arial,sans-serif; font-size:11px; color:#000; padding:15px; }
  .header { display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px; }
  .header h1 { font-size:16px; }
  .header .num { font-size:20px; font-weight:bold; color:#1d4ed8; font-family:monospace; }
  .section { margin-bottom:12px; }
  .section-title { background:#1d4ed8; color:#fff; padding:3px 8px; font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:6px; }
  .grid { display:grid; gap:8px; }
  .g2 { grid-template-columns:1fr 1fr; }
  .g3 { grid-template-columns:1fr 1fr 1fr; }
  .field label { font-size:9px; text-transform:uppercase; color:#555; display:block; }
  .field span { font-weight:bold; }
  .narrativa { border:1px solid #ccc; padding:8px; min-height:80px; background:#f9f9f9; white-space:pre-wrap; }
  .footer { margin-top:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; text-align:center; }
  .footer .assinatura { border-top:1px solid #000; padding-top:5px; font-size:9px; }
  @media print { body { padding:0; } button { display:none; } }
</style>
</head>
<body>
<div class="header">
  <div>
    <div><i class="fa-solid fa-shield-halved"></i> <strong>SIG-GCM — Guarda Civil Municipal</strong></div>
    <h1>Talão de Ocorrência</h1>
  </div>
  <div class="num">#<?=htmlspecialchars($talao['numero'])?></div>
</div>

<div class="section">
  <div class="section-title">Identificação</div>
  <div class="grid g3">
    <div class="field"><label>Data/Hora</label><span><?=date('d/m/Y H:i',strtotime($talao['data_hora_abertura']))?></span></div>
    <div class="field"><label>Natureza</label><span><?=htmlspecialchars($talao['natureza_nome']??'—')?></span></div>
    <div class="field"><label>Status</label><span><?=ucfirst(str_replace('_',' ',$talao['status']))?></span></div>
    <div class="field"><label>BOGM</label><span><?=htmlspecialchars($talao['bogm']?:'—')?></span></div>
    <div class="field"><label>BOPC</label><span><?=htmlspecialchars($talao['bopc']?:'—')?></span></div>
  </div>
</div>

<div class="section">
  <div class="section-title">Localização</div>
  <div class="grid g2">
    <div class="field"><label>Endereço</label><span><?=htmlspecialchars($talao['endereco']?:'—')?></span></div>
    <div class="field"><label>Bairro</label><span><?=htmlspecialchars($talao['bairro']?:'—')?></span></div>
    <div class="field"><label>Referência</label><span><?=htmlspecialchars($talao['referencia']?:'—')?></span></div>
    <?php if(($talao['tipo_equipe']??'viatura') !== 'pop'): ?>
    <div class="field"><label>Viatura</label><span><?=htmlspecialchars($talao['viatura_prefixo']?:'—')?></span></div>
    <?php else: ?>
    <div class="field"><label>Equipe</label><span>POP (Pé/Moto)</span></div>
    <?php endif; ?>
  </div>
</div>

<div class="section">
  <div class="section-title">Agentes</div>
  <div class="field"><label>Responsável</label><span><?=htmlspecialchars($talao['agente_nome']?:'—')?></span></div>
  <?php if(!empty($agentes)): ?>
  <div class="field" style="margin-top:4px"><label>Apoio</label><span><?=implode(', ',array_column($agentes,'nome'))?></span></div>
  <?php endif; ?>
</div>

<?php if(!empty($envolvidos)): ?>
<div class="section">
  <div class="section-title">Pessoas Envolvidas</div>
  <table width="100%" border="1" style="border-collapse:collapse;font-size:10px">
    <tr style="background:#eee"><th style="padding:3px">Tipo</th><th>Nome</th><th>CPF</th><th>Telefone</th></tr>
    <?php foreach($envolvidos as $e): ?>
    <tr><td style="padding:3px"><?=ucfirst($e['tipo'])?></td><td><?=htmlspecialchars($e['nome']??'—')?></td><td><?=htmlspecialchars($e['cpf']??'—')?></td><td><?=htmlspecialchars($e['telefone']??'—')?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<div class="section">
  <div class="section-title">Narrativa</div>
  <div class="narrativa"><?=htmlspecialchars($talao['narrativa']?:'Não informada.')?></div>
</div>

<?php if($talao['encaminhamento']): ?>
<div class="section">
  <div class="section-title">Encaminhamento</div>
  <div class="narrativa"><?=htmlspecialchars($talao['encaminhamento'])?></div>
</div>
<?php endif; ?>

<?php if($talao['medidas_tomadas']): ?>
<div class="section">
  <div class="section-title">Medidas Tomadas</div>
  <div class="narrativa"><?=htmlspecialchars($talao['medidas_tomadas'])?></div>
</div>
<?php endif; ?>

<div class="footer">
  <div class="assinatura">Agente Responsável<br><?=htmlspecialchars($talao['agente_nome']??'__________________')?></div>
  <div class="assinatura">Inspetor de Plantão<br>__________________</div>
  <div class="assinatura">Data / Hora<br><?=date('d/m/Y H:i')?></div>
</div>

<div style="margin-top:15px;font-size:9px;color:#888;text-align:center">
  Documento gerado em <?=date('d/m/Y \à\s H:i')?> — SIG-GCM v<?=APP_VERSION?>
</div>

<script>window.onload = () => window.print();</script>
</body>
</html>
