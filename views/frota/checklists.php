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
 */$pageTitle='Checklists'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Checklists de Viaturas</h2><div class="breadcrumb">Frota › Checklists</div></div>
  <a href="<?=BASE_URL?>/frota/checklists/novo" class="btn btn-primary">+ Novo Checklist</a>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Data/Hora</th><th>Viatura</th><th>Tipo</th><th>Agente</th><th>KM</th><th>Situação</th></tr></thead>
    <tbody>
    <?php if(empty($checklists)): ?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum checklist registrado.</td></tr>
    <?php else: foreach($checklists as $c):
      $campos=['pneus','luzes','lataria','vidros','giroflex','sirene','estepe','extintor','documentos','triangulo','oleo','agua','combustivel','colete','algemas','radio'];
      $prob=count(array_filter($campos,fn($i)=>$c[$i]==='problema'));
      $atc =count(array_filter($campos,fn($i)=>$c[$i]==='atencao'));
    ?>
      <tr>
        <td><?=date('d/m/Y H:i',strtotime($c['realizado_em']))?></td>
        <td><strong><?=htmlspecialchars($c['prefixo'])?></strong></td>
        <td><span class="badge badge-info"><?=ucfirst($c['tipo'])?></span></td>
        <td><?=htmlspecialchars($c['agente'])?></td>
        <td><?=$c['km']?number_format($c['km'],0,'.',',').' km':'—'?></td>
        <td>
          <?php if($prob): ?><span class="badge badge-danger"><i class="fa-solid fa-circle-xmark" style="color:var(--danger)"></i> <?=$prob?> problema(s)</span>
          <?php elseif($atc): ?><span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> <?=$atc?> atenção</span>
          <?php else: ?><span class="badge badge-success"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Tudo OK</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
