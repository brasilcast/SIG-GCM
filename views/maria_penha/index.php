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
 */$pageTitle='Maria da Penha'; ?>
<div class="page-header">
  <div><h2><i class='fa-solid fa-shield-halved'></i> Maria da Penha</h2><div class="breadcrumb">Operacional › Maria da Penha</div></div>
  <div style="display:flex;gap:.5rem">
    <?php if(Auth::can('maria_penha.cadastrar_vitima')): ?>
    <a href="<?=BASE_URL?>/maria-penha/nova" class="btn btn-primary">+ Nova Vítima</a>
    <?php endif; ?>
    <a href="<?=BASE_URL?>/maria-penha/mapa" class="btn btn-secondary"><i class='fa-solid fa-map-location-dot'></i> Mapa</a>
  </div>
</div>
<div class="table-wrapper" style="margin-bottom:.75rem">
  <div style="padding:.75rem 1.25rem">
    <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap">
      <input type="text" name="q" class="form-control" placeholder="Buscar nome ou bairro..." value="<?=htmlspecialchars($busca??'')?>" style="max-width:260px">
      <select name="prioridade" class="form-control" style="max-width:160px">
        <option value="">Todas prioridades</option>
        <?php for($p=1;$p<=5;$p++): ?><option value="<?=$p?>" <?=($prio??'')==$p?'selected':''?>>P<?=$p?> — <?=['Máxima','Alta','Média','Baixa','Mínima'][$p-1]?></option><?php endfor; ?>
      </select>
      <button class="btn btn-secondary">Filtrar</button>
      <?php if($busca||$prio): ?><a href="<?=BASE_URL?>/maria-penha" class="btn btn-secondary"><i class='fa-solid fa-xmark'></i></a><?php endif; ?>
    </form>
  </div>
</div>
<div class="table-wrapper">
  <div class="table-responsive"><table>
    <thead><tr><th>Prioridade</th><th>Nome</th><th>Bairro</th><th>Telefone</th><th>Medidas Ativas</th><th>Última Visita</th><th>Ação</th></tr></thead>
    <tbody>
    <?php if(empty($vitimas)): ?><tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhuma vítima cadastrada.</td></tr>
    <?php else: foreach($vitimas as $v):
      $pLabels=['1'=>['badge-danger','<i class="fa-solid fa-circle"></i> P1 — Máxima'],'2'=>['badge-warning','<i class="fa-solid fa-circle"></i> P2 — Alta'],'3'=>['badge-info','<i class="fa-solid fa-circle"></i> P3 — Média'],'4'=>['badge-secondary','<i class="fa-solid fa-circle"></i> P4 — Baixa'],'5'=>['badge-secondary','<i class="fa-regular fa-circle"></i> P5 — Mínima']];
      [$pb,$pl]=$pLabels[$v['prioridade']]??['badge-secondary','P?'];
      $diasUlt = $v['ultima_visita']?round((time()-strtotime($v['ultima_visita']))/86400):'∞';
      $diasCor = $diasUlt==='∞'||$diasUlt>30?'badge-danger':($diasUlt>14?'badge-warning':'badge-success');
    ?>
      <tr style="cursor:pointer" onclick="location.href='<?=BASE_URL?>/maria-penha/<?=$v['id']?>'">
        <td><span class="badge <?=$pb?>"><?=$pl?></span></td>
        <td><strong><?=htmlspecialchars($v['nome'])?></strong></td>
        <td><?=htmlspecialchars($v['bairro']?:'—')?></td>
        <td><?=htmlspecialchars($v['telefone']?:'—')?></td>
        <td><?=$v['medidas_ativas']?'<span class="badge badge-success">'.$v['medidas_ativas'].' ativa(s)</span>':'<span class="badge badge-secondary">Nenhuma</span>'?></td>
        <td><span class="badge <?=$diasCor?>"><?=$diasUlt==='∞'?'Nunca visitada':'há '.$diasUlt.' dia(s)'?></span></td>
        <td onclick="event.stopPropagation()" style="display:flex;gap:.35rem">
          <a href="<?=BASE_URL?>/maria-penha/<?=$v['id']?>" class="btn btn-secondary btn-sm" title="Ver Histórico">Ver</a>
          <?php if(Auth::can('maria_penha.cadastrar_vitima')): ?>
            <a href="<?=BASE_URL?>/maria-penha/<?=$v['id']?>/editar" class="btn btn-secondary btn-sm" style="color:var(--warning)" title="Editar Vítima"><i class="fa-solid fa-pen"></i></a>
            <form method="POST" action="<?=BASE_URL?>/maria-penha/<?=$v['id']?>/excluir" style="margin:0" onsubmit="return confirm('Tem certeza que deseja desativar/excluir esta vítima?');">
              <?=Auth::csrfField()?>
              <button class="btn btn-secondary btn-sm" style="color:var(--danger)" title="Remover Vítima"><i class="fa-solid fa-trash"></i></button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>
