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
 */$labelsEquipe  = array_column($porEquipe, 'turno');
$valuesEquipe  = array_map('intval', array_column($porEquipe, 'total'));
$labelsDia     = array_column($porDia, 'data');
$valuesDia     = array_map('intval', array_column($porDia, 'total'));
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-calendar-alt"></i> Relatório de Escalas</h2>
    <div class="breadcrumb">Relatórios › Escalas</div>
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
  <div class="form-group">
    <label class="form-label">Equipe / Turno</label>
    <select name="equipe" class="form-control">
      <option value="">Todos</option>
      <option value="alfa"           <?=$equipe==='alfa'          ?'selected':''?>>ALFA</option>
      <option value="bravo"          <?=$equipe==='bravo'         ?'selected':''?>>BRAVO</option>
      <option value="charlie"        <?=$equipe==='charlie'       ?'selected':''?>>CHARLIE</option>
      <option value="delta"          <?=$equipe==='delta'         ?'selected':''?>>DELTA</option>
      <option value="administrativo" <?=$equipe==='administrativo'?'selected':''?>>ADM</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
</div>
</form>

<!-- Trocas de Plantão -->
<div class="rel-summary-row">
  <div class="rel-summary-item">
    <div class="val"><?= count($escalas) ?></div>
    <div class="lbl">Plantões no Período</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#f59e0b"><?= $trocas['pendentes'] ?? 0 ?></div>
    <div class="lbl">Trocas Pendentes</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#22c55e"><?= $trocas['aprovadas'] ?? 0 ?></div>
    <div class="lbl">Trocas Aprovadas</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?= $trocas['negadas'] ?? 0 ?></div>
    <div class="lbl">Trocas Negadas</div>
  </div>
</div>

<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-pie"></i> Plantões por Equipe</div>
    <canvas id="chartEquipe" height="100"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Plantões por Dia</div>
    <canvas id="chartDia" height="100"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-user-shield"></i> Top 10 Agentes</div>
    <ul style="list-style:none;padding:0;margin:0">
      <?php foreach($topAgentes as $i=>$a): ?>
      <li style="display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid var(--border)">
        <span><?=$i+1?>. <?=htmlspecialchars($a['nome'])?></span>
        <span class="badge badge-primary"><?=$a['total']?> plant.</span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<div class="table-wrapper">
  <div class="table-header"><h3>Plantões no Período</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Data</th><th>Agente</th><th>Equipe/Turno</th><th>Entrada</th><th>Saída</th></tr></thead>
      <tbody>
      <?php if(empty($escalas)): ?>
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum plantão encontrado.</td></tr>
      <?php else: foreach($escalas as $e): ?>
      <tr>
        <td><?=date('d/m/Y', strtotime($e['data']))?></td>
        <td><?=htmlspecialchars($e['agente_nome'])?></td>
        <td><span class="badge badge-secondary"><?=strtoupper($e['turno'])?></span></td>
        <td><?=htmlspecialchars($e['entrada'] ?? '—')?></td>
        <td><?=htmlspecialchars($e['saida'] ?? '—')?></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const COLORS=['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#14b8a6'];
new Chart(document.getElementById('chartEquipe'),{
  type:'doughnut',
  data:{labels:<?=json_encode(array_map('strtoupper',$labelsEquipe))?>,datasets:[{data:<?=json_encode($valuesEquipe)?>,backgroundColor:COLORS}]},
  options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});
new Chart(document.getElementById('chartDia'),{
  type:'bar',
  data:{labels:<?=json_encode($labelsDia)?>,datasets:[{label:'Plantões',data:<?=json_encode($valuesDia)?>,backgroundColor:'#3b82f6'}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
</script>
