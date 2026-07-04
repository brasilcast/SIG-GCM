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
 */$pageTitle='Comunicados'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-bullhorn"></i> Comunicados</h2><div class="breadcrumb">Comunicados</div></div>
  <?php if(Auth::can('comunicados.criar')): ?>
  <a href="<?=BASE_URL?>/comunicados/novo" class="btn btn-primary">+ Novo Comunicado</a>
  <?php endif; ?>
</div>
<?php foreach($comunicados as $c):
  $tipoIcons=['urgente'=>['<i class="fa-solid fa-circle-exclamation" style="color:var(--danger)"></i>','badge-danger','comunicado-urgente'],'atencao'=>['<i class="fa-solid fa-triangle-exclamation"></i>','badge-warning',''],'informacao'=>['<i class="fa-solid fa-circle-info"></i>','badge-info','']];
  [$ic,$ib,$cls]=$tipoIcons[$c['tipo']]??['<i class="fa-solid fa-bullhorn"></i>','badge-secondary',''];
?>
<div class="table-wrapper <?=$cls?>" style="margin-bottom:.75rem;padding:1rem 1.25rem">
  <div style="display:flex;align-items:flex-start;gap:.85rem">
    <div style="font-size:1.3rem;flex-shrink:0"><?=$ic?></div>
    <div style="flex:1">
      <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.35rem">
        <strong><?=htmlspecialchars($c['titulo'])?></strong>
        <span class="badge <?=$ib?>"><?=ucfirst($c['tipo'])?></span>
        <?php if($c['lido']): ?><span class="badge badge-success" style="font-size:.7rem"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Lido</span><?php endif; ?>
      </div>
      <p style="font-size:.875rem;color:var(--text-muted);line-height:1.6"><?=nl2br(htmlspecialchars($c['conteudo']))?></p>
      <div style="font-size:.75rem;color:var(--text-muted);margin-top:.5rem">
        <i class="fa-solid fa-clipboard-list"></i> <?=htmlspecialchars($c['criado_por_nome'])?> — <?=date('d/m/Y',strtotime($c['criado_em']))?>
        <?php if($c['valido_ate']): ?> · válido até <?=date('d/m/Y',strtotime($c['valido_ate']))?><?php endif; ?>
      </div>
    </div>
    <?php if($c['requer_confirmacao'] && !$c['lido']): ?>
    <form method="POST" action="<?=BASE_URL?>/comunicados/<?=$c['id']?>/confirmar" style="flex-shrink:0">
      <?=Auth::csrfField()?>
      <button class="btn btn-primary btn-sm"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Confirmar Leitura</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
<?php if(empty($comunicados)): ?>
<div class="table-wrapper" style="padding:2rem;text-align:center;color:var(--text-muted)">
  Nenhum comunicado para você no momento.
</div>
<?php endif; ?>
