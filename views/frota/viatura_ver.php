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
 */$pageTitle='Viatura '.$v['prefixo'];
$stBadge=['disponivel'=>'badge-success','em_servico'=>'badge-info','manutencao'=>'badge-danger','inativa'=>'badge-secondary'];
?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-car"></i> <?=htmlspecialchars($v['prefixo'])?> <small style="font-size:.7em;color:var(--text-muted)"><?=htmlspecialchars($v['placa']?:'Sem placa')?></small></h2><div class="breadcrumb">Frota › Viaturas</div></div>
  <a href="<?=BASE_URL?>/frota/viaturas" class="btn btn-secondary">← Voltar</a>
</div>

<div class="cards-grid" style="margin-bottom:1rem">
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label">Status</span></div><div><span class="badge <?=$stBadge[$v['status']]??"badge-secondary"?>" style="font-size:1rem;padding:.4rem .9rem"><?=ucfirst(str_replace('_',' ',$v['status']))?></span></div></div>
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label">KM Atual</span><div class="stat-card-icon icon-blue"><i class="fa-solid fa-location-dot"></i></div></div><div class="stat-card-value"><?=number_format($v['km_atual']??0,0,'.',',')?></div><div class="stat-card-sub">quilômetros</div></div>
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label">Modelo</span></div><div style="font-size:1rem;font-weight:700"><?=htmlspecialchars($v['marca'].' '.$v['modelo'])?></div><div class="stat-card-sub"><?=$v['ano']?:'—'?> · <?=htmlspecialchars($v['cor']?:'—')?></div></div>
</div>

<!-- Últimos Checklists -->
<div class="table-wrapper" style="margin-bottom:1rem">
  <div class="table-header">
    <h3><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Últimos Checklists</h3>
    <a href="<?=BASE_URL?>/frota/checklists/novo" class="btn btn-primary btn-sm">+ Novo</a>
  </div>
  <div class="table-responsive"><table>
    <thead><tr><th>Data</th><th>Tipo</th><th>Agente</th><th>KM</th><th>Pendências</th></tr></thead>
    <tbody>
    <?php if(empty($checklists)): ?><tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--text-muted)">Nenhum checklist.</td></tr>
    <?php else: foreach($checklists as $c):
      $campos=['pneus','luzes','lataria','vidros','giroflex','sirene','estepe','extintor','documentos','triangulo','oleo','agua','combustivel','colete','algemas','radio'];
      $problemas=array_filter($campos,fn($i)=>$c[$i]==='problema');
      $atencao  =array_filter($campos,fn($i)=>$c[$i]==='atencao');
    ?>
      <tr>
        <td><?=date('d/m/Y H:i',strtotime($c['realizado_em']))?></td>
        <td><span class="badge badge-info"><?=ucfirst($c['tipo'])?></span></td>
        <td><?=htmlspecialchars($c['agente'])?></td>
        <td><?=$c['km']?number_format($c['km'],0,'.',',').' km':'—'?></td>
        <td>
          <?php if($problemas): ?><span class="badge badge-danger"><i class="fa-solid fa-circle-xmark" style="color:var(--danger)"></i> <?=count($problemas)?> problema(s)</span><?php endif; ?>
          <?php if($atencao):   ?><span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> <?=count($atencao)?> atenção</span><?php endif; ?>
          <?php if(!$problemas&&!$atencao): ?><span class="badge badge-success"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> OK</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>

<!-- Últimos Abastecimentos -->
<div class="table-wrapper">
  <div class="table-header">
    <h3><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i> Últimos Abastecimentos</h3>
    <a href="<?=BASE_URL?>/frota/abastecimentos/novo" class="btn btn-primary btn-sm">+ Novo</a>
  </div>
  <div class="table-responsive"><table>
    <thead><tr><th>Data</th><th>Litros</th><th>Valor</th><th>KM</th><th>Consumo</th></tr></thead>
    <tbody>
    <?php if(empty($abastec)): ?><tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--text-muted)">Nenhum abastecimento.</td></tr>
    <?php else: foreach($abastec as $a): ?>
      <tr>
        <td><?=date('d/m/Y',strtotime($a['data_abastecimento']))?></td>
        <td><?=number_format($a['litros']??0,1,'.',',')?> L</td>
        <td>R$ <?=number_format($a['valor_total']??0,2,',','.')?></td>
        <td><?=$a['km_atual']?number_format($a['km_atual'],0,'.',',').' km':'—'?></td>
        <td><?=$a['consumo_calculado']?$a['consumo_calculado'].' km/L':'—'?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
