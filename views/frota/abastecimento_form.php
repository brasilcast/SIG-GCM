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
 */$pageTitle='Novo Abastecimento'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-gas-pump" style="color:var(--primary)"></i> Novo Abastecimento</h2><div class="breadcrumb">Frota › Abastecimentos</div></div>
  <a href="<?=BASE_URL?>/frota/abastecimentos" class="btn btn-secondary">← Voltar</a>
</div>
<form method="POST" action="<?=BASE_URL?>/frota/abastecimentos/novo">
  <?=Auth::csrfField()?>
  <div class="grid-2" style="margin-bottom:1rem">

    <div class="table-wrapper" style="padding:1.5rem">
      <div class="form-group"><label class="form-label">Viatura *</label>
        <select name="viatura_id" id="viatSel" class="form-control" required>
          <option value="">— Selecione —</option>
          <?php foreach($viaturas as $v): ?><option value="<?=$v['id']?>" data-km="<?=$v['km_atual']?>"><?=htmlspecialchars($v['prefixo'].' — '.number_format($v['km_atual'],0,'.',',').' km')?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Data *</label><input type="date" name="data_abastecimento" class="form-control" value="<?=date('Y-m-d')?>" required></div>
      <div class="form-group"><label class="form-label">KM Atual</label><input type="number" step="0.1" name="km_atual" id="kmAtual" class="form-control" placeholder="KM do odômetro"></div>
      <div class="form-group"><label class="form-label">Tipo de Combustível</label>
        <select name="tipo_combustivel" class="form-control">
          <option value="gasolina">Gasolina</option><option value="etanol">Etanol</option><option value="diesel">Diesel</option><option value="gnv">GNV</option>
        </select>
      </div>
    </div>
    <div class="table-wrapper" style="padding:1.5rem">
      <div class="form-group"><label class="form-label">Litros</label><input type="number" step="0.01" name="litros" id="litros" class="form-control" placeholder="Ex: 40.5" oninput="calcConsumption()"></div>
      <div class="form-group"><label class="form-label">Valor Total (R$)</label><input type="number" step="0.01" name="valor_total" id="valorTotal" class="form-control" placeholder="Ex: 250.00" oninput="calcConsumption()"></div>
      <div style="background:var(--surface-2);border-radius:var(--radius);padding:.75rem;margin-bottom:1rem;font-size:.85rem">
        <span style="color:var(--text-muted)">Consumo calculado:</span> <strong id="consumoCalc">—</strong> &nbsp;|&nbsp;
        <span style="color:var(--text-muted)">R$/L:</span> <strong id="precoLitro">—</strong>
      </div>
      <div class="form-group"><label class="form-label">Posto / Fornecedor</label><input type="text" name="posto" class="form-control" placeholder="Nome do posto"></div>
      <div class="form-group"><label class="form-label">Observações</label><textarea name="observacoes" class="form-control" rows="3"></textarea></div>
    </div>
  </div>
  <div class="form-actions">

    <a href="<?=BASE_URL?>/frota/abastecimentos" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class='fa-solid fa-gas-pump'></i> Salvar Abastecimento</button>
  </div>
</form>
<script>
let kmAnt = 0;
document.getElementById('viatSel').addEventListener('change',function(){
  kmAnt = parseFloat(this.options[this.selectedIndex].dataset.km)||0;
  document.getElementById('kmAtual').value = kmAnt||'';
  calcConsumption();
});
function calcConsumption(){
  const km = parseFloat(document.getElementById('kmAtual').value)||0;
  const lit = parseFloat(document.getElementById('litros').value)||0;
  const val = parseFloat(document.getElementById('valorTotal').value)||0;
  document.getElementById('consumoCalc').textContent = (km&&lit&&km>kmAnt)?((km-kmAnt)/lit).toFixed(2)+' km/L':'—';
  document.getElementById('precoLitro').textContent  = (val&&lit)?(val/lit).toFixed(4):'—';
}
</script>
