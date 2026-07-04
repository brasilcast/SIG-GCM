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
 */$labelsVisitas = array_column($visistasMes, 'mes');
$valuesVisitas = array_map('intval', array_column($visistasMes, 'total'));
$labelsBairro  = array_column($porBairro, 'bairro');
$valuesBairro  = array_map('intval', array_column($porBairro, 'total'));
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-shield-alt"></i> Relatório — Maria da Penha</h2>
    <div class="breadcrumb">Relatórios › Maria da Penha</div>
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
    <div class="val"><?=$totalVitimas?></div>
    <div class="lbl">Vítimas Ativas</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#f59e0b"><?=$seVisita7Dias?></div>
    <div class="lbl">Sem visita há +7 dias</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?=$medidasVencidas?></div>
    <div class="lbl">Medidas Vencidas</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#f59e0b"><?=$medidasVencendo?></div>
    <div class="lbl">Medidas (vencem/30d)</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?=count($visitas)?></div>
    <div class="lbl">Visitas no Período</div>
  </div>
</div>

<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Visitas por Mês</div>
    <canvas id="chartVis" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-pie"></i> Vítimas por Bairro</div>
    <canvas id="chartBairro" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-user-shield"></i> Top 5 Agentes (visitas)</div>
    <ul style="list-style:none;padding:0;margin:0">
      <?php foreach($topAgentes as $i=>$a): ?>
      <li style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border)">
        <span><?=$i+1?>. <?=htmlspecialchars($a['nome'])?></span>
        <span class="badge badge-success"><?=$a['total']?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Vítimas -->
<div class="table-wrapper" style="margin-bottom:1rem">
  <div class="table-header"><h3>Vítimas Cadastradas</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Nome</th><th>Bairro</th><th>Prioridade</th><th>Visitas (período)</th><th>Última Visita</th><th>Medidas Ativas</th><th></th></tr></thead>
      <tbody>
      <?php if(empty($vitimas)): ?>
        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhuma vítima encontrada.</td></tr>
      <?php else: foreach($vitimas as $v): ?>
      <tr>
        <td><?=htmlspecialchars($v['nome'])?></td>
        <td><?=htmlspecialchars($v['bairro'] ?? '—')?></td>
        <td>
          <?php $pLabel=['1'=>'Alta','2'=>'Média','3'=>'Baixa'][$v['prioridade']] ?? $v['prioridade']; ?>
          <?php $pBadge=['1'=>'badge-danger','2'=>'badge-warning','3'=>'badge-secondary'][$v['prioridade']] ?? 'badge-secondary'; ?>
          <span class="badge <?=$pBadge?>"><?=$pLabel?></span>
        </td>
        <td><?=$v['visitas_periodo'] ?? 0?></td>
        <td><?=$v['ultima_visita'] ? date('d/m/Y',strtotime($v['ultima_visita'])) : '<span style="color:#ef4444">Nunca</span>'?></td>
        <td><?php if($v['medidas_ativas']>0): ?><span class="badge badge-warning"><?=$v['medidas_ativas']?></span><?php else: ?>—<?php endif; ?></td>
        <td><a href="<?=BASE_URL?>/maria-penha/<?=$v['id']?>" class="btn btn-secondary btn-sm">Ver</a></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const COLORS=['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#14b8a6'];
new Chart(document.getElementById('chartVis'),{
  type:'line',
  data:{labels:<?=json_encode($labelsVisitas)?>,datasets:[{label:'Visitas',data:<?=json_encode($valuesVisitas)?>,fill:true,borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.13)',tension:.4}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
new Chart(document.getElementById('chartBairro'),{
  type:'doughnut',
  data:{labels:<?=json_encode($labelsBairro)?>,datasets:[{data:<?=json_encode($valuesBairro)?>,backgroundColor:COLORS}]},
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:11}}}}}
});
</script>
