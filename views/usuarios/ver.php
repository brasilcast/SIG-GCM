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
 */$pageTitle = htmlspecialchars($u['nome']); ?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-user"></i> <?=htmlspecialchars($u['nome'])?></h2>
    <div class="breadcrumb">Usuários › Perfil</div>
  </div>
  <div style="display:flex;gap:.5rem">
    <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>/editar" class="btn btn-secondary"><i class="fa-solid fa-pencil"></i> Editar</a>
    <?php if(Auth::can('usuarios.alterar_permissoes')): ?>
    <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>/permissoes" class="btn btn-primary"><i class="fa-solid fa-lock"></i> Permissões</a>
    <?php endif; ?>
    <a href="<?=BASE_URL?>/usuarios" class="btn btn-secondary">← Voltar</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
  <!-- Info Card -->
  <div class="table-wrapper" style="padding:1.5rem">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem">
      <div style="width:64px;height:64px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#fff;flex-shrink:0">
        <?=strtoupper(substr($u['nome'],0,1))?>
      </div>
      <div>
        <strong style="font-size:1.1rem"><?=htmlspecialchars($u['nome'])?></strong><br>
        <code style="font-size:.8rem;color:var(--text-muted)">@<?=htmlspecialchars($u['username'])?></code><br>
        <?php $badges=['operador'=>'badge-secondary','inspetor'=>'badge-info','subcomandante'=>'badge-primary','comandante'=>'badge-warning','corregedor'=>'badge-danger','admin'=>'badge-success']; ?>
        <span class="badge <?=$badges[$u['perfil']]??'badge-secondary'?>"><?=ucfirst($u['perfil'])?></span>
        <span class="badge <?=$u['ativo']?'badge-success':'badge-danger'?>"><?=$u['ativo']?'Ativo':'Inativo'?></span>
        <?php if(!empty($u['primeiro_acesso'])): ?><span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Senha Temporária</span><?php endif; ?>
      </div>
    </div>
    <table width="100%" style="font-size:.875rem">
      <?php 
      $enderecoCompleto = trim(($u['endereco']??'') . ' ' . ($u['bairro']??''));
      $cidadeEstado = trim(($u['cidade']??'') . (($u['cidade']??'') && ($u['estado']??'') ? '/' : '') . ($u['estado']??''));
      $campos=[
          ['Matrícula',$u['matricula']],
          ['E-mail',$u['email']],
          ['CPF',$u['cpf']],
          ['RG',$u['rg']],
          ['Nascimento',!empty($u['data_nascimento'])?date('d/m/Y',strtotime($u['data_nascimento'])):''],
          ['Telefone',$u['telefone']],
          ['Endereço', $enderecoCompleto],
          ['Cidade/UF', $cidadeEstado],
          ['Último Login',$u['ultimo_login']?date('d/m/Y H:i',strtotime($u['ultimo_login'])):'Nunca']
      ]; 
      ?>
      <?php foreach($campos as [$l,$v]): if(!$v) continue; ?>
      <tr style="border-bottom:1px solid var(--border)">
        <td style="padding:.5rem 0;color:var(--text-muted);width:40%"><?=$l?></td>
        <td style="padding:.5rem 0;font-weight:500"><?=htmlspecialchars($v)?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- Ações rápidas -->
  <div style="display:flex;flex-direction:column;gap:1rem">
    <!-- Toggle Ativo -->
    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-sliders"></i> Ações Rápidas</h3>
      <?php if((int)$u['id']!==Auth::id()): ?>
      <form method="POST" action="<?=BASE_URL?>/usuarios/<?=$u['id']?>/toggle" onsubmit="return confirm('<?=$u['ativo']?'Desativar':'Ativar'?> este usuário?')" style="margin-bottom:0.5rem">
        <?=Auth::csrfField()?>
        <button type="submit" class="btn <?=$u['ativo']?'btn-danger':'btn-success'?> btn-block">
          <?=$u['ativo']?'<i class="fa-solid fa-ban"></i> Desativar Usuário':'<i class="fa-solid fa-circle-check"></i> Ativar Usuário'?>
        </button>
      </form>
      <?php else: ?>
      <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:.5rem">Você não pode desativar sua própria conta.</p>
      <?php endif; ?>

      <?php if(Auth::can('usuarios.criar')): ?>
      <form method="POST" action="<?=BASE_URL?>/usuarios/<?=$u['id']?>/gerar-senha" onsubmit="return confirm('Tem certeza que deseja forçar uma nova senha temporária para este usuário? A senha atual dele deixará de funcionar imediatamente.')" style="margin:0">
        <?=Auth::csrfField()?>
        <button type="submit" class="btn btn-warning btn-block">
          <i class="fa-solid fa-key"></i> Gerar Nova Senha Temporária
        </button>
      </form>
      <?php endif; ?>
    </div>

    <!-- Painel de permissões -->
    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.95rem;margin-bottom:.75rem"><i class="fa-solid fa-lock"></i> Personalizações de Permissão</h3>
      <?php
      // Map de labels legíveis
      $labelMap = [
          'dashboard'=>'Ver dashboard','taloes.criar'=>'Criar talões','taloes.ver_proprios'=>'Ver próprios talões',
          'taloes.ver_todos'=>'Ver todos talões','taloes.editar_parcial'=>'Editar talões (parcial)','taloes.editar_todos'=>'Editar todos talões',
          'frota.checklists'=>'Checklists viatura','frota.abastecimentos'=>'Abastecimentos','frota.cadastrar_viaturas'=>'Cadastrar viaturas','frota.ver'=>'Ver frota',
          'escala.ver_propria'=>'Ver própria escala','escala.criar'=>'Criar escalas','escala.editar'=>'Editar escalas','escala.ver_todos'=>'Ver todas escalas',
          'troca.solicitar'=>'Solicitar troca','troca.aprovar'=>'Aprovar trocas',
          'ronda_escolar.registrar'=>'Ronda escolar','ronda_escolar.ver_todos'=>'Ver rondas',
          'maria_penha.visitar'=>'Visitas MPenha','maria_penha.cadastrar_vitima'=>'Cadastrar vítimas','maria_penha.ver'=>'Ver MPenha',
          'rh.ver_proprio'=>'Ver própria ficha','rh.ver_todos'=>'Ver fichas todos','rh.editar'=>'Editar fichas',
          'materiais.ver_acautelados'=>'Ver materiais','materiais.movimentar'=>'Movimentar materiais','materiais.cadastrar'=>'Cadastrar materiais',
          'treinamentos.ver_proprios'=>'Ver treinamentos','treinamentos.cadastrar_cursos'=>'Cadastrar treinamentos',
          'comunicados.ver'=>'Ver comunicados','comunicados.criar'=>'Criar comunicados',
          'relatorios.basicos'=>'Relatórios básicos','relatorios.gerenciais'=>'Relatórios gerenciais',
          'corregedoria.ver'=>'Ver processos','corregedoria.criar'=>'Criar processos','corregedoria.editar'=>'Editar processos',
          'auditoria.ver'=>'Ver auditoria','usuarios.criar'=>'Criar usuários','usuarios.alterar_permissoes'=>'Alterar permissões',
          'alertas.ver'=>'Ver alertas','alertas.criar'=>'Criar alertas',
      ];
      $permsCustom = Database::getInstance()->fetchAll("SELECT recurso,permitido FROM permissoes_usuario WHERE usuario_id=?",[$u['id']]);
      $perfilDefaults = Auth::defaultsForPerfil($u['perfil']);
      $isAdminPerfil = $u['perfil'] === 'admin';

      // Classificar as customizações como "adicionadas" ou "removidas"
      $adicionadas = [];
      $removidas = [];
      foreach ($permsCustom as $pc) {
          $inDefault = $isAdminPerfil || in_array($pc['recurso'], $perfilDefaults);
          if ($pc['permitido'] && !$inDefault) {
              $adicionadas[] = $pc['recurso'];  // tinha no banco ativo, mas não era do perfil
          } elseif (!$pc['permitido'] && $inDefault) {
              $removidas[] = $pc['recurso'];    // estava no perfil, mas foi removida
          }
      }

      if (empty($permsCustom) || (empty($adicionadas) && empty($removidas))): ?>
        <p style="font-size:.85rem;color:var(--text-muted)">Usando permissões padrão do perfil <strong><?=ucfirst($u['perfil'])?></strong>. Nenhuma customização ativa.</p>
      <?php else: ?>
        <?php if($adicionadas): ?>
        <p style="font-size:.8rem;font-weight:600;color:#22c55e;margin:0 0 .3rem"><i class="fa-solid fa-circle-plus"></i> Permissões extras (além do padrão do perfil):</p>
        <div style="display:flex;flex-wrap:wrap;gap:.3rem;margin-bottom:.6rem">
          <?php foreach($adicionadas as $r): ?>
          <span class="badge badge-success" style="font-size:.7rem">
            <i class="fa-solid fa-circle-check"></i> <?=htmlspecialchars($labelMap[$r]??$r)?>
          </span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if($removidas): ?>
        <p style="font-size:.8rem;font-weight:600;color:#ef4444;margin:0 0 .3rem"><i class="fa-solid fa-circle-minus"></i> Permissões removidas (estava no padrão do perfil):</p>
        <div style="display:flex;flex-wrap:wrap;gap:.3rem;margin-bottom:.6rem">
          <?php foreach($removidas as $r): ?>
          <span class="badge badge-danger" style="font-size:.7rem">
            <i class="fa-solid fa-circle-xmark"></i> <?=htmlspecialchars($labelMap[$r]??$r)?>
          </span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if(Auth::can('usuarios.alterar_permissoes')): ?>
      <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>/permissoes" class="btn btn-secondary btn-sm" style="margin-top:.5rem">
        <i class="fa-solid fa-sliders"></i> Editar Permissões
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Histórico de Logins -->
<div class="table-wrapper">
  <div class="table-header"><h3><i class="fa-solid fa-clock-rotate-left"></i> Histórico de Logins (últimos 20)</h3></div>
  <div class="table-responsive">
    <table>
      <thead><tr><th>Data/Hora</th><th>IP</th><th>Status</th></tr></thead>
      <tbody>
      <?php if(empty($logins)): ?>
        <tr><td colspan="3" style="text-align:center;padding:1.5rem;color:var(--text-muted)">Nenhum registro de login.</td></tr>
      <?php else: foreach($logins as $l): ?>
        <tr>
          <td><?=date('d/m/Y H:i:s',strtotime($l['tentado_em']))?></td>
          <td><code style="font-size:.8rem"><?=htmlspecialchars($l['ip'])?></code></td>
          <td><span class="badge <?=$l['sucesso']?'badge-success':'badge-danger'?>"><?=$l['sucesso']?'<i class="fa-solid fa-circle-check"></i> Sucesso':'<i class="fa-solid fa-circle-xmark"></i> Falhou'?></span></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
