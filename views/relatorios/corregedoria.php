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
 */$labelsTipo = array_column($porTipo, 'tipo');
$valuesTipo = array_map('intval', array_column($porTipo, 'total'));
$labelsMes  = array_column($porMes, 'mes');
$valuesMes  = array_map('intval', array_column($porMes, 'total'));
$totalProc  = array_sum($valuesMes);
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-gavel"></i> Relatório — Corregedoria</h2>
    <div class="breadcrumb">Relatórios › Corregedoria</div>
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
  <?php
  $statusMap = ['aberto'=>0,'em_andamento'=>0,'concluido'=>0,'arquivado'=>0];
  foreach($porStatus as $s) $statusMap[$s['status']] = (int)$s['total'];
  ?>
  <div class="rel-summary-item">
    <div class="val"><?=$totalProc?></div>
    <div class="lbl">Total no Período</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?=$statusMap['aberto']?></div>
    <div class="lbl">Em Aberto</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#f59e0b"><?=$statusMap['em_andamento']?></div>
    <div class="lbl">Em Andamento</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#22c55e"><?=$statusMap['concluido']?></div>
    <div class="lbl">Concluídos</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?=$prazosVencidos?></div>
    <div class="lbl">Com Prazo Vencido</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?= $tempMedio ? $tempMedio.'d' : '—' ?></div>
    <div class="lbl">Tempo Médio Resolução</div>
  </div>
</div>

<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-pie"></i> Por Tipo</div>
    <canvas id="chartTipo" height="100"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-bar"></i> Processos por Mês</div>
    <canvas id="chartMes" height="100"></canvas>
  </div>
</div>

<div class="table-wrapper">
  <div class="table-header"><h3>Processos no Período</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Número</th><th>Tipo</th><th>Investigado</th><th>Abertura</th><th>Prazo Legal</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php if(empty($processos)): ?>
        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum processo encontrado.</td></tr>
      <?php else: foreach($processos as $p): ?>
      <tr>
        <td><strong><?=htmlspecialchars($p['numero'])?></strong></td>
        <td><?=htmlspecialchars($p['tipo'])?></td>
        <td><?=htmlspecialchars($p['investigado_nome'] ?? '—')?></td>
        <td><?=date('d/m/Y', strtotime($p['data_abertura']))?></td>
        <td><?=$p['prazo_legal'] ? date('d/m/Y',strtotime($p['prazo_legal'])) : '—'?></td>
        <td>
          <?php $sBadge=['aberto'=>'badge-danger','em_andamento'=>'badge-warning','concluido'=>'badge-success','arquivado'=>'badge-secondary'][$p['status']] ?? 'badge-secondary'; ?>
          <span class="badge <?=$sBadge?>"><?=str_replace('_',' ',ucfirst($p['status']))?></span>
        </td>
        <td><a href="<?=BASE_URL?>/corregedoria/<?=$p['id']?>" class="btn btn-secondary btn-sm">Ver</a></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const COLORS=['#ef4444','#f59e0b','#22c55e','#6366f1','#8b5cf6','#06b6d4','#ec4899'];
new Chart(document.getElementById('chartTipo'),{
  type:'doughnut',
  data:{labels:<?=json_encode($labelsTipo)?>,datasets:[{data:<?=json_encode($valuesTipo)?>,backgroundColor:COLORS}]},
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:10}}}}}
});
new Chart(document.getElementById('chartMes'),{
  type:'bar',
  data:{labels:<?=json_encode($labelsMes)?>,datasets:[{label:'Processos',data:<?=json_encode($valuesMes)?>,backgroundColor:'#8b5cf6'}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
</script>
