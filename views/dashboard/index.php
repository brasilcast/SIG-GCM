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
 *//**
 * View: Dashboard — personalizado por perfil
 * Variável $perfil define qual bloco renderizar
 */
$user = Auth::user();
$pageTitle = 'Dashboard';
?>

<!-- Comunicados urgentes no topo -->
<?php foreach (($comunicados ?? []) as $com): ?>
  <?php if ($com['tipo'] === 'urgente'): ?>
  <div class="comunicado-urgente">
    <span class="comunicado-urgente-icon"><i class="fa-solid fa-circle-exclamation" style="color:var(--danger)"></i></span>
    <div>
      <strong><?= htmlspecialchars($com['titulo']) ?></strong>
      <p style="font-size:.875rem; margin-top:.25rem; color:var(--text-muted)"><?= nl2br(htmlspecialchars(substr($com['conteudo'],0,200))) ?></p>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/comunicados/<?= $com['id'] ?>/confirmar" style="flex-shrink:0">
      <?= Auth::csrfField() ?>
      <button class="btn btn-danger btn-sm">Li e entendi</button>
    </form>
  </div>
  <?php endif; ?>
<?php endforeach; ?>

<div class="page-header">
  <div>
    <h2>Olá, <?= htmlspecialchars(explode(' ', $user['nome'])[0]) ?>! </h2>
    <div class="breadcrumb">
      <?= date('l, d \d\e F \d\e Y', strtotime('now')) ?> &nbsp;|&nbsp;
      <?= strtolower(str_replace(['operador','inspetor','subcomandante','comandante','corregedor','admin'],
                                 ['Guarda Municipal','Inspetor','Subcomandante','Comandante','Corregedor','Administrador'],
                                 $perfil)) ?>
    </div>
  </div>
</div>

<!-- ============================================================ -->
<!-- DASHBOARD: OPERADOR                                          -->
<!-- ============================================================ -->
<?php if ($perfil === 'operador'): ?>

<div class="cards-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Talões do Turno</span>
      <div class="stat-card-icon icon-blue"><i class="fa-solid fa-file-lines"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['meusTaloes']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">registros de hoje</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Talões em Aberto</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-file-invoice"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--danger)"><?= $stats['taloesAbertos']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><a href="<?= BASE_URL ?>/taloes">Ver talões →</a></div>
  </div>

</div>

<!-- Escala da semana -->
<div class="table-wrapper">
  <div class="table-header">
    <h3><i class="fa-solid fa-calendar-days"></i> Minha Escala desta Semana</h3>
    <a href="<?= BASE_URL ?>/escalas" class="btn btn-secondary btn-sm">Ver mais</a>
  </div>
  <?php if (!empty($stats['escala'])): ?>
  <div class="table-responsive">
    <table>
      <thead><tr>
        <th>Data</th><th>Turno</th>
      </tr></thead>
      <tbody>
        <?php 
          $diasPT = ['Mon'=>'Seg', 'Tue'=>'Ter', 'Wed'=>'Qua', 'Thu'=>'Qui', 'Fri'=>'Sex', 'Sat'=>'Sáb', 'Sun'=>'Dom'];
          foreach ($stats['escala'] as $e): 
            $diaSemanaEng = date('D', strtotime($e['data']));
            $diaPT = $diasPT[$diaSemanaEng] ?? $diaSemanaEng;
        ?>
        <tr>
          <td><?= date('d/m/Y', strtotime($e['data'])) ?> (<?= $diaPT ?>)</td>
          <td><span class="badge badge-info"><?= ucfirst(str_replace('_', ' ', $e['turno'])) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p style="padding:1.5rem; color:var(--text-muted); text-align:center">Nenhuma escala para esta semana.</p>
  <?php endif; ?>
</div>

<!-- ============================================================ -->
<!-- DASHBOARD: INSPETOR                                          -->
<!-- ============================================================ -->
<?php elseif ($perfil === 'inspetor'): ?>

<div class="cards-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Talões em Aberto</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-file-lines"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--danger)"><?= $stats['taloesAbertos']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><a href="<?= BASE_URL ?>/taloes">Ver talões →</a></div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Escalados Hoje</span>
      <div class="stat-card-icon icon-green"><i class="fa-solid fa-user-shield"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['escalaHoje']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">agentes em serviço</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Trocas Pendentes</span>
      <div class="stat-card-icon icon-yellow"><i class="fa-solid fa-arrows-rotate"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--warning)"><?= $stats['trocasPendentes']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><a href="<?= BASE_URL ?>/trocas">Aprovar trocas →</a></div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Medidas Vencendo</span>
      <div class="stat-card-icon icon-purple"><i class="fa-solid fa-scale-balanced"></i></div>
    </div>
    <div class="stat-card-value" style="color:<?= ($stats['medidasVencendo']['total'] ?? 0) > 0 ? 'var(--warning)' : 'var(--success)' ?>">
      <?= $stats['medidasVencendo']['total'] ?? 0 ?>
    </div>
    <div class="stat-card-sub">nos próx. 7 dias</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Escolas sem Ronda</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-school"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--danger)"><?= $stats['rondasSemVisita']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">+7 dias sem visita</div>
  </div>
</div>

<!-- ============================================================ -->
<!-- DASHBOARD: COMANDANTE / SUBCOMANDANTE                        -->
<!-- ============================================================ -->
<?php elseif (in_array($perfil, ['comandante', 'subcomandante'])): ?>

<div class="cards-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Total Agentes</span>
      <div class="stat-card-icon icon-blue"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['totalAgentes']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">efetivo ativo</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Escalados Hoje</span>
      <div class="stat-card-icon icon-green"><i class="fa-solid fa-calendar-days"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['escaladosHoje']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">de <?= $stats['totalAgentes']['total'] ?? 0 ?> disponíveis</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Talões em Aberto</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-circle-exclamation" style="color:var(--danger)"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--danger)"><?= $stats['taloesAbertos']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><?= $stats['taloesHoje']['total'] ?? 0 ?> registrados hoje</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Viaturas Disp.</span>
      <div class="stat-card-icon icon-green"><i class="fa-solid fa-car"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['viaturasDisp']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><?= $stats['viaturasManut']['total'] ?? 0 ?> em manutenção</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Medidas Vencidas</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-scale-balanced"></i></div>
    </div>
    <div class="stat-card-value" style="color:<?= ($stats['medidasVencidas']['total'] ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' ?>">
      <?= $stats['medidasVencidas']['total'] ?? 0 ?>
    </div>
    <div class="stat-card-sub">medidas protetivas</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Processos Abertos</span>
      <div class="stat-card-icon icon-yellow"><i class="fa-solid fa-scale-balanced"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['processosAbertos']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><a href="<?= BASE_URL ?>/corregedoria">Ver processos →</a></div>
  </div>
</div>

<!-- Gráfico de talões (últimos 7 dias) -->
<?php if (!empty($stats['taloesSemana'])): ?>
<div class="table-wrapper" style="margin-bottom:1rem">
  <div class="table-header">
    <h3><i class="fa-solid fa-chart-bar"></i> Talões — Últimos 7 dias</h3>
  </div>
  <div style="padding:1.25rem; display:flex; align-items:flex-end; gap:0.5rem; height:180px;">
    <?php
    $max = max(array_column($stats['taloesSemana'], 'total')) ?: 1;
    foreach ($stats['taloesSemana'] as $dia):
      $pct  = round(($dia['total'] / $max) * 100);
      $date = date('d/m', strtotime($dia['dia']));
    ?>
    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:0.25rem; height:100%; justify-content:flex-end;">
      <span style="font-size:.75rem; color:var(--text-muted)"><?= $dia['total'] ?></span>
      <div style="width:100%; background:var(--primary); border-radius:4px 4px 0 0; height:<?= $pct ?>%; min-height:4px; transition:height .5s ease;"></div>
      <span style="font-size:.7rem; color:var(--text-muted)"><?= $date ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- DASHBOARD: CORREGEDOR                                        -->
<!-- ============================================================ -->
<?php elseif ($perfil === 'corregedor'): ?>

<div class="cards-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Processos Abertos</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-scale-balanced"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--danger)"><?= $stats['processosAbertos']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">aguardando instrução</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Em Andamento</span>
      <div class="stat-card-icon icon-yellow"><i class="fa-solid fa-arrows-rotate"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['processosAndamento']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">em apuração</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Prazos Vencendo</span>
      <div class="stat-card-icon icon-red"><i class="fa-solid fa-clock"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--warning)"><?= $stats['prazosVencendo']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">próximos 10 dias</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Concluídos</span>
      <div class="stat-card-icon icon-green"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i></div>
    </div>
    <div class="stat-card-value" style="color:var(--success)"><?= $stats['processosConcluidos']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">processos encerrados</div>
  </div>
</div>

<div class="table-wrapper">
  <div class="table-header">
    <h3><i class="fa-solid fa-scale-balanced"></i> Processos em Andamento</h3>
    <a href="<?= BASE_URL ?>/corregedoria/novo" class="btn btn-primary btn-sm">+ Novo Processo</a>
  </div>
  <?php if (!empty($stats['processos'])): ?>
  <div class="table-responsive">
    <table>
      <thead><tr><th>Nº</th><th>Tipo</th><th>Investigado</th><th>Prazo</th><th>Status</th><th>Ação</th></tr></thead>
      <tbody>
      <?php foreach ($stats['processos'] as $p): ?>
        <?php
          $prazoClass = '';
          if ($p['prazo_legal']) {
            $diff = (strtotime($p['prazo_legal']) - time()) / 86400;
            $prazoClass = $diff <= 0 ? 'badge-danger' : ($diff <= 10 ? 'badge-warning' : 'badge-success');
          }
        ?>
        <tr>
          <td><?= htmlspecialchars($p['numero']) ?></td>
          <td><span class="badge badge-secondary"><?= ucfirst(str_replace('_',' ',$p['tipo'])) ?></span></td>
          <td><?= htmlspecialchars($p['investigado_nome'] ?? '—') ?></td>
          <td>
            <?php if ($p['prazo_legal']): ?>
              <span class="badge <?= $prazoClass ?>"><?= date('d/m/Y', strtotime($p['prazo_legal'])) ?></span>
            <?php else: ?> — <?php endif; ?>
          </td>
          <td><span class="badge badge-info"><?= ucfirst($p['status']) ?></span></td>
          <td><a href="<?= BASE_URL ?>/corregedoria/<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Ver</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p style="padding:1.5rem; color:var(--text-muted); text-align:center">Nenhum processo em andamento.</p>
  <?php endif; ?>
</div>

<!-- ============================================================ -->
<!-- DASHBOARD: ADMIN                                             -->
<!-- ============================================================ -->
<?php elseif ($perfil === 'admin'): ?>

<div class="cards-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Total Usuários</span>
      <div class="stat-card-icon icon-blue"><i class="fa-solid fa-users"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['totalUsuarios']['total'] ?? 0 ?></div>
    <div class="stat-card-sub"><a href="<?= BASE_URL ?>/usuarios">Gerenciar →</a></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Total Talões</span>
      <div class="stat-card-icon icon-cyan"><i class="fa-solid fa-file-lines"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['totalTaloes']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">histórico completo</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Viaturas</span>
      <div class="stat-card-icon icon-green"><i class="fa-solid fa-car"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['totalViaturas']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">cadastradas e ativas</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Escolas</span>
      <div class="stat-card-icon icon-purple"><i class="fa-solid fa-school"></i></div>
    </div>
    <div class="stat-card-value"><?= $stats['totalEscolas']['total'] ?? 0 ?></div>
    <div class="stat-card-sub">no programa ronda</div>
  </div>
</div>

<div class="table-wrapper">
  <div class="table-header">
    <h3><i class="fa-solid fa-lock"></i> Últimos Logins</h3>
    <a href="<?= BASE_URL ?>/auditoria/logins" class="btn btn-secondary btn-sm">Ver todos</a>
  </div>
  <div class="table-responsive">
    <table>
      <thead><tr><th>Usuário</th><th>IP</th><th>Data/Hora</th></tr></thead>
      <tbody>
      <?php foreach (($stats['ultimosLogins'] ?? []) as $lg): ?>
        <tr>
          <td><?= htmlspecialchars($lg['nome'] ?? $lg['username_tentado']) ?></td>
          <td><?= htmlspecialchars($lg['ip']) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($lg['tentado_em'])) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php endif; ?>

<!-- Comunicados informativos (não urgentes) -->
<?php
$comInfos = array_filter($comunicados ?? [], fn($c) => $c['tipo'] !== 'urgente');
if (!empty($comInfos)):
?>
<div class="table-wrapper" style="margin-top:1rem">
  <div class="table-header">
    <h3><i class="fa-solid fa-bullhorn"></i> Comunicados Recentes</h3>
    <a href="<?= BASE_URL ?>/comunicados" class="btn btn-secondary btn-sm">Ver todos</a>
  </div>
  <?php foreach ($comInfos as $com): ?>
  <div style="padding:.85rem 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:flex-start; gap:.75rem;">
    <span style="font-size:1.1rem"><?= $com['tipo'] === 'atencao' ? '<i class="fa-solid fa-triangle-exclamation"></i>' : '<i class="fa-solid fa-circle-info"></i>' ?></span>
    <div style="flex:1">
      <strong style="font-size:.9rem"><?= htmlspecialchars($com['titulo']) ?></strong>
      <p style="font-size:.8rem; color:var(--text-muted); margin-top:.2rem"><?= htmlspecialchars(substr($com['conteudo'],0,150)) ?><?= strlen($com['conteudo']) > 150 ? '...' : '' ?></p>
    </div>
    <?php if ($com['requer_confirmacao']): ?>
    <form method="POST" action="<?= BASE_URL ?>/comunicados/<?= $com['id'] ?>/confirmar">
      <?= Auth::csrfField() ?>
      <button class="btn btn-secondary btn-sm">Confirmar leitura</button>
    </form>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
