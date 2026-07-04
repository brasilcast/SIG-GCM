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
 */$pageTitle='Novo Checklist'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Novo Checklist de Viatura</h2><div class="breadcrumb">Frota › Checklists</div></div>
  <a href="<?=BASE_URL?>/frota/checklists" class="btn btn-secondary">← Voltar</a>
</div>

<form method="POST" action="<?=BASE_URL?>/frota/checklists/novo" enctype="multipart/form-data">
  <?=Auth::csrfField()?>
  <div class="grid-auto" style="margin-bottom:1.5rem">

    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem">Identificação</h3>
      <div class="form-group"><label class="form-label">Viatura *</label>
        <select name="viatura_id" id="viaturaSelect" class="form-control" required>
          <option value="">— Selecione —</option>
          <?php foreach($viaturas as $v): ?>
          <option value="<?=$v['id']?>" data-km="<?=$v['km_atual']?>"><?=htmlspecialchars($v['prefixo'].' — '.$v['modelo'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Tipo</label>
        <select name="tipo" class="form-control">
          <option value="saida">Saída</option>
          <option value="retorno">Retorno</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">KM da Viatura</label>
        <input type="number" step="0.1" name="km" id="kmInput" class="form-control" placeholder="KM atual">
      </div>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem">Fotos (opcional)</h3>
      <input type="file" name="fotos[]" class="form-control" multiple accept="image/*">
      <div class="form-group" style="margin-top:1rem"><label class="form-label">Observações Gerais</label>
        <textarea name="observacoes" class="form-control" rows="4" placeholder="Anotações gerais sobre a viatura..."></textarea>
      </div>
    </div>
  </div>

  <!-- Itens do Checklist -->
  <?php
  $grupos = [
    'Exterior'        => ['pneus'=>'Pneus','luzes'=>'Luzes','lataria'=>'Lataria','vidros'=>'Vidros'],
    'Sinalização'     => ['giroflex'=>'Giroflex','sirene'=>'Sirene'],
    'Itens Obrigat.'  => ['estepe'=>'Estepe','extintor'=>'Extintor','documentos'=>'Documentos','triangulo'=>'Triângulo'],
    'Níveis'          => ['oleo'=>'Óleo do Motor','agua'=>'Água do Radiador','combustivel'=>'Combustível'],
    'EPI / Equipam.'  => ['colete'=>'Colete','algemas'=>'Algemas','radio'=>'Rádio Comunicador'],
  ];
  ?>
  <?php foreach($grupos as $grp=>$itens): ?>
  <div class="checklist-section">
    <h3 class="checklist-section-title"><i class="fa-solid fa-list-check"></i> <?=$grp?></h3>
    <div class="checklist-grid">
      <?php foreach($itens as $k=>$l): ?>
      <div class="checklist-card">
        <div class="checklist-card-title"><?=$l?></div>
        <div class="checklist-radio-group">
          <?php foreach(['ok'=>'<i class="fa-solid fa-check"></i><br>OK','atencao'=>'<i class="fa-solid fa-triangle-exclamation"></i><br>Atenção','problema'=>'<i class="fa-solid fa-xmark"></i><br>Problema'] as $v=>$vl): ?>
          <div class="checklist-radio-item">
            <input type="radio" name="<?=$k?>" id="<?=$k?>_<?=$v?>" value="<?=$v?>" <?=$v==='ok'?'checked':''?>>
            <label class="checklist-radio-label" for="<?=$k?>_<?=$v?>">
              <?=$vl?>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <div class="form-actions">

    <a href="<?=BASE_URL?>/frota/checklists" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Salvar Checklist</button>
  </div>
</form>
<script>
document.getElementById('viaturaSelect').addEventListener('change',function(){
  const km = this.options[this.selectedIndex].dataset.km;
  if(km) document.getElementById('kmInput').value = km;
});
</script>
