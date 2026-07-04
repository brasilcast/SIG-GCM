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
 */$pageTitle = $titulo ?? 'Usuário';
$u = $usuario ?? [];
$isEdit = !empty($u['id']);
$action = $isEdit ? BASE_URL.'/usuarios/'.$u['id'].'/editar' : BASE_URL.'/usuarios/novo';
?>
<div class="page-header">
  <div>
    <h2><i class="fas fa-user-circle"></i> <?=htmlspecialchars($titulo)?></h2>
    <div class="breadcrumb">Usuários › <?=htmlspecialchars($titulo)?></div>
  </div>
  <a href="<?=BASE_URL?>/usuarios" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>

<form method="POST" action="<?=$action?>" enctype="multipart/form-data">
  <?=Auth::csrfField()?>
  <div class="grid-2" style="margin-bottom:1rem">


    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="margin-bottom:1.25rem;font-size:1rem"><i class="fas fa-id-card"></i> Dados de Acesso</h3>
      <div class="form-group">
        <label class="form-label">Nome Completo *</label>
        <input type="text" name="nome" class="form-control" value="<?=htmlspecialchars($u['nome']??'')?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Username (login) *</label>
        <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($u['username']??'')?>" required autocomplete="off">
      </div>
      <?php if(!$isEdit): ?>
      <div class="form-group">
        <label class="form-label">Senha Inicial *</label>
        <!-- type="text" para facilitar a visualização do administrador que está criando a conta -->
        <input type="text" name="senha" class="form-control" placeholder="Defina a senha inicial deste usuário..." required autocomplete="new-password">
      </div>
      <?php endif; ?>
      <div class="form-row">

        <div class="form-group">
          <label class="form-label">Perfil *</label>
          <select name="perfil" class="form-control" required>
            <?php foreach(['operador'=>'Operador','inspetor'=>'Inspetor','subcomandante'=>'Subcomandante','comandante'=>'Comandante','corregedor'=>'Corregedor','admin'=>'Administrador'] as $p=>$l): ?>
            <option value="<?=$p?>" <?=(($u['perfil']??'')===$p?'selected':'')?>><?=$l?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Equipe (Escala)</label>
          <select name="equipe" class="form-control">
            <option value="">— Sem Equipe —</option>
            <?php foreach(['alfa'=>'ALFA','bravo'=>'BRAVO','charlie'=>'CHARLIE','delta'=>'DELTA'] as $e=>$l): ?>
            <option value="<?=$e?>" <?=(($u['equipe']??'')===$e?'selected':'')?>><?=$l?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="margin-bottom:1.25rem;font-size:1rem"><i class="fas fa-id-badge"></i> Dados Funcionais e Segurança</h3>
      <div class="form-group">
        <label class="form-label">Matrícula</label>
        <input type="text" name="matricula" class="form-control" value="<?=htmlspecialchars($u['matricula']??'')?>">
      </div>
      <div class="form-group">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($u['email']??'')?>">
      </div>
      <?php if(!$isEdit): ?>
      <div class="alert alert-info" style="margin-top:1rem">
        <i class="fas fa-info-circle"></i> A senha inicial que você preencheu acima será exigida no primeiro login deste usuário. Ele será obrigado a alterá-la.
      </div>
      <?php else: ?>
      <div class="alert alert-warning" style="margin-top:1rem">
        <i class="fas fa-lock"></i> Para <strong>redefinir a senha</strong>, acesse o perfil deste usuário e use o botão amarelo "Gerar Nova Senha Temporária".
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="table-wrapper" style="padding:1.5rem; margin-top: 1rem;">
      <h3 style="margin-bottom:1.25rem;font-size:1rem"><i class="fas fa-address-book"></i> Dados Pessoais (Opcionais)</h3>
      <div class="form-row-3">

        <div class="form-group">
            <label class="form-label">CPF</label>
            <input type="text" name="cpf" class="form-control" value="<?=htmlspecialchars($u['cpf']??'')?>">
        </div>
        <div class="form-group">
            <label class="form-label">RG</label>
            <input type="text" name="rg" class="form-control" value="<?=htmlspecialchars($u['rg']??'')?>">
        </div>
        <div class="form-group">
            <label class="form-label">Data de Nascimento</label>
            <input type="date" name="data_nascimento" class="form-control" value="<?=htmlspecialchars($u['data_nascimento']??'')?>">
        </div>
        <div class="form-group">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" value="<?=htmlspecialchars($u['telefone']??'')?>">
        </div>
        <div class="form-group" style="grid-column: span 2;">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" class="form-control" value="<?=htmlspecialchars($u['endereco']??'')?>">
        </div>
        <div class="form-group">
            <label class="form-label">Bairro</label>
            <input type="text" name="bairro" class="form-control" value="<?=htmlspecialchars($u['bairro']??'')?>">
        </div>
        <div class="form-group">
            <label class="form-label">Cidade</label>
            <input type="text" name="cidade" class="form-control" value="<?=htmlspecialchars($u['cidade']??'')?>">
        </div>
        <div class="form-group">
            <label class="form-label">Estado (UF)</label>
            <input type="text" name="estado" class="form-control" value="<?=htmlspecialchars($u['estado']??'')?>" maxlength="2">
        </div>
      </div>
  </div>

  <div class="form-actions">

    <a href="<?=BASE_URL?>/usuarios" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
      <i class="fas <?=$isEdit?'fa-save':'fa-plus'?>"></i> <?=$isEdit?'Salvar Alterações':'Criar Usuário'?>
    </button>
  </div>
</form>
