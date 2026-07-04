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
 */$pageTitle=htmlspecialchars($v['nome']); ?>
<div class="page-header">
  <div><h2><i class='fa-solid fa-shield-halved'></i> <?=htmlspecialchars($v['nome'])?></h2><div class="breadcrumb">Maria da Penha › Detalhes</div></div>
  <a href="<?=BASE_URL?>/maria-penha" class="btn btn-secondary">← Voltar</a>
</div>
<?php $pLabels=[1=>'badge-danger',2=>'badge-warning',3=>'badge-info',4=>'badge-secondary',5=>'badge-secondary']; ?>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;margin-bottom:1rem">
  <div style="display:flex;flex-direction:column;gap:1rem">
    <div class="table-wrapper" style="padding:1.5rem">
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
        <span class="badge <?=$pLabels[$v['prioridade']]??"badge-secondary"?>" style="font-size:.85rem">P<?=$v['prioridade']?></span>
        <strong style="font-size:1.1rem"><?=htmlspecialchars($v['nome'])?></strong>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;font-size:.875rem">
        <?php foreach([['CPF',$v['cpf']],['Telefone',$v['telefone']],['Endereço',$v['endereco']],['Bairro',$v['bairro']],['Agressor',$v['nome_agressor']],['Vínculo',$v['vinculo_agressor']],['Contato Emergência',$v['nome_emergencia'].' — '.$v['telefone_emergencia']]] as [$l,$val]): if(!$val||$val==='— ') continue; ?>
        <div><span style="color:var(--text-muted);font-size:.78rem;text-transform:uppercase"><?=$l?></span><br><strong><?=htmlspecialchars($val)?></strong></div>
        <?php endforeach; ?>
      </div>
      <?php if($v['historico_violencia']): ?>
      <div style="margin-top:1rem"><h4 style="font-size:.8rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:.4rem">Histórico de Violência</h4>
        <p style="font-size:.85rem;white-space:pre-wrap;background:var(--surface-2);padding:.75rem;border-radius:4px"><?=htmlspecialchars($v['historico_violencia'])?></p></div>
      <?php endif; ?>
    </div>
    <!-- Medidas Protetivas -->
    <div class="table-wrapper" style="padding:1.25rem">
      <div class="table-header" style="padding:0;border:none;margin-bottom:.85rem"><h3><i class="fa-solid fa-scale-balanced"></i> Medidas Protetivas</h3></div>
      <?php foreach($medidas as $m): $mi=['vigente'=>'badge-success','vencida'=>'badge-danger','renovada'=>'badge-info','arquivada'=>'badge-secondary'][$m['status']]??'badge-secondary'; ?>
      <div style="padding:.6rem;background:var(--surface-2);border-radius:4px;margin-bottom:.5rem;font-size:.85rem">
        <div style="display:flex;justify-content:space-between">
          <strong><?=htmlspecialchars($m['tipo']??'—')?></strong>
          <span class="badge <?=$mi?>"><?=ucfirst($m['status'])?></span>
        </div>
        <div style="color:var(--text-muted);font-size:.78rem;margin-top:.2rem">Processo: <?=htmlspecialchars($m['numero_processo']??'—')?> · Vence: <?=$m['data_vencimento']?date('d/m/Y',strtotime($m['data_vencimento'])):'—'?></div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($medidas)): ?><p style="color:var(--text-muted);font-size:.85rem">Nenhuma medida cadastrada.</p><?php endif; ?>
      <?php if(Auth::can('maria_penha.cadastrar_vitima')): ?>
      <div style="margin-top:.75rem;border-top:1px solid var(--border);padding-top:.75rem">
        <form method="POST" action="<?=BASE_URL?>/maria-penha/<?=$v['id']?>/medida">
          <?=Auth::csrfField()?>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem">
            <input type="text" name="tipo" class="form-control" placeholder="Tipo da medida" required>
            <input type="text" name="numero_processo" class="form-control" placeholder="Nº do processo">
            <input type="date" name="data_inicio" class="form-control" value="<?=date('Y-m-d')?>">
            <input type="date" name="data_vencimento" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary btn-sm" style="margin-top:.5rem">+ Adicionar Medida</button>
        </form>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- Registro de Visita -->
  <div style="display:flex;flex-direction:column;gap:1rem">
    <?php if(Auth::can('maria_penha.visitar')): ?>
    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.9rem;margin-bottom:.85rem"><i class="fa-solid fa-location-dot"></i> Registrar Visita</h3>
      <form method="POST" action="<?=BASE_URL?>/maria-penha/<?=$v['id']?>/visita" enctype="multipart/form-data">
        <?=Auth::csrfField()?>
        <input type="hidden" name="latitude" id="lat">
        <input type="hidden" name="longitude" id="lon">
        <div class="form-group"><label class="form-label">Situação</label>
          <select name="situacao" class="form-control">
            <option value="normal"><i class='fa-solid fa-circle'></i> Normal</option>
            <option value="necessita_atencao"><i class='fa-solid fa-circle'></i> Necessita Atenção</option>
            <option value="urgente"><i class='fa-solid fa-circle'></i> Urgente</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Relato</label>
          <textarea name="relato" class="form-control" rows="4" placeholder="Como está a situação?"></textarea>
        </div>
        <button type="button" onclick="capturarGPS('lat','lon','btnG')" id="btnG" class="btn btn-secondary btn-sm btn-block" style="margin-bottom:.5rem"><i class="fa-solid fa-location-dot"></i> Capturar GPS</button>
        <button type="submit" class="btn btn-primary btn-block">Registrar Visita</button>
      </form>
    </div>
    <?php endif; ?>
    <!-- Histórico de Visitas -->
    <div class="table-wrapper" style="padding:1.25rem;max-height:350px;overflow-y:auto">
      <h3 style="font-size:.9rem;margin-bottom:.75rem"><i class="fa-solid fa-file-lines"></i> Últimas Visitas (<?=count($visitas)?>)</h3>
      <?php foreach($visitas as $vis): $si=['normal'=>'badge-success','necessita_atencao'=>'badge-warning','urgente'=>'badge-danger'][$vis['situacao']]?? 'badge-secondary'; ?>
      <div style="border-bottom:1px solid var(--border);padding:.6rem 0;font-size:.83rem">
        <div style="display:flex;justify-content:space-between">
          <span class="badge <?=$si?>"><?=ucfirst(str_replace('_',' ',$vis['situacao']))?></span>
          <span style="color:var(--text-muted)"><?=date('d/m/Y H:i',strtotime($vis['realizado_em']))?></span>
        </div>
        <div style="margin-top:.25rem"><strong><?=htmlspecialchars($vis['agente'])?></strong></div>
        <?php if($vis['relato']): ?><p style="color:var(--text-muted);margin-top:.2rem"><?=htmlspecialchars(substr($vis['relato'],0,120))?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php if(empty($visitas)): ?><p style="color:var(--text-muted);font-size:.85rem;text-align:center">Nenhuma visita registrada.</p><?php endif; ?>
    </div>
  </div>
</div>
