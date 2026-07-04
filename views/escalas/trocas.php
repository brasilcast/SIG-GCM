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
 */$pageTitle='Trocas de Plantão'; ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-arrows-rotate"></i> Trocas de Plantão</h2><div class="breadcrumb">Escalas › Trocas</div></div>
  <a href="<?=BASE_URL?>/escalas" class="btn btn-secondary">← Escalas</a>
</div>
<?php if(!empty($minhasEscalas)): ?>
<div class="table-wrapper" style="padding:1.25rem;margin-bottom:1rem">
  <h3 style="font-size:.9rem;margin-bottom:.85rem;color:var(--text-muted);text-transform:uppercase">Solicitar Troca</h3>
  <form method="POST" action="<?=BASE_URL?>/trocas/solicitar" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-end">
    <?=Auth::csrfField()?>
    <div class="form-group" style="margin-bottom:0;min-width:200px">
      <label class="form-label">Minha Escala (Origem)</label>
      <select name="escala_origem_id" class="form-control" required>
        <?php foreach($minhasEscalas as $e): ?>
        <option value="<?=$e['id']?>"><?=date('d/m/Y',strtotime($e['data']))?> – <?=ucfirst($e['turno'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group" style="margin-bottom:0;min-width:200px">
      <label class="form-label">Substituto</label>
      <select name="substituto_id" id="substituto_select" class="form-control" required onchange="atualizarDestinos()">
        <option value="">- Selecione -</option>
        <?php foreach($agentes as $a): ?><option value="<?=$a['id']?>"><?=htmlspecialchars($a['nome'])?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group" style="margin-bottom:0;min-width:200px">
      <label class="form-label">Escala do Colega (Destino)</label>
      <select name="escala_destino_id" id="escala_destino_select" class="form-control" required>
        <option value="">- Selecione o substituto -</option>
      </select>
    </div>
    <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
      <label class="form-label">Motivo</label>
      <input type="text" name="motivo" class="form-control" placeholder="Motivo da troca..." required>
    </div>
    <button type="submit" class="btn btn-primary">Solicitar</button>
  </form>
</div>
<?php endif; ?>
<div class="table-wrapper">
  <div class="table-header"><h3>Solicitações</h3></div>
  <div class="table-responsive"><table>
    <thead><tr><th>Data Escala</th><th>Solicitante</th><th>Substituto</th><th>Status</th><?php if(Auth::can('troca.aprovar')): ?><th>Ação</th><?php endif; ?></tr></thead>
    <tbody>
    <?php if(empty($ativas)): ?><tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--text-muted)">Nenhuma solicitação.</td></tr>
    <?php else: foreach($ativas as $t):
      $sb=['pendente'=>'badge-warning','aprovada'=>'badge-success','negada'=>'badge-danger'][$t['status']]??'badge-secondary';
    ?>
      <tr>
        <td style="font-size:.85rem;line-height:1.4">
            <span style="color:var(--text-muted)">Origem:</span> <strong><?=date('d/m/Y',strtotime($t['data_origem']))?> (<?=ucfirst($t['turno_origem'])?>)</strong><br>
            <span style="color:var(--text-muted)">Destino:</span> <strong><?=date('d/m/Y',strtotime($t['data_destino']))?> (<?=ucfirst($t['turno_destino'])?>)</strong>
        </td>
        <td><?=htmlspecialchars($t['solicitante'])?></td>
        <td><?=htmlspecialchars($t['substituto'])?></td>
        <td><span class="badge <?=$sb?>"><?=ucfirst($t['status'])?></span></td>
        <?php if(Auth::can('troca.aprovar') && $t['status']==='pendente'): ?>
        <td>
          <form method="POST" action="<?=BASE_URL?>/trocas/<?=$t['id']?>/aprovar" style="display:inline">
            <?=Auth::csrfField()?><input type="hidden" name="decisao" value="aprovada">
            <button class="btn btn-success btn-sm"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Aprovar</button>
          </form>
          <form method="POST" action="<?=BASE_URL?>/trocas/<?=$t['id']?>/aprovar" style="display:inline">
            <?=Auth::csrfField()?><input type="hidden" name="decisao" value="negada">
            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-circle-xmark" style="color:var(--danger)"></i> Negar</button>
          </form>
        </td>
        <?php elseif(Auth::can('troca.aprovar')): ?><td>—</td><?php endif; ?>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table></div>
</div>

<script>
const escalasJSON = <?= $jsonEscalas ?? '{}' ?>;
function atualizarDestinos() {
    const subId = document.getElementById('substituto_select').value;
    const dest = document.getElementById('escala_destino_select');
    dest.innerHTML = '';
    
    if (!subId || !escalasJSON[subId] || escalasJSON[subId].length === 0) {
        dest.innerHTML = '<option value="">- Nenhuma escala futura encontrada -</option>';
        return;
    }
    
    dest.innerHTML = '<option value="">- Selecione a escala alvo -</option>';
    escalasJSON[subId].forEach(e => {
        let parts = e.data.split('-');
        let dStr = parts[2] + '/' + parts[1] + '/' + parts[0];
        let tStr = e.turno.charAt(0).toUpperCase() + e.turno.slice(1);
        dest.innerHTML += `<option value="${e.id}">${dStr} – ${tStr}</option>`;
    });
}
// Init on load if any
atualizarDestinos();
</script>
