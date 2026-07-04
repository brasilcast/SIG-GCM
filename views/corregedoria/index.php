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
 */$pageTitle='Corregedoria'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-scale-balanced"></i> Corregedoria</h2><div class="breadcrumb">Administrativo › Corregedoria</div></div>
  <?php if(Auth::can('corregedoria.criar')): ?>
  <a href="<?=BASE_URL?>/corregedoria/novo" class="btn btn-primary">+ Novo Processo</a>
  <?php endif; ?>
</div>
<!-- Filtros rápidos -->
<div style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap">
  <?php foreach([''=>'Todos','aberto'=>'Abertos','em_andamento'=>'Em Andamento','concluido'=>'Concluídos','arquivado'=>'Arquivados'] as $v=>$l): ?>
  <a href="<?=BASE_URL?>/corregedoria<?=$v?"?status=$v":''?>" class="btn <?=($status??'')===$v?'btn-primary':'btn-secondary'?> btn-sm"><?=$l?></a>
  <?php endforeach; ?>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Número</th><th>Tipo</th><th>Investigado</th><th>Abertura</th><th>Prazo Legal</th><th>Status</th><th>Ação</th></tr></thead>
    <tbody>
    <?php if(empty($processos)): ?><tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum processo.</td></tr>
    <?php else: foreach($processos as $p):
      $sb=['aberto'=>'badge-danger','em_andamento'=>'badge-warning','concluido'=>'badge-success','arquivado'=>'badge-secondary'][$p['status']]??'badge-secondary';
      $prazoBadge='badge-success';
      if($p['prazo_legal']){$diff=(strtotime($p['prazo_legal'])-time())/86400;$prazoBadge=$diff<=0?'badge-danger':($diff<=10?'badge-warning':'badge-success');}
    ?>
      <tr>
        <td><strong style="font-family:monospace"><?=htmlspecialchars($p['numero'])?></strong></td>
        <td><span class="badge badge-secondary"><?=ucfirst(str_replace('_',' ',$p['tipo']))?></span></td>
        <td><?=htmlspecialchars($p['investigado_nome']??'—')?></td>
        <td><?=date('d/m/Y',strtotime($p['data_abertura']))?></td>
        <td><?=$p['prazo_legal']?'<span class="badge '.$prazoBadge.'">'.date('d/m/Y',strtotime($p['prazo_legal'])).'</span>':'—'?></td>
        <td><span class="badge <?=$sb?>"><?=ucfirst($p['status'])?></span></td>
        <td><a href="<?=BASE_URL?>/corregedoria/<?=$p['id']?>" class="btn btn-secondary btn-sm">Ver</a></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
