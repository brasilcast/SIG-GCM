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
 */$pageTitle='Processo '.$p['numero']; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-scale-balanced"></i> Processo <?=htmlspecialchars($p['numero'])?></h2><div class="breadcrumb">Corregedoria › Detalhes</div></div>
  <a href="<?=BASE_URL?>/corregedoria" class="btn btn-secondary">← Voltar</a>
</div>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;margin-bottom:1rem">
  <div class="table-wrapper" style="padding:1.5rem">
    <?php $sb=['aberto'=>'badge-danger','em_andamento'=>'badge-warning','concluido'=>'badge-success','arquivado'=>'badge-secondary'][$p['status']]??'badge-secondary'; ?>
    <div style="display:flex;gap:.5rem;align-items:center;margin-bottom:1rem">
      <span class="badge <?=$sb?>"><?=ucfirst($p['status'])?></span>
      <span class="badge badge-secondary"><?=ucfirst(str_replace('_',' ',$p['tipo']))?></span>
    </div>
    <table width="100%" style="font-size:.875rem">
      <?php foreach([['Investigado',$p['investigado_nome']??'—'],['Data Abertura',date('d/m/Y',strtotime($p['data_abertura']))],['Prazo Legal',$p['prazo_legal']?date('d/m/Y',strtotime($p['prazo_legal'])):'—']] as [$l,$v]): ?>
      <tr style="border-bottom:1px solid var(--border)"><td style="padding:.5rem 0;color:var(--text-muted);width:40%"><?=$l?></td><td style="padding:.5rem 0;font-weight:600"><?=htmlspecialchars($v)?></td></tr>
      <?php endforeach; ?>
    </table>
    <div style="margin-top:1rem">
      <h4 style="font-size:.85rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:.5rem">Descrição do Fato</h4>
      <p style="font-size:.875rem;line-height:1.7;white-space:pre-wrap;background:var(--surface-2);padding:.75rem;border-radius:var(--radius)"><?=htmlspecialchars($p['fato_descricao']??'—')?></p>
    </div>
    <?php if($p['conclusao']): ?>
    <div style="margin-top:1rem">
      <h4 style="font-size:.85rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:.5rem">Conclusão</h4>
      <p style="font-size:.875rem;white-space:pre-wrap;background:var(--surface-2);padding:.75rem;border-radius:var(--radius)"><?=htmlspecialchars($p['conclusao'])?></p>
    </div>
    <?php endif; ?>
  </div>
  <!-- Ações rápidas -->
  <div class="table-wrapper" style="padding:1.25rem">
    <h3 style="font-size:.9rem;margin-bottom:.85rem">Status do Processo</h3>
    <?php foreach(['aberto'=>'<i class="fa-solid fa-circle"></i> Reabrir','em_andamento'=>'<i class="fa-solid fa-circle"></i> Em Andamento','concluido'=>'<i class="fa-solid fa-circle"></i> Concluir','arquivado'=>'<i class="fa-solid fa-folder"></i> Arquivar'] as $s=>$l): if($s===$p['status']) continue; ?>
    <form method="POST" action="<?=BASE_URL?>/corregedoria/<?=$p['id']?>/movimentar" style="margin-bottom:.35rem">
      <?=Auth::csrfField()?>
      <input type="hidden" name="novo_status" value="<?=$s?>">
      <input type="hidden" name="descricao" value="Status alterado para: <?=$l?>">
      <button class="btn btn-secondary btn-sm btn-block"><?=$l?></button>
    </form>
    <?php endforeach; ?>
  </div>
</div>
<!-- Movimentações -->
<div class="table-wrapper">
  <div class="table-header"><h3><i class="fa-solid fa-file-lines"></i> Movimentações</h3></div>
  <?php foreach($movs as $m): ?>
  <div style="padding:.85rem 1.25rem;border-bottom:1px solid var(--border)">
    <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.3rem"><?=htmlspecialchars($m['registrado']??'Sistema')?> — <?=date('d/m/Y H:i',strtotime($m['criado_em']))?></div>
    <p style="font-size:.875rem"><?=htmlspecialchars($m['descricao'])?></p>
  </div>
  <?php endforeach; ?>
  <?php if(empty($movs)): ?><p style="padding:1.25rem;color:var(--text-muted);text-align:center">Nenhuma movimentação.</p><?php endif; ?>
  <?php if(Auth::can('corregedoria.editar')): ?>
  <div style="padding:1rem 1.25rem;border-top:1px solid var(--border)">
    <form method="POST" action="<?=BASE_URL?>/corregedoria/<?=$p['id']?>/movimentar">
      <?=Auth::csrfField()?>
      <div style="display:flex;gap:.75rem">
        <textarea name="descricao" class="form-control" rows="2" placeholder="Registrar movimentação..." style="flex:1" required></textarea>
        <button type="submit" class="btn btn-primary" style="align-self:flex-end">Registrar</button>
      </div>
    </form>
  </div>
  <?php endif; ?>
</div>
