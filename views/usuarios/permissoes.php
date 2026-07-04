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
 */$pageTitle = 'Permissões — '.htmlspecialchars($u['nome']);

/* ── Labels & grupos (idênticos a antes) ───────────────────────── */
$grupos = [
    'Dashboard'        => ['dashboard'],
    'Talões'           => ['taloes.criar','taloes.ver_proprios','taloes.ver_todos','taloes.editar_parcial','taloes.editar_todos'],
    'Frota'            => ['frota.checklists','frota.abastecimentos','frota.cadastrar_viaturas','frota.ver'],
    'Escalas'          => ['escala.ver_propria','escala.criar','escala.editar','escala.ver_todos','troca.solicitar','troca.aprovar'],
    'Ronda Escolar'    => ['ronda_escolar.registrar','ronda_escolar.ver_todos'],
    'Maria da Penha'   => ['maria_penha.visitar','maria_penha.cadastrar_vitima','maria_penha.ver'],
    'Recursos Humanos' => ['rh.ver_proprio','rh.ver_todos','rh.editar'],
    'Materiais'        => ['materiais.ver_acautelados','materiais.movimentar','materiais.cadastrar'],
    'Treinamentos'     => ['treinamentos.ver_proprios','treinamentos.cadastrar_cursos'],
    'Comunicados'      => ['comunicados.ver','comunicados.criar'],
    'Relatórios'       => ['relatorios.basicos','relatorios.gerenciais'],
    'Corregedoria'     => ['corregedoria.ver','corregedoria.criar','corregedoria.editar'],
    'Administração'    => ['auditoria.ver','usuarios.criar','usuarios.alterar_permissoes','alertas.ver','alertas.criar'],
];
$labels = [
    'dashboard'                  => 'Ver dashboard',
    'taloes.criar'               => 'Criar talões',
    'taloes.ver_proprios'        => 'Ver próprios talões',
    'taloes.ver_todos'           => 'Ver todos os talões',
    'taloes.editar_parcial'      => 'Editar talões (parcial)',
    'taloes.editar_todos'        => 'Editar todos os talões',
    'frota.checklists'           => 'Checklists de viatura',
    'frota.abastecimentos'       => 'Abastecimentos',
    'frota.cadastrar_viaturas'   => 'Cadastrar viaturas',
    'frota.ver'                  => 'Ver frotas (somente leitura)',
    'escala.ver_propria'         => 'Ver própria escala',
    'escala.criar'               => 'Criar escalas',
    'escala.editar'              => 'Editar escalas',
    'escala.ver_todos'           => 'Ver todas as escalas',
    'escala.excluir'             => 'Excluir plantões',
    'troca.solicitar'            => 'Solicitar troca de plantão',
    'troca.aprovar'              => 'Aprovar trocas',
    'banco_horas.ver_proprio'    => 'Ver próprio banco de horas',
    'banco_horas.lancar'         => 'Lançar banco de horas',
    'ronda_escolar.registrar'    => 'Registrar ronda escolar',
    'ronda_escolar.ver_todos'    => 'Ver todas as rondas',
    'maria_penha.visitar'        => 'Registrar visitas',
    'maria_penha.cadastrar_vitima'=> 'Cadastrar vítimas',
    'maria_penha.ver'            => 'Somente leitura (M. Penha)',
    'rh.ver_proprio'             => 'Ver própria ficha',
    'rh.ver_todos'               => 'Ver fichas de todos',
    'rh.editar'                  => 'Editar fichas',
    'materiais.ver_acautelados'  => 'Ver materiais acautelados',
    'materiais.movimentar'       => 'Movimentar materiais',
    'materiais.cadastrar'        => 'Cadastrar materiais',
    'treinamentos.ver_proprios'  => 'Ver próprios treinamentos',
    'treinamentos.cadastrar_cursos' => 'Cadastrar treinamentos',
    'comunicados.ver'            => 'Ver comunicados',
    'comunicados.criar'          => 'Criar comunicados',
    'relatorios.basicos'         => 'Relatórios básicos',
    'relatorios.gerenciais'      => 'Relatórios gerenciais',
    'corregedoria.ver'           => 'Ver processos',
    'corregedoria.criar'         => 'Criar processos',
    'corregedoria.editar'        => 'Editar processos',
    'auditoria.ver'              => 'Ver auditoria/logins',
    'usuarios.criar'             => 'Criar usuários',
    'usuarios.alterar_permissoes'=> 'Alterar permissões',
    'alertas.ver'                => 'Ver alertas',
    'alertas.criar'              => 'Criar alertas',
];

/* ── Permissões efetivas ───────────────────────────────────────────
   Regra: customização individual sobrescreve o padrão do perfil.
   $permsMap  vem do controller (recurso => 0|1 das customizações).
   $defaultSet são as permissões do perfil sem customizações.          */
$defaultSet = Auth::defaultsForPerfil($u['perfil']); // array de strings
$isAdmin    = $u['perfil'] === 'admin';               // admin tem tudo (*)

/**
 * Retorna true se o recurso deve estar marcado considerando:
 *  1. Se existe customização explícita (base de dados), usa ela.
 *  2. Caso contrário, usa o padrão do perfil.
 */
$isChecked = function(string $recurso) use ($permsMap, $defaultSet, $isAdmin): bool {
    if (isset($permsMap[$recurso])) {
        return (bool)$permsMap[$recurso]; // customização explícita
    }
    if ($isAdmin) return true;
    return in_array($recurso, $defaultSet); // padrão do perfil
};

/**
 * Indica se o recurso pertence ao padrão do perfil (sem customizações).
 */
$isDefault = fn(string $r) => $isAdmin || in_array($r, $defaultSet);
?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-lock"></i> Permissões — <?=htmlspecialchars($u['nome'])?></h2>
    <div class="breadcrumb">Usuários › Permissões › <?=htmlspecialchars($u['username'])?></div>
  </div>
  <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>" class="btn btn-secondary">← Voltar</a>
</div>

<div class="alert alert-info" style="margin-bottom:1rem">
  <i class="fa-solid fa-circle-info"></i>
  As permissões já <strong>pré-marcadas</strong> refletem o padrão do perfil <strong><?=ucfirst($u['perfil'])?></strong>.
  Marque / desmarque para <em>personalizar individualmente</em> este usuário — as alterações sobrescrevem apenas o que você modificar.
</div>

<!-- Legenda -->
<div style="display:flex;gap:1rem;margin-bottom:1rem;font-size:.8rem;flex-wrap:wrap">
  <span><span style="display:inline-block;width:12px;height:12px;background:#22c55e;border-radius:2px;vertical-align:middle;margin-right:4px"></span>Padrão do perfil (pré-marcado)</span>
  <span><span style="display:inline-block;width:12px;height:12px;background:#3b82f6;border-radius:2px;vertical-align:middle;margin-right:4px"></span>Adicionado manualmente</span>
  <span><span style="display:inline-block;width:12px;height:12px;background:#ef4444;border-radius:2px;vertical-align:middle;margin-right:4px"></span>Removido manualmente</span>
</div>

<form method="POST" action="<?=BASE_URL?>/usuarios/<?=$u['id']?>/permissoes">
  <?=Auth::csrfField()?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;margin-bottom:1rem">
    <?php foreach($grupos as $grupo=>$recursos): ?>
    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.9rem;margin-bottom:.85rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em"><?=$grupo?></h3>
      <?php foreach($recursos as $r):
        $checked   = $isChecked($r);
        $isDefault_ = $isDefault($r);
        $hasCustom  = isset($permsMap[$r]);
        // Determine highlight color of the label
        if ($hasCustom) {
            $accentColor = $permsMap[$r] ? '#3b82f6' : '#ef4444'; // added blue / removed red
        } elseif ($isDefault_) {
            $accentColor = '#22c55e'; // green = default of profile
        } else {
            $accentColor = 'var(--text-muted)';
        }
      ?>
      <label style="display:flex;align-items:center;gap:.6rem;padding:.35rem 0;cursor:pointer;font-size:.875rem;color:<?=$accentColor?>">
        <input type="checkbox" name="perms[]" value="<?=$r?>" <?=$checked?'checked':''?>
               style="width:16px;height:16px;accent-color:var(--primary-light)">
        <?=htmlspecialchars($labels[$r]??$r)?>
        <?php if($hasCustom && $isDefault_ && !$permsMap[$r]): ?>
          <span title="Removido do padrão" style="font-size:.7rem;opacity:.7">(removido)</span>
        <?php elseif($hasCustom && !$isDefault_ && $permsMap[$r]): ?>
          <span title="Adicionado manualmente" style="font-size:.7rem;opacity:.7">(extra)</span>
        <?php endif; ?>
      </label>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <div style="display:flex;gap:.75rem;justify-content:flex-end">
    <button type="button" class="btn btn-secondary" onclick="resetToDefaults()"><i class="fa-solid fa-rotate-left"></i> Restaurar Padrão do Perfil</button>
    <a href="<?=BASE_URL?>/usuarios/<?=$u['id']?>" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Salvar Permissões</button>
  </div>
</form>

<script>
const defaultPerms = <?=json_encode($defaultSet)?>;
function resetToDefaults() {
    if (!confirm('Isso vai restaurar as permissões deste usuário para o padrão do perfil "<?=ucfirst($u['perfil'])?>". Continuar?')) return;
    document.querySelectorAll('input[name="perms[]"]').forEach(cb => {
        cb.checked = defaultPerms.includes(cb.value) || <?=$isAdmin?'true':'false'?>;
    });
}
</script>
