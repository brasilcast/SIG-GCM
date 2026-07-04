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
 */$pageTitle='Auditoria do Sistema'; ?>
<div class="page-header">
  <div><h2><i class='fa-solid fa-magnifying-glass'></i> Log de Auditoria</h2><div class="breadcrumb">Administração › Auditoria</div></div>
  <a href="<?=BASE_URL?>/auditoria/logins" class="btn btn-secondary"><i class="fa-solid fa-lock"></i> Log de Logins</a>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Data/Hora</th><th>Usuário</th><th>Ação</th><th>Tabela</th><th>Registro</th></tr></thead>
    <tbody>
    <?php if(empty($logs)): ?><tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum log disponível.</td></tr>
    <?php else: foreach($logs as $l):
      $acaoB=['CREATE'=>'badge-success','UPDATE'=>'badge-info','DELETE'=>'badge-danger','VIEW'=>'badge-secondary'][$l['acao']]??'badge-secondary';
    ?>
      <tr>
        <td style="font-size:.8rem;white-space:nowrap"><?=date('d/m/Y H:i:s',strtotime($l['criado_em']))?></td>
        <td><?=htmlspecialchars($l['nome']??'Sistema')?></td>
        <td><span class="badge <?=$acaoB?>"><?=htmlspecialchars($l['acao'])?></span></td>
        <td><code style="font-size:.78rem"><?=htmlspecialchars($l['tabela'])?></code></td>
        <td style="font-size:.8rem">#<?=$l['registro_id']?>'—'?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
