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
 */$pageTitle='Log de Logins'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-lock"></i> Log de Logins</h2><div class="breadcrumb">Auditoria › Logins</div></div>
  <a href="<?=BASE_URL?>/auditoria" class="btn btn-secondary">← Auditoria</a>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Data/Hora</th><th>Usuário</th><th>Username Tentado</th><th>IP</th><th>Resultado</th><th>Dispositivo</th></tr></thead>
    <tbody>
    <?php if(empty($logs)): ?><tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum log.</td></tr>
    <?php else: foreach($logs as $l):
      $sb = $l['sucesso'] ? 'badge-success' : 'badge-danger';
      $sl = $l['sucesso'] ? '<i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Sucesso' : '<i class="fa-solid fa-circle-xmark" style="color:var(--danger)"></i> Falha';
    ?>
      <tr>
        <td style="font-size:.78rem;white-space:nowrap"><?=date('d/m/Y H:i:s',strtotime($l['tentado_em']))?></td>
        <td><?=htmlspecialchars($l['nome']??'—')?></td>
        <td><code style="font-size:.78rem"><?=htmlspecialchars($l['username_tentado']??'—')?></code></td>
        <td><code style="font-size:.78rem"><?=htmlspecialchars($l['ip']??'—')?></code></td>
        <td><span class="badge <?=$sb?>"><?=$sl?></span></td>
        <td style="font-size:.72rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted)"><?=htmlspecialchars(substr($l['user_agent']??'—',0,60))?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
