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
 */// Build chart data arrays for JS
$meses12 = [];
$diasLabels = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
for ($i = 11; $i >= 0; $i--) {
    $meses12[] = date('Y-m', strtotime("-$i months"));
}
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-chart-bar"></i> Central de Relatórios</h2>
    <div class="breadcrumb">Relatórios › Dashboard</div>
  </div>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    <?php if(Auth::can('relatorios.gerenciais') || Auth::can('relatorios.basicos')): ?>
    <a href="<?=BASE_URL?>/relatorios/taloes"       class="btn btn-secondary btn-sm"><i class="fas fa-file-alt"></i> Talões</a>
    <a href="<?=BASE_URL?>/relatorios/escalas"      class="btn btn-secondary btn-sm"><i class="fas fa-calendar-alt"></i> Escalas</a>
    <a href="<?=BASE_URL?>/relatorios/frota"        class="btn btn-secondary btn-sm"><i class="fas fa-car"></i> Frota</a>
    <a href="<?=BASE_URL?>/relatorios/maria-penha"  class="btn btn-secondary btn-sm"><i class="fas fa-shield-alt"></i> M. Penha</a>
    <a href="<?=BASE_URL?>/relatorios/ronda-escolar" class="btn btn-secondary btn-sm"><i class="fas fa-graduation-cap"></i> Rondas</a>
    <?php if(Auth::can('relatorios.gerenciais')): ?>
    <a href="<?=BASE_URL?>/relatorios/corregedoria" class="btn btn-secondary btn-sm"><i class="fas fa-gavel"></i> Corregedoria</a>
    <a href="<?=BASE_URL?>/relatorios/auditoria"    class="btn btn-secondary btn-sm"><i class="fas fa-history"></i> Auditoria</a>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- KPI Cards -->
<div class="rel-kpi-grid">
  <div class="rel-kpi-card rel-kpi-blue">
    <div class="rel-kpi-icon"><i class="fas fa-file-alt"></i></div>
    <div class="rel-kpi-value"><?= number_format($kpis['taloes_mes'] ?? 0) ?></div>
    <div class="rel-kpi-label">Talões este mês</div>
  </div>
  <div class="rel-kpi-card rel-kpi-red">
    <div class="rel-kpi-icon"><i class="fas fa-exclamation-triangle"></i></div>
    <div class="rel-kpi-value"><?= $kpis['taloes_abertos'] ?? 0 ?></div>
    <div class="rel-kpi-label">Talões em aberto</div>
  </div>
  <div class="rel-kpi-card rel-kpi-green">
    <div class="rel-kpi-icon"><i class="fas fa-users"></i></div>
    <div class="rel-kpi-value"><?= $kpis['agentes_ativos'] ?? 0 ?></div>
    <div class="rel-kpi-label">GCMs ativos</div>
  </div>
  <div class="rel-kpi-card rel-kpi-orange">
    <div class="rel-kpi-icon"><i class="fas fa-car"></i></div>
    <div class="rel-kpi-value"><?= ($kpis['viaturas_disp'] ?? 0) ?><small style="font-size:.55em"> disp.</small></div>
    <div class="rel-kpi-label"><?= $kpis['viaturas_manut'] ?? 0 ?> em manutenção</div>
  </div>
  <div class="rel-kpi-card rel-kpi-purple">
    <div class="rel-kpi-icon"><i class="fas fa-shield-alt"></i></div>
    <div class="rel-kpi-value"><?= $kpis['visitas_sem'] ?? 0 ?></div>
    <div class="rel-kpi-label">Visitas M.Penha/semana</div>
  </div>
  <div class="rel-kpi-card rel-kpi-teal">
    <div class="rel-kpi-icon"><i class="fas fa-graduation-cap"></i></div>
    <div class="rel-kpi-value"><?= $kpis['rondas_sem'] ?? 0 ?></div>
    <div class="rel-kpi-label">Rondas escola/semana</div>
  </div>
  <div class="rel-kpi-card <?= ($kpis['medidas_vencendo'] ?? 0) > 0 ? 'rel-kpi-red' : 'rel-kpi-gray' ?>">
    <div class="rel-kpi-icon"><i class="fas fa-clock"></i></div>
    <div class="rel-kpi-value"><?= $kpis['medidas_vencendo'] ?? 0 ?></div>
    <div class="rel-kpi-label">Medidas vencendo/30d</div>
  </div>
  <div class="rel-kpi-card <?= ($kpis['processos_abertos'] ?? 0) > 0 ? 'rel-kpi-orange' : 'rel-kpi-gray' ?>">
    <div class="rel-kpi-icon"><i class="fas fa-gavel"></i></div>
    <div class="rel-kpi-value"><?= $kpis['processos_abertos'] ?? 0 ?></div>
    <div class="rel-kpi-label">Processos em aberto</div>
  </div>
  <div class="rel-kpi-card <?= ($kpis['trocas_pendentes'] ?? 0) > 0 ? 'rel-kpi-orange' : 'rel-kpi-gray' ?>">
    <div class="rel-kpi-icon"><i class="fas fa-exchange-alt"></i></div>
    <div class="rel-kpi-value"><?= $kpis['trocas_pendentes'] ?? 0 ?></div>
    <div class="rel-kpi-label">Trocas de plantão pend.</div>
  </div>
</div>

<!-- Gráficos -->
<div class="rel-charts-grid">

  <!-- Talões por Mês -->
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Talões por Mês (12 meses)</div>
    <canvas id="chartTaloesMes" height="80"></canvas>
  </div>

  <!-- Talões por Natureza -->
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-pie"></i> Talões por Natureza</div>
    <canvas id="chartNatureza" height="80"></canvas>
  </div>

  <!-- Talões por Dia da Semana -->
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-calendar-week"></i> Talões por Dia da Semana</div>
    <canvas id="chartDiaSemana" height="80"></canvas>
  </div>

  <!-- Visitas Maria Penha -->
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-shield-alt"></i> Visitas Maria da Penha / Mês</div>
    <canvas id="chartVisitas" height="80"></canvas>
  </div>

  <!-- Consumo frota -->
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-gas-pump"></i> Consumo Médio Frota (km/L)</div>
    <canvas id="chartConsumo" height="80"></canvas>
  </div>

  <!-- Links rápidos -->
  <div class="table-wrapper rel-chart-box" style="display:flex;flex-direction:column;gap:.75rem;justify-content:center;align-items:center;min-height:200px">
    <div class="rel-chart-title" style="margin-bottom:.5rem"><i class="fas fa-link"></i> Relatórios Detalhados</div>
    <a href="<?=BASE_URL?>/relatorios/taloes"        class="btn btn-primary btn-block" style="max-width:280px"><i class="fas fa-file-alt"></i> Relatório de Talões</a>
    <a href="<?=BASE_URL?>/relatorios/escalas"       class="btn btn-primary btn-block" style="max-width:280px"><i class="fas fa-calendar-alt"></i> Relatório de Escalas</a>
    <a href="<?=BASE_URL?>/relatorios/frota"         class="btn btn-primary btn-block" style="max-width:280px"><i class="fas fa-car"></i> Relatório de Frota</a>
    <a href="<?=BASE_URL?>/relatorios/maria-penha"   class="btn btn-primary btn-block" style="max-width:280px"><i class="fas fa-shield-alt"></i> Maria da Penha</a>
    <a href="<?=BASE_URL?>/relatorios/ronda-escolar" class="btn btn-primary btn-block" style="max-width:280px"><i class="fas fa-graduation-cap"></i> Ronda Escolar</a>
    <?php if(Auth::can('relatorios.gerenciais')): ?>
    <a href="<?=BASE_URL?>/relatorios/corregedoria"  class="btn btn-secondary btn-block" style="max-width:280px"><i class="fas fa-gavel"></i> Corregedoria</a>
    <a href="<?=BASE_URL?>/relatorios/auditoria"     class="btn btn-secondary btn-block" style="max-width:280px"><i class="fas fa-history"></i> Auditoria de Usuários</a>
    <?php endif; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const MESES_12   = <?=json_encode($meses12)?>;
const DIAS_LABELS= ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];

const COLORS = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#14b8a6','#f97316','#6366f1'];

// Gráfico 1: Talões por mês
(function(){
  const rawData = <?=json_encode($taloesMes ?? [])?>;
  const labels  = MESES_12;
  const values  = labels.map(m => rawData[m] || 0);
  new Chart(document.getElementById('chartTaloesMes'), {
    type:'line',
    data:{labels,datasets:[{label:'Talões',data:values,fill:true,borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,.15)',tension:.4,pointRadius:4}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
  });
})();

// Gráfico 2: Por natureza (donut)
(function(){
  const raw = <?=json_encode($taloesPorNatureza ?? [])?>;
  const labels = Object.keys(raw);
  const values = Object.values(raw);
  new Chart(document.getElementById('chartNatureza'), {
    type:'doughnut',
    data:{labels,datasets:[{data:values,backgroundColor:COLORS}]},
    options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:11}}}}}
  });
})();

// Gráfico 3: Dia da semana (barra)
(function(){
  const raw = <?=json_encode($taloesDiaSemana ?? [0,0,0,0,0,0,0])?>;
  new Chart(document.getElementById('chartDiaSemana'), {
    type:'bar',
    data:{labels:DIAS_LABELS,datasets:[{label:'Talões',data:raw,backgroundColor:COLORS}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
  });
})();

// Gráfico 4: Visitas MP por mês
(function(){
  const raw = <?=json_encode($visitasMes ?? [])?>;
  const labels = MESES_12;
  const values = labels.map(m => raw[m] || 0);
  new Chart(document.getElementById('chartVisitas'), {
    type:'line',
    data:{labels,datasets:[{label:'Visitas',data:values,fill:true,borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.15)',tension:.4,pointRadius:4}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
  });
})();

// Gráfico 5: Consumo KM/L
(function(){
  const raw = <?=json_encode($consumoMes ?? [])?>;
  const labels = Object.keys(raw).length ? Object.keys(raw) : MESES_12;
  const values = Object.keys(raw).length ? Object.values(raw) : MESES_12.map(()=>0);
  new Chart(document.getElementById('chartConsumo'), {
    type:'bar',
    data:{labels,datasets:[{label:'km/L',data:values,backgroundColor:'#f59e0b'}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
})();
</script>
