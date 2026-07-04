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
 */$pageTitle = 'Viaturas'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-car"></i> Gestão de Viaturas</h2><div class="breadcrumb">Frota › Viaturas</div></div>
  <?php if(Auth::can('frota.cadastrar_viaturas')): ?>
  <a href="<?=BASE_URL?>/frota/viaturas/nova" class="btn btn-primary">+ Nova Viatura</a>
  <?php endif; ?>
</div>

<div class="cards-grid" style="margin-bottom:1.5rem">
  <?php
  $totais=['disponivel'=>['n'=>0,'icon'=>'<i class="fa-solid fa-circle-check"></i>','cor'=>'green','l'=>'Disponíveis'],
            'em_servico'=>['n'=>0,'icon'=>'<i class="fa-solid fa-car-side"></i>','cor'=>'blue','l'=>'Em Serviço'],
            'manutencao'=>['n'=>0,'icon'=>'<i class="fa-solid fa-wrench"></i>','cor'=>'red','l'=>'Manutenção'],
            'inativa'   =>['n'=>0,'icon'=>'<i class="fa-solid fa-ban"></i>','cor'=>'secondary','l'=>'Inativas']];
  foreach($viaturas as $v) { if(isset($totais[$v['status']])) $totais[$v['status']]['n']++; }
  foreach($totais as $st=>$t): ?>
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label"><?=$t['l']?></span><div class="stat-card-icon icon-<?=$t['cor']?>"><?=$t['icon']?></div></div><div class="stat-card-value"><?=$t['n']?></div></div>
  <?php endforeach; ?>
</div>

<div class="table-wrapper">
  <div class="table-responsive">
    <table>
      <thead><tr><th>Prefixo</th><th>Placa</th><th>Modelo/Marca</th><th>Ano</th><th>KM Atual</th><th>Status</th><th>Ação</th></tr></thead>
      <tbody>
      <?php if(empty($viaturas)): ?>
        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhuma viatura cadastrada.</td></tr>
      <?php else: foreach($viaturas as $v): ?>
        <?php $sb=['disponivel'=>'badge-success','em_servico'=>'badge-info','manutencao'=>'badge-danger','inativa'=>'badge-secondary'][$v['status']]??'badge-secondary'; ?>
        <tr>
          <td><strong style="font-size:1rem"><?=htmlspecialchars($v['prefixo'])?></strong></td>
          <td><code><?=htmlspecialchars($v['placa']?:'—')?></code></td>
          <td><?=htmlspecialchars($v['modelo'].' '.$v['marca'])?></td>
          <td><?=$v['ano']?:'—'?></td>
          <td><?=$v['km_atual']?number_format($v['km_atual'],0,'.',',').' km':'—'?></td>
          <td><span class="badge <?=$sb?>"><?=ucfirst(str_replace('_',' ',$v['status']))?></span></td>
          <td>
            <div style="display:flex;gap:.35rem">
              <a href="<?=BASE_URL?>/frota/viaturas/<?=$v['id']?>" class="btn btn-secondary btn-sm" title="Ver"><i class="fa-solid fa-eye"></i></a>
              <?php if(Auth::can('frota.cadastrar_viaturas')): ?>
              <a href="<?=BASE_URL?>/frota/viaturas/<?=$v['id']?>/editar" class="btn btn-primary btn-sm" title="Editar"><i class="fa-solid fa-pencil"></i></a>
              <form method="POST" action="<?=BASE_URL?>/frota/viaturas/<?=$v['id']?>/excluir" style="display:inline" onsubmit="return confirm('Tem certeza que deseja excluir esta viatura?')">
                <?=Auth::csrfField()?>
                <button type="submit" class="btn btn-danger btn-sm" title="Excluir"><i class="fa-solid fa-trash"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
