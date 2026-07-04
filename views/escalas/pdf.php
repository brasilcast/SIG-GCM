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
 */
// Layout especial para impressão/PDF das escalas
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Escalas de Serviço — Semana <?=htmlspecialchars($semana)?> — SIG-GCM</title>
<style>
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:Arial,sans-serif; font-size:11px; color:#000; padding:20px; background:#fff; }
  .header { display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px; }
  .header h1 { font-size:16px; margin-top: 4px; }
  .header .info { text-align:right; font-size:10px; line-height:12px; }
  .section { margin-bottom:20px; }
  .section-title { background:#1d4ed8; color:#fff; padding:4px 8px; font-weight:bold; font-size:11px; text-transform:uppercase; margin-bottom:10px; }
  
  /* Estilização das tabelas */
  table { width:100%; border-collapse:collapse; margin-bottom:15px; font-size:10px; }
  th, td { border:1px solid #1e293b; padding:6px 8px; text-align:left; vertical-align:top; }
  th { background:#f1f5f9; font-weight:bold; font-size:9px; text-transform:uppercase; }
  
  .day-col { width:14%; }
  
  .shift-card { background:#f8fafc; border-left:3px solid #64748b; padding:4px; margin-bottom:4px; border-radius:3px; }
  .shift-card.diurno { border-left-color:#0ea5e9; }
  .shift-card.noturno { border-left-color:#3b82f6; }
  .shift-card.adm { border-left-color:#10b981; }
  
  .shift-agent { font-weight:bold; font-size:10px; }
  .shift-time { font-size:9px; color:#475569; margin-top:2px; }
  .shift-viatura { background:#e2e8f0; padding:1px 3px; border-radius:2px; font-size:8px; font-weight:bold; display:inline-block; margin-top:2px; }
  
  .footer { margin-top:40px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; text-align:center; }
  .footer .assinatura { border-top:1px solid #000; padding-top:5px; font-size:9px; }
  
  @media print { 
    body { padding:0; } 
    button { display:none; } 
    .no-print { display:none; }
  }
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:15px; text-align:right">
  <button onclick="window.print()" style="padding:6px 12px; background:#1d4ed8; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer"><i class="fa-solid fa-print"></i> Imprimir Escala</button>
</div>

<div class="header">
  <div>
    <div><strong>SIG-GCM — Guarda Civil Municipal</strong></div>
    <h1>Escalas de Serviço Operacional e Administrativo</h1>
  </div>
  <div class="info">
    <div><strong>Semana:</strong> <?=htmlspecialchars($semana)?></div>
    <div>Período: <?=date('d/m',strtotime($seg)).' a '.date('d/m/Y',strtotime($dom))?></div>
  </div>
</div>

<!-- ======================= ESCALA 12x36 (OPERACIONAL) ======================= -->
<div class="section">
  <div class="section-title">Regime de Serviço 12x36 (Operacional)</div>
  <?php
  $diasSemana = ['Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'];
  $diasDatas = [];
  for($d=0;$d<7;$d++){
    $diasDatas[] = date('Y-m-d',strtotime("+$d day",strtotime($seg)));
  }
  $porDia = [];
  foreach($escalas as $e){
    $porDia[$e['data']][] = $e;
  }
  ?>
  <table>
    <thead>
      <tr>
        <?php foreach($diasSemana as $i=>$dia): ?>
          <th class="day-col"><?=htmlspecialchars($dia)?><br><font size="1"><?=date('d/m',strtotime($diasDatas[$i]))?></font></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php foreach($diasDatas as $dt): ?>
          <td>
            <?php if(empty($porDia[$dt])): ?>
              <div style="color:#64748b; font-style:italic; font-size:9px">Sem escala</div>
            <?php else: foreach($porDia[$dt] as $e): ?>
              <div class="shift-card <?=$e['turno']?>">
                <div class="shift-agent"><?=htmlspecialchars($e['nome'])?></div>
                <div class="shift-time">
                  <?=ucfirst($e['turno'])?><br>
                  <?=!empty($e['entrada']) ? substr($e['entrada'],0,5) : ''?> – <?=!empty($e['saida']) ? substr($e['saida'],0,5) : ''?>
                </div>
                <?php if(!empty($e['prefixo'])): ?>
                  <div class="shift-viatura"><?=$e['prefixo']?></div>
                <?php endif; ?>
              </div>
            <?php endforeach; endif; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    </tbody>
  </table>
</div>

<!-- ======================= ESCALA ADMINISTRATIVA (5x2) ======================= -->
<div class="section">
  <div class="section-title">Regime de Serviço Administrativo (5x2)</div>
  <?php
  $admPorDia = [];
  foreach($escalasAdm as $ea){
    $admPorDia[$ea['data']][] = $ea;
  }
  ?>
  <table>
    <thead>
      <tr>
        <?php foreach($diasSemana as $i=>$dia): ?>
          <th class="day-col"><?=htmlspecialchars($dia)?><br><font size="1"><?=date('d/m',strtotime($diasDatas[$i]))?></font></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php foreach($diasDatas as $dt): ?>
          <td>
            <?php if(empty($admPorDia[$dt])): ?>
              <div style="color:#64748b; font-style:italic; font-size:9px">Sem escala</div>
            <?php else: foreach($admPorDia[$dt] as $ea): ?>
              <div class="shift-card adm">
                <div class="shift-agent"><?=htmlspecialchars($ea['nome'])?></div>
                <div class="shift-time">
                  <?=!empty($ea['entrada']) ? substr($ea['entrada'],0,5) : '07:30'?>–<?=!empty($ea['saida']) ? substr($ea['saida'],0,5) : '11:30'?><br>
                  <?=!empty($ea['entrada_tarde']) ? substr($ea['entrada_tarde'],0,5) : '13:00'?>–<?=!empty($ea['saida_tarde']) ? substr($ea['saida_tarde'],0,5) : '17:00'?>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    </tbody>
  </table>
</div>

<div class="footer">
  <div class="assinatura">Elaborado por<br>Auxiliar de Escalas</div>
  <div class="assinatura">Visto e Homologado<br>Comandante GCM</div>
  <div class="assinatura">Data da Emissão<br><?=date('d/m/Y H:i')?></div>
</div>

<div style="margin-top:25px; font-size:9px; color:#64748b; text-align:center">
  Documento gerado em <?=date('d/m/Y \à\s H:i')?> — SIG-GCM v<?=APP_VERSION?> — Eldorado dos Ventos - SP
</div>

<script>
  window.onload = () => {
    setTimeout(() => { window.print(); }, 500);
  };
</script>
</body>
</html>
