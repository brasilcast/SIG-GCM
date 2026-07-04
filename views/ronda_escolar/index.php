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
 */$pageTitle='Ronda Escolar'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-school"></i> Ronda Escolar</h2><div class="breadcrumb">Operacional › Ronda Escolar</div></div>
  <div style="display:flex;gap:.5rem">
    <?php if(Auth::can('ronda_escolar.registrar')): ?>
    <a href="<?=BASE_URL?>/escolas/nova" class="btn btn-secondary">+ Nova Escola</a>
    <?php endif; ?>
    <a href="<?=BASE_URL?>/ronda-escolar/registrar" class="btn btn-primary">+ Registrar Ronda</a>
  </div>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Escola</th><th>Bairro</th><th>Total Rondas</th><th>Última Ronda</th><th>Status</th><th>Ação</th></tr></thead>
    <tbody>
    <?php if(empty($escolas)): ?>
      <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhuma escola cadastrada.</td></tr>
    <?php else: foreach($escolas as $e):
      $dias = $e['ultima_ronda']?round((time()-strtotime($e['ultima_ronda']))/86400):'∞';
      $cor  = $dias==='∞'||$dias>14?'badge-danger':($dias>7?'badge-warning':'badge-success');
    ?>
      <tr>
        <td><strong><?=htmlspecialchars($e['nome'])?></strong></td>
        <td><?=htmlspecialchars($e['bairro']?:'—')?></td>
        <td><?=$e['total_rondas']?> ronda(s)</td>
        <td><span class="badge <?=$cor?>"><?=$dias==='∞'?'Nunca visitada':'há '.$dias.' dia(s)'?></span></td>
        <td><?=$e['ultima_ronda']?'<span class="badge badge-info">'.date('d/m/Y H:i',strtotime($e['ultima_ronda'])).'</span>':'—'?></td>
        <td style="display:flex;gap:.35rem">
          <a href="<?=BASE_URL?>/ronda-escolar/<?=$e['id']?>" class="btn btn-secondary btn-sm" title="Ver Histórico">Ver</a>
          <?php if(Auth::can('ronda_escolar.registrar')): ?>
            <a href="<?=BASE_URL?>/escolas/<?=$e['id']?>/editar" class="btn btn-secondary btn-sm" style="color:var(--warning)" title="Editar Escola"><i class="fa-solid fa-pen"></i></a>
            <form method="POST" action="<?=BASE_URL?>/escolas/<?=$e['id']?>/excluir" style="margin:0" onsubmit="return confirm('Tem certeza que deseja desativar/excluir esta escola?');">
              <?=Auth::csrfField()?>
              <button class="btn btn-secondary btn-sm" style="color:var(--danger)" title="Desativar Escola"><i class="fa-solid fa-trash"></i></button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
