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
 */$pageTitle = 'Usuários'; ?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-users"></i> Usuários do Sistema</h2>
    <div class="breadcrumb">Administração › Usuários</div>
  </div>
  <?php if(Auth::can('usuarios.criar')): ?>
  <a href="<?=BASE_URL?>/usuarios/novo" class="btn btn-primary">+ Novo Usuário</a>
  <?php endif; ?>
</div>

<div class="table-wrapper">
  <div class="table-header">
    <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;flex:1">
      <input type="text" name="q" class="form-control" placeholder="Buscar nome, usuário ou matrícula..." value="<?=htmlspecialchars($busca)?>" style="max-width:280px">
      <select name="perfil" class="form-control" style="max-width:180px">
        <option value="">Todos os perfis</option>
        <?php foreach(['operador','inspetor','subcomandante','comandante','corregedor','admin'] as $p): ?>
        <option value="<?=$p?>" <?=$perfil===$p?'selected':''?>><?=ucfirst($p)?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-secondary">Filtrar</button>
      <?php if($busca||$perfil): ?><a href="<?=BASE_URL?>/usuarios" class="btn btn-secondary"><i class='fa-solid fa-xmark'></i> Limpar</a><?php endif; ?>
    </form>
  </div>
  <div class="table-responsive">
    <table>
      <thead><tr><th>Nome</th><th>Username</th><th>Perfil</th><th>Matrícula</th><th>Último Login</th><th>Status</th><th>Ações</th></tr></thead>
      <tbody>
      <?php if(empty($usuarios)): ?>
        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum usuário encontrado.</td></tr>
      <?php else: foreach($usuarios as $u): ?>
        <tr>
          <td><strong><?=htmlspecialchars($u['nome'])?></strong></td>
          <td><code style="background:var(--surface-2);padding:2px 6px;border-radius:4px;font-size:.8rem"><?=htmlspecialchars($u['username'])?></code></td>
          <td><?php
            $badges=['operador'=>'badge-secondary','inspetor'=>'badge-info','subcomandante'=>'badge-primary','comandante'=>'badge-warning','corregedor'=>'badge-danger','admin'=>'badge-success'];
            ?><span class="badge <?=$badges[$u['perfil']]??'badge-secondary'?>"><?=ucfirst($u['perfil'])?></span></td>
          <td><?=htmlspecialchars($u['matricula']?:'—')?></td>
          <td style="font-size:.8rem;color:var(--text-muted)"><?=$u['ultimo_login']?date('d/m/Y H:i',strtotime($u['ultimo_login'])):'Nunca'?></td>
          <td><span class="badge <?=$u['ativo']?'badge-success':'badge-danger'?>"><?=$u['ativo']?'Ativo':'Inativo'?></span></td>
          <td style="white-space:nowrap">
            <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>" class="btn btn-secondary btn-sm">Ver</a>
            <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>/editar" class="btn btn-secondary btn-sm"><i class="fa-solid fa-pencil"></i></a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($pag['total_pages']>1): ?>
  <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 1.25rem;border-top:1px solid var(--border);font-size:.85rem;color:var(--text-muted)">
    <span>Mostrando <?=count($usuarios)?> de <?=$pag['total']?> usuário(s)</span>
    <div style="display:flex;gap:.35rem">
      <?php if($pag['has_prev']): ?><a href="?page=<?=$pag['current']-1?>&q=<?=urlencode($busca)?>&perfil=<?=$perfil?>" class="btn btn-secondary btn-sm">‹ Anterior</a><?php endif; ?>
      <?php if($pag['has_next']): ?><a href="?page=<?=$pag['current']+1?>&q=<?=urlencode($busca)?>&perfil=<?=$perfil?>" class="btn btn-secondary btn-sm">Próximo ›</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
