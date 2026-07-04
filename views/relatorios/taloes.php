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
 */$labelsNatureza = array_column($porNatureza, 'nome');
$valuesNatureza = array_map('intval', array_column($porNatureza, 'total'));
$labelsDia      = array_column($porDia, 'dia');
$valuesDia      = array_map('intval', array_column($porDia, 'total'));
?>
<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/relatorios.css?v=<?=time()?>">
<div class="page-header">
  <div>
    <h2><i class="fas fa-file-alt"></i> Relatório de Talões</h2>
    <div class="breadcrumb">Relatórios › Talões</div>
  </div>
  <div style="display:flex;gap:.5rem">
    <a href="<?=BASE_URL?>/relatorios" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
    <a href="?<?=http_build_query(array_merge($_GET, ['export'=>1]))?>" class="btn btn-success"><i class="fas fa-download"></i> Exportar CSV</a>
  </div>
</div>

<!-- Filtros -->
<form method="GET" action="">
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
    <label class="form-label">Status</label>
    <select name="status" class="form-control">
      <option value="">Todos</option>
      <option value="aberto"  <?=$status==='aberto' ?'selected':''?>>Aberto</option>
      <option value="fechado" <?=$status==='fechado'?'selected':''?>>Fechado</option>
    </select>
  </div>
  <div class="form-group">
    <label class="form-label">Natureza</label>
    <select name="natureza" class="form-control">
      <option value="">Todas</option>
      <?php foreach($naturezas as $n): ?>
      <option value="<?=$n['id']?>" <?=$natId==$n['id']?'selected':''?>><?=htmlspecialchars($n['nome'])?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label class="form-label">Agente</label>
    <select name="agente" class="form-control">
      <option value="">Todos</option>
      <?php foreach($agentes as $a): ?>
      <option value="<?=$a['id']?>" <?=$agente==$a['id']?'selected':''?>><?=htmlspecialchars($a['nome'])?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label class="form-label">Bairro</label>
    <input type="text" name="bairro" class="form-control" value="<?=htmlspecialchars($bairro)?>" placeholder="Digite o bairro...">
  </div>
  <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
</div>
</form>

<!-- Totalizadores -->
<div class="rel-summary-row">
  <div class="rel-summary-item">
    <div class="val"><?= number_format($totais['total'] ?? 0) ?></div>
    <div class="lbl">Total no Período</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#22c55e"><?= $totais['fechados'] ?? 0 ?></div>
    <div class="lbl">Fechados</div>
  </div>
  <div class="rel-summary-item">
    <div class="val" style="color:#ef4444"><?= $totais['abertos'] ?? 0 ?></div>
    <div class="lbl">Em Aberto</div>
  </div>
  <div class="rel-summary-item">
    <div class="val"><?= $totais['media_horas'] ?? '—' ?><small style="font-size:.5em">h</small></div>
    <div class="lbl">Tempo Médio Atend.</div>
  </div>
</div>

<!-- Gráficos -->
<div class="rel-charts-grid">
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-pie"></i> Por Natureza</div>
    <canvas id="chartNat" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-chart-line"></i> Por Dia no Período</div>
    <canvas id="chartDia" height="90"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-map-marker-alt"></i> Top 10 Bairros</div>
    <canvas id="chartBairro" height="100"></canvas>
  </div>
  <div class="table-wrapper rel-chart-box">
    <div class="rel-chart-title"><i class="fas fa-user-shield"></i> Top 5 Agentes</div>
    <ul style="list-style:none;padding:0;margin:0">
      <?php foreach($topAgentes as $i=>$a): ?>
      <li style="display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid var(--border)">
        <span><?=$i+1?>.  <?=htmlspecialchars($a['nome'])?></span>
        <span class="badge badge-primary"><?=$a['total']?> talões</span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Tabela -->
<div class="table-wrapper">
  <div class="table-header"><h3>Listagem (máx. 500 registros)</h3></div>
  <div class="table-responsive">
    <table class="rel-table">
      <thead><tr><th>Nº</th><th>Data/Hora</th><th>Natureza</th><th>Bairro</th><th>Status</th><th>Agente</th><th>Viatura</th><th></th></tr></thead>
      <tbody>
      <?php if(empty($taloes)): ?>
        <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum talão encontrado para o período.</td></tr>
      <?php else: foreach($taloes as $t): ?>
      <tr>
        <td><strong><?=htmlspecialchars($t['numero'])?></strong></td>
        <td><?=date('d/m/Y H:i', strtotime($t['data_hora_abertura']))?></td>
        <td><?=htmlspecialchars($t['natureza_nome'] ?? '—')?></td>
        <td><?=htmlspecialchars($t['bairro'] ?? '—')?></td>
        <td><span class="badge <?=$t['status']==='fechado'?'badge-success':'badge-warning'?>"><?=ucfirst($t['status'])?></span></td>
        <td><?=htmlspecialchars($t['agente_nome'] ?? '—')?></td>
        <td><?=htmlspecialchars($t['viatura'] ?? '—')?></td>
        <td><a href="<?=BASE_URL?>/taloes/<?=$t['id']?>" class="btn btn-secondary btn-sm">Ver</a></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const COLORS=['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#14b8a6','#f97316','#6366f1'];

new Chart(document.getElementById('chartNat'),{
  type:'doughnut',
  data:{labels:<?=json_encode($labelsNatureza)?>,datasets:[{data:<?=json_encode($valuesNatureza)?>,backgroundColor:COLORS}]},
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:11}}}}}
});

new Chart(document.getElementById('chartDia'),{
  type:'line',
  data:{labels:<?=json_encode($labelsDia)?>,datasets:[{label:'Talões',data:<?=json_encode($valuesDia)?>,fill:true,borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,.12)',tension:.3}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});

new Chart(document.getElementById('chartBairro'),{
  type:'bar',
  data:{
    labels:<?=json_encode(array_column($porBairro,'bairro'))?>,
    datasets:[{label:'Ocorrências',data:<?=json_encode(array_map('intval',array_column($porBairro,'total')))?>,backgroundColor:'#8b5cf6'}]
  },
  options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{precision:0}}}}
});
</script>
