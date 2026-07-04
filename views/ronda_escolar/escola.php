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
 */$pageTitle=htmlspecialchars($escola['nome']); ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-school"></i> <?=htmlspecialchars($escola['nome'])?></h2><div class="breadcrumb">Ronda Escolar › Escola</div></div>
  <div style="display:flex;gap:.5rem">
    <a href="<?=BASE_URL?>/ronda-escolar/registrar" class="btn btn-primary">+ Registrar Ronda</a>
    <a href="<?=BASE_URL?>/ronda-escolar" class="btn btn-secondary">← Voltar</a>
  </div>
</div>
<div class="cards-grid" style="margin-bottom:1rem">
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label">Total de Rondas</span><div class="stat-card-icon icon-blue"><i class="fa-solid fa-school"></i></div></div><div class="stat-card-value"><?=count($rondas)?></div></div>
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label">Última Ronda</span><div class="stat-card-icon icon-green"><i class="fa-solid fa-calendar-days"></i></div></div>
    <div class="stat-card-value" style="font-size:1rem"><?=!empty($rondas)?date('d/m/Y',strtotime($rondas[0]['realizado_em'])):'Nunca'?></div>
  </div>
  <div class="stat-card"><div class="stat-card-header"><span class="stat-card-label">Ocorrências</span><div class="stat-card-icon icon-red"><i class="fa-solid fa-triangle-exclamation"></i></div></div><div class="stat-card-value"><?=count($ocorrencias)?></div></div>
</div>
<div class="table-wrapper" style="padding:1.25rem;margin-bottom:1rem">
  <h3 style="font-size:.9rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:.75rem">Dados da Escola</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.6rem;font-size:.875rem">
    <?php foreach([['Endereço',$escola['endereco']],['Bairro',$escola['bairro']],['Diretor(a)',$escola['diretor']],['Telefone',$escola['telefone']]] as [$l,$val]): if(!$val) continue; ?>
    <div><span style="color:var(--text-muted);font-size:.75rem;text-transform:uppercase"><?=$l?></span><br><strong><?=htmlspecialchars($val)?></strong></div>
    <?php endforeach; ?>
  </div>
</div>
<div class="table-wrapper">
  <div class="table-header"><h3><i class="fa-solid fa-file-lines"></i> Histórico de Rondas</h3></div>
  <div class="table-responsive"><table>
    <thead><tr><th>Data/Hora</th><th>Agente</th><th>Viatura</th><th>Observações</th></tr></thead>
    <tbody>
    <?php if(empty($rondas)): ?><tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--text-muted)">Nenhuma ronda registrada.</td></tr>
    <?php else: foreach($rondas as $r): ?>
      <tr>
        <td><?=date('d/m/Y H:i',strtotime($r['realizado_em']))?></td>
        <td><?=htmlspecialchars($r['agente'])?></td>
        <td><?=$r['prefixo']?'<span class="badge badge-info">'.$r['prefixo'].'</span>':'—'?></td>
        <td style="font-size:.82rem;color:var(--text-muted)"><?=htmlspecialchars(substr($r['observacoes']??'—',0,80))?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
