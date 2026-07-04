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
 */$pageTitle='Abastecimentos'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i> Abastecimentos</h2><div class="breadcrumb">Frota › Abastecimentos</div></div>
  <a href="<?=BASE_URL?>/frota/abastecimentos/novo" class="btn btn-primary">+ Novo</a>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Data</th><th>Viatura</th><th>Agente</th><th>Litros</th><th>Valor</th><th>KM</th><th>Consumo</th><th>Tipo</th></tr></thead>
    <tbody>
    <?php if(empty($abastec)): ?><tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum abastecimento.</td></tr>
    <?php else: foreach($abastec as $a): ?>
      <tr>
        <td><?=date('d/m/Y',strtotime($a['data_abastecimento']))?></td>
        <td><strong><?=htmlspecialchars($a['prefixo'])?></strong></td>
        <td><?=htmlspecialchars($a['agente'])?></td>
        <td><?=number_format($a['litros']??0,2,'.',',').' L'?></td>
        <td><strong>R$ <?=number_format($a['valor_total']??0,2,',','.')?></strong></td>
        <td><?=$a['km_atual']?number_format($a['km_atual'],0,'.',',').' km':'—'?></td>
        <td><?=$a['consumo_calculado']?'<span class="badge badge-info">'.$a['consumo_calculado'].' km/L</span>':'—'?></td>
        <td><?=htmlspecialchars($a['tipo_combustivel']?:'—')?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
