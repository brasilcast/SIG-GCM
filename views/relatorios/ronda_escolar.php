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
 */$labelsEscola  = array_column($porEscola, 'nome');
$valuesEscola  = array_map('intval', array_column($porEscola, 'total'));
$labelsSemana  = array_column($porSemana, 'semana');
$valuesSemana  = array_map('intval', array_column($porSemana, 'total'));
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-graduation-cap"></i> Relatório — Ronda Escolar</h2>
    <div class="breadcrumb">Relatórios › Ronda Escolar</div>
  </div>
  <div style="display:flex;gap:.5rem">
    <a href="<?=BASE_URL?>/relatorios" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
    <a href="?<?=http_build_query(array_merge($_GET, ['export'=>1]))?>" class="btn btn-success"><i class="fas fa-download"></i> Exportar CSV</a>
  </div>
</div>

<form method="GET">
<div class="rel-filter-bar">
  <div class="form-group">
    <label class="form-label">Início</label>
    <input type="date" name="inicio" class="form-control" value="<?=htmlspecialchars($inicio)?>">
  </div>
  <div class="form-group">
    <label class="form-label">Fim</label>
    <input type="date" name="fim" class="form-control" value="<?=htmlspecialchars($fim)?>">
  </div>
  <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
</div>
</form>

<div class="rel-summary-row">
  <div class="rel-summary-item">
    <div class="val"><?=$totalRondas?></div>
    <div class="lbl">Rondas no Período</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?=count($semVisita)?></div>
    <div class="lbl">Escolas sem visita/7d</div>
  </div>
</div>

<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Rondas por Semana</div>
    <canvas id="chartSem" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-bar"></i> Top Escolas</div>
    <canvas id="chartEsc" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-user-shield"></i> Top 5 Agentes</div>
    <ul style="list-style:none;padding:0;margin:0">
      <?php foreach($topAgentes as $i=>$a): ?>
      <li style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border)">
        <span><?=$i+1?>. <?=htmlspecialchars($a['nome'])?></span>
        <span class="badge badge-primary"><?=$a['total']?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php if(!empty($semVisita)): ?>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title" style="color:#ef4444"><i class="fas fa-exclamation-triangle"></i> Escolas sem visita há +7 dias</div>
    <ul style="list-style:none;padding:0;margin:0">
      <?php foreach($semVisita as $e): ?>
      <li style="padding:.35rem 0;border-bottom:1px solid var(--border);font-size:.85rem">
        <strong><?=htmlspecialchars($e['nome'])?></strong> — <?=htmlspecialchars($e['bairro'] ?? '')?>
        <span style="float:right;color:var(--text-muted);font-size:.78rem"><?=$e['ultima'] ? 'Últ: '.date('d/m/Y',strtotime($e['ultima'])) : 'Sem visita'?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
</div>

<div class="table-wrapper">
  <div class="table-header"><h3>Rondas Realizadas</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Data/Hora</th><th>Escola</th><th>Bairro</th><th>Agente</th><th>Viatura</th><th>Observações</th></tr></thead>
      <tbody>
      <?php if(empty($rondas)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhuma ronda encontrada.</td></tr>
      <?php else: foreach($rondas as $r): ?>
      <tr>
        <td><?=date('d/m/Y H:i', strtotime($r['realizado_em']))?></td>
        <td><?=htmlspecialchars($r['escola_nome'])?></td>
        <td><?=htmlspecialchars($r['bairro'] ?? '—')?></td>
        <td><?=htmlspecialchars($r['agente_nome'])?></td>
        <td><?=htmlspecialchars($r['viatura'] ?? '—')?></td>
        <td style="font-size:.8rem;color:var(--text-muted)"><?=htmlspecialchars(substr($r['observacoes'] ?? '', 0, 60))?></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const COLORS=['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#14b8a6'];
new Chart(document.getElementById('chartSem'),{
  type:'line',
  data:{labels:<?=json_encode($labelsSemana)?>,datasets:[{label:'Rondas',data:<?=json_encode($valuesSemana)?>,fill:true,borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.12)',tension:.3}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
new Chart(document.getElementById('chartEsc'),{
  type:'bar',
  data:{labels:<?=json_encode($labelsEscola)?>,datasets:[{label:'Rondas',data:<?=json_encode($valuesEscola)?>,backgroundColor:'#3b82f6'}]},
  options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{precision:0}}}}
});
</script>
