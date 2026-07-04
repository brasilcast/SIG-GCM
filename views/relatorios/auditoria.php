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
 */$labelsDia      = array_column($loginsPorDia, 'dia');
$valuesBomDia   = array_map('intval', array_column($loginsPorDia, 'bem_sucedidos'));
$valuesMauDia   = array_map('intval', array_column($loginsPorDia, 'falhos'));
$labelsUsuarios = array_column($topUsuarios, 'nome');
$valuesUsuarios = array_map('intval', array_column($topUsuarios, 'total'));
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-history"></i> Relatório — Auditoria de Acessos</h2>
    <div class="breadcrumb">Relatórios › Auditoria</div>
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
    <div class="val"><?=$totalLogins?></div>
    <div class="lbl">Total de Tentativas</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#22c55e"><?=$loginsBemSuced?></div>
    <div class="lbl">Logins com Sucesso</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?=$loginsFalhos?></div>
    <div class="lbl">Tentativas Falhas</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?= $totalLogins > 0 ? round($loginsBemSuced/$totalLogins*100) : 0 ?>%</div>
    <div class="lbl">Taxa de Sucesso</div>
  </div>
</div>

<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Logins por Dia</div>
    <canvas id="chartLogins" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-bar"></i> Top 10 Usuários Mais Ativos</div>
    <canvas id="chartUsuarios" height="90"></canvas>
  </div>
</div>

<div class="table-wrapper">
  <div class="table-header"><h3>Histórico de Acessos (últimos 200)</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Data/Hora</th><th>Usuário</th><th>Login tentado</th><th>Status</th><th>IP</th></tr></thead>
      <tbody>
      <?php if(empty($ultimasAcoes)): ?>
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum registro encontrado.</td></tr>
      <?php else: foreach($ultimasAcoes as $a): ?>
      <tr>
        <td><?=date('d/m/Y H:i:s', strtotime($a['tentado_em']))?></td>
        <td><?=htmlspecialchars($a['nome'] ?? '—')?></td>
        <td><code style="font-size:.8rem"><?=htmlspecialchars($a['username_tentado'] ?? '—')?></code></td>
        <td><span class="badge <?=$a['sucesso']?'badge-success':'badge-danger'?>"><?=$a['sucesso']?'✓ Sucesso':'✗ Falhou'?></span></td>
        <td><code style="font-size:.78rem"><?=htmlspecialchars($a['ip'] ?? '—')?></code></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartLogins'),{
  type:'line',
  data:{
    labels:<?=json_encode($labelsDia)?>,
    datasets:[
      {label:'Bem-sucedidos',data:<?=json_encode($valuesBomDia)?>,fill:true,borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,.12)',tension:.3},
      {label:'Falhos',data:<?=json_encode($valuesMauDia)?>,fill:true,borderColor:'#ef4444',backgroundColor:'rgba(239,68,68,.08)',tension:.3}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
new Chart(document.getElementById('chartUsuarios'),{
  type:'bar',
  data:{labels:<?=json_encode($labelsUsuarios)?>,datasets:[{label:'Logins',data:<?=json_encode($valuesUsuarios)?>,backgroundColor:'#3b82f6'}]},
  options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{precision:0}}}}
});
</script>
