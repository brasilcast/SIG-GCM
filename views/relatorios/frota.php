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
 */$labelsCusto  = array_column($custoMes, 'mes');
$valuesCusto  = array_map('floatval', array_column($custoMes, 'total'));
$labelsViatura = array_column($porViatura, 'prefixo');
$valuesConsumo = array_map('floatval', array_column($porViatura, 'consumo_medio'));
$valuesKm      = array_map('intval', array_column($porViatura, 'km_percorrido'));
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-car"></i> Relatório de Frota</h2>
    <div class="breadcrumb">Relatórios › Frota</div>
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
    <div class="val">R$ <?=number_format($totalCusto, 2, ',', '.')?></div>
    <div class="lbl">Custo Total Combustível</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?=number_format($totalLitros, 1, ',', '.')?> L</div>
    <div class="lbl">Total de Litros</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?=count($checklistsAlerta)?></div>
    <div class="lbl">Checklists c/ Alerta</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?=count($abastecimentos)?></div>
    <div class="lbl">Abastecimentos</div>
  </div>
</div>

<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Custo Mensal (R$)</div>
    <canvas id="chartCusto" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-gas-pump"></i> Consumo Médio por Viatura (km/L)</div>
    <canvas id="chartConsumo" height="90"></canvas>
  </div>
</div>

<!-- Por Viatura -->
<div class="table-wrapper" style="margin-bottom:1rem">
  <div class="table-header"><h3>Resumo por Viatura</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Viatura</th><th>Abastec.</th><th>Litros</th><th>Custo Total</th><th>Consumo Médio</th><th>KM Percorrido</th></tr></thead>
      <tbody>
      <?php if(empty($porViatura)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum dado para o período.</td></tr>
      <?php else: foreach($porViatura as $v): ?>
      <tr>
        <td><strong><?=htmlspecialchars($v['prefixo'])?></strong></td>
        <td><?=$v['abastecimentos']?></td>
        <td><?=number_format($v['total_litros'],1,',','.')?> L</td>
        <td>R$ <?=number_format($v['custo_total'],2,',','.')?></td>
        <td><?=$v['consumo_medio'] ? number_format($v['consumo_medio'],2,',','.').' km/L' : '—'?></td>
        <td><?=$v['km_percorrido'] ? number_format($v['km_percorrido'],0,',','.').' km' : '—'?></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Checklists com alerta -->
<?php if(!empty($checklistsAlerta)): ?>
<div class="table-wrapper">
  <div class="table-header"><h3><i class="fas fa-exclamation-triangle" style="color:#f59e0b"></i> Checklists com Problema / Atenção</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Data</th><th>Viatura</th><th>Agente</th><th>Status</th><th>Observações</th></tr></thead>
      <tbody>
      <?php foreach($checklistsAlerta as $c): ?>
      <tr>
        <td><?=date('d/m/Y H:i', strtotime($c['realizado_em']))?></td>
        <td><?=htmlspecialchars($c['prefixo'])?></td>
        <td><?=htmlspecialchars($c['agente'])?></td>
        <td><span class="badge <?=$c['status_geral']==='problema'?'badge-danger':'badge-warning'?>"><?=ucfirst($c['status_geral'])?></span></td>
        <td style="font-size:.8rem"><?=htmlspecialchars(substr($c['observacoes'] ?? '',0,80))?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartCusto'),{
  type:'line',
  data:{labels:<?=json_encode($labelsCusto)?>,datasets:[{label:'R$',data:<?=json_encode($valuesCusto)?>,fill:true,borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.12)',tension:.4}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
new Chart(document.getElementById('chartConsumo'),{
  type:'bar',
  data:{labels:<?=json_encode($labelsViatura)?>,datasets:[{label:'km/L',data:<?=json_encode($valuesConsumo)?>,backgroundColor:'#10b981'}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>
