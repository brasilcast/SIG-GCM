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
 */$pageTitle='Escalas — Semana '.htmlspecialchars($semana); ?>
<div class="page-header">
  <div><h2><i class="fa-solid fa-calendar-days"></i> Escalas</h2><div class="breadcrumb">Escalas › Semana <?=date('d/m',strtotime($seg)).' – '.date('d/m/Y',strtotime($dom))?></div></div>
  <div style="display:flex;gap:.5rem">
    <?php $prevW=date('Y-W',strtotime('-1 week',strtotime($seg))); $nextW=date('Y-W',strtotime('+1 week',strtotime($seg))); ?>
    <a href="?semana=<?=$prevW?>" class="btn btn-secondary">‹ Semana Anterior</a>
    <a href="?semana=<?=$nextW?>" class="btn btn-secondary">Próxima Semana ›</a>
    <a href="<?=BASE_URL?>/trocas" class="btn btn-secondary"><i class="fa-solid fa-arrows-rotate"></i> Trocas</a>
    <a href="<?=BASE_URL?>/escalas/pdf?semana=<?=htmlspecialchars($semana)?>" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print"></i> Gerar PDF</a>
  </div>
</div>

<?php if(Auth::can('escala.criar')): ?>
<!-- Formulários de Escala -->
<div class="table-wrapper" style="margin-bottom:1.5rem">
  
  <!-- Add Agente à Equipe -->
  <div style="padding:1rem;border-bottom:1px solid var(--border)">
    <h3 style="font-size:.9rem;color:var(--text-muted);margin-bottom:.75rem"><i class="fa-solid fa-users-gear"></i> Vincular Agente à Equipe</h3>
    <form method="POST" action="<?=BASE_URL?>/escalas/equipe" style="display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap">
      <?=Auth::csrfField()?>
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
        <label class="form-label" style="font-size:.7rem">Agente</label>
        <select name="agente_id" class="form-control" style="padding:.4rem;font-size:.85rem" required>
          <option value="">— Selecione um agente —</option>
          <?php foreach($agentes as $a): ?>
            <option value="<?=$a['id']?>"><?=htmlspecialchars($a['nome'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0;width:150px">
        <label class="form-label" style="font-size:.7rem">Equipe Alvo</label>
        <select name="equipe" class="form-control" style="padding:.4rem;font-size:.85rem" required>
          <option value="alfa">ALFA</option>
          <option value="bravo">BRAVO</option>
          <option value="charlie">CHARLIE</option>
          <option value="delta">DELTA</option>
          <option value="adm">ADM (Administrativo)</option>
          <option value="">Nenhuma (Remover)</option>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm" style="padding:.45rem 1rem">Vincular</button>
    </form>
  </div>

  <!-- Escala Avulsa -->
  <div style="padding:1rem;border-bottom:1px solid var(--border)">
    <h3 style="font-size:.9rem;color:var(--text-muted);margin-bottom:.75rem"><i class="fa-solid fa-plus"></i> Adicionar Escala Avulsa</h3>
    <form method="POST" action="<?=BASE_URL?>/escalas" style="display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap">
      <?=Auth::csrfField()?>
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px">
        <label class="form-label" style="font-size:.7rem">Agente / Equipe Inteira</label>
        <select name="agente_id" id="avulsaAgente" class="form-control" style="padding:.4rem;font-size:.85rem" required>
          <optgroup label="Equipes Inteiras">
            <option value="equipe_alfa">EQUIPE ALFA (Todos)</option>
            <option value="equipe_bravo">EQUIPE BRAVO (Todos)</option>
            <option value="equipe_charlie">EQUIPE CHARLIE (Todos)</option>
            <option value="equipe_delta">EQUIPE DELTA (Todos)</option>
          </optgroup>
          <optgroup label="Agentes Individuais">
            <?php foreach($agentes as $a): ?>
              <option value="<?=$a['id']?>"><?=htmlspecialchars($a['nome'])?></option>
            <?php endforeach; ?>
          </optgroup>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label" style="font-size:.7rem">Data</label>
        <input type="date" name="data" class="form-control" style="padding:.4rem;font-size:.85rem" required value="<?=date('Y-m-d')?>">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label" style="font-size:.7rem">Turno</label>
        <select name="turno" id="avulsaTurno" class="form-control" style="padding:.4rem;font-size:.85rem" required>
          <option value="diurno">Diurno</option>
          <option value="noturno">Noturno</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0;width:90px">
        <label class="form-label" style="font-size:.7rem">Entrada</label>
        <input type="time" name="entrada" id="avulsaEntrada" class="form-control" style="padding:.4rem;font-size:.85rem" required value="06:00" onchange="calcSaida(this.value, 'avulsaSaida')">
      </div>
      <div class="form-group" style="margin-bottom:0;width:90px">
        <label class="form-label" style="font-size:.7rem">Saída</label>
        <input type="time" name="saida" id="avulsaSaida" class="form-control" style="padding:.4rem;font-size:.85rem" required value="18:00">
      </div>
      <button type="submit" class="btn btn-primary btn-sm" style="padding:.45rem 1rem">+ Escalar</button>
    </form>
  </div>

  <!-- Gerador 12x36 -->
  <div style="padding:1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
      <h3 style="font-size:.9rem;color:var(--text-muted);margin:0"><i class="fa-solid fa-wand-magic-sparkles"></i> Gerador Automático (12x36)</h3>
      <?php if(Auth::can('escala.excluir') || Auth::can('escala.editar')): ?>
      <form method="POST" action="<?=BASE_URL?>/escalas/zerar-12x36" style="margin:0" onsubmit="return confirm('ATENÇÃO: Isso apagará TODAS as escalas do regime 12x36 do sistema. Tem certeza absoluta?');">
        <?=Auth::csrfField()?>
        <button type="submit" class="btn btn-sm" style="background:var(--dev-red);color:#fff;padding:.3rem .8rem;font-size:.75rem">
          <i class="fa-solid fa-trash-can"></i> Zerar 12x36
        </button>
      </form>
      <?php endif; ?>
    </div>
    <form method="POST" action="<?=BASE_URL?>/escalas/gerar-12x36" style="display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap">
      <?=Auth::csrfField()?>
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
        <label class="form-label" style="font-size:.7rem">Equipe</label>
        <select name="equipe" id="genEquipe" class="form-control" style="padding:.4rem;font-size:.85rem" required>
          <option value="alfa">ALFA</option>
          <option value="bravo">BRAVO</option>
          <option value="charlie">CHARLIE</option>
          <option value="delta">DELTA</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0;width:90px">
        <label class="form-label" style="font-size:.7rem">Entrada</label>
        <input type="time" name="entrada" id="genEntrada" class="form-control" style="padding:.4rem;font-size:.85rem" required value="06:00" onchange="calcSaida(this.value, 'genSaida')">
      </div>
      <div class="form-group" style="margin-bottom:0;width:90px">
        <label class="form-label" style="font-size:.7rem">Saída</label>
        <input type="time" name="saida" id="genSaida" class="form-control" style="padding:.4rem;font-size:.85rem" required value="18:00">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label" style="font-size:.7rem">Início 1º Plantão</label>
        <input type="date" name="data_inicial" class="form-control" style="padding:.4rem;font-size:.85rem" required value="<?=date('Y-m-d')?>">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label" style="font-size:.7rem">Fim do Ciclo</label>
        <input type="date" name="data_final" class="form-control" style="padding:.4rem;font-size:.85rem" required value="<?=date('Y-m-t')?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm" style="padding:.45rem 1rem"><i class="fa-solid fa-bolt"></i> Gerar Lote</button>
    </form>
  </div>
</div>

<script>
    function calcSaida(entrada, saidaId) {
        if(!entrada) return;
        let [h, m] = entrada.split(':');
        let saidaH = (parseInt(h) + 12) % 24;
        let saidaHStr = saidaH.toString().padStart(2, '0');
        document.getElementById(saidaId).value = saidaHStr + ':' + m;
    }

    // Configura os horários automáticos para o Gerador
    document.getElementById('genEquipe').addEventListener('change', function() {
        let ent = document.getElementById('genEntrada');
        if(this.value === 'alfa' || this.value === 'charlie') {
            ent.value = '06:00';
        } else if(this.value === 'bravo' || this.value === 'delta') {
            ent.value = '18:00';
        }
        calcSaida(ent.value, 'genSaida');
    });

    // Configura os horários automáticos para a Escala Avulsa
    document.getElementById('avulsaAgente').addEventListener('change', function() {
        let v = this.value;
        let ent = document.getElementById('avulsaEntrada');
        if(v === 'equipe_alfa' || v === 'equipe_charlie') {
            document.getElementById('avulsaTurno').value = 'diurno';
            ent.value = '06:00';
        } else if(v === 'equipe_bravo' || v === 'equipe_delta') {
            document.getElementById('avulsaTurno').value = 'noturno';
            ent.value = '18:00';
        }
        calcSaida(ent.value, 'avulsaSaida');
    });

    // Inicializa os scripts
    document.getElementById('genEquipe').dispatchEvent(new Event('change'));
    document.getElementById('avulsaAgente').dispatchEvent(new Event('change'));
</script>
<?php endif; ?>

<!-- Tabela semanal -->
<?php
$dias=['Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'];
$diasDatas=[];
for($d=0;$d<7;$d++){
  $diasDatas[]=date('Y-m-d',strtotime("+$d day",strtotime($seg)));
}
$porDia=[];
foreach($escalas as $e){$porDia[$e['data']][]=$e;}
?>
<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:.5rem">
  <?php foreach($diasDatas as $i=>$dt): ?>
  <div class="table-wrapper" style="padding:.85rem;<?=$dt===date('Y-m-d')?'border-color:var(--primary);':''?>">
    <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);margin-bottom:.5rem;text-transform:uppercase"><?=$dias[$i]?></div>
    <div style="font-size:.9rem;font-weight:600;margin-bottom:.75rem"><?=date('d/m',$dt?strtotime($dt):time())?></div>
    <?php if(empty($porDia[$dt])): ?>
      <div style="font-size:.75rem;color:var(--text-muted);text-align:center;padding:.5rem">Sem escala</div>
    <?php else: foreach($porDia[$dt] as $e): ?>
      <?php $turnoCor=['diurno'=>'badge-info','noturno'=>'badge-primary'][$e['turno']]??'badge-secondary'; ?>
      <div style="background:var(--surface-2);border-radius:4px;padding:.4rem .5rem;margin-bottom:.45rem;font-size:.75rem;border-left:3px solid var(<?=$e['turno']==='diurno'?'--info':'--primary'?>);position:relative">
        <div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:20px"><?=htmlspecialchars($e['nome'])?></div>
        
        <?php if(Auth::can('escala.editar')): ?>
        <div style="position:absolute;top:4px;right:4px;display:flex;gap:.25rem">
          <!-- Botão de Edição Rápida -->
          <button onclick="openEditModal(<?=$e['id']?>, '<?=$e['turno']?>', '<?=$e['entrada']?substr($e['entrada'],0,5):''?>', '<?=$e['saida']?substr($e['saida'],0,5):''?>', <?=$e['agente_id']?>, '<?=$e['data']?>')" style="background:none;border:none;color:var(--text-muted);cursor:pointer;padding:2px" title="Editar Plantão"><i class="fa-solid fa-pen"></i></button>
          
          <!-- Botão de Exluir -->
          <form method="POST" action="<?=BASE_URL?>/escalas/<?=$e['id']?>/excluir" style="display:inline" onsubmit="return confirm('Tem certeza que deseja apagar essa escala permanentemente?');">
            <?=Auth::csrfField()?>
            <button type="submit" style="background:none;border:none;color:var(--dev-red);cursor:pointer;padding:2px" title="Excluir Plantão"><i class="fa-solid fa-trash"></i></button>
          </form>
        </div>
        <?php endif; ?>

        <div style="margin-top:.35rem;display:flex;align-items:center;gap:.35rem">
          <span class="badge <?=$turnoCor?>" style="font-size:.65rem"><?=ucfirst($e['turno'])?></span>
          <?php if($e['entrada']): ?>
            <span style="font-size:.7rem;color:var(--text-muted);background:var(--surface);padding:2px 4px;border-radius:3px;border:1px solid var(--border)">
              <i class="fa-regular fa-clock"></i> <?=substr($e['entrada'],0,5)?> <?php if($e['saida']): ?>– <?=substr($e['saida'],0,5)?><?php endif; ?>
            </span>
          <?php endif; ?>
          <?php if(!empty($e['prefixo'])): ?><span class="badge badge-secondary" style="font-size:.65rem"><?=$e['prefixo']?></span><?php endif; ?>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<!-- Modal Edição Escala -->
<?php if(Auth::can('escala.editar')): ?>
<div id="modalEditEscala" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center">
  <div class="modal-content" style="background:var(--surface);padding:1.5rem;border-radius:8px;width:100%;max-width:350px;box-shadow:0 10px 25px rgba(0,0,0,0.5)">
    <h3 style="margin-bottom:1rem;font-size:1.1rem;color:var(--text)"><i class="fa-solid fa-pen-to-square"></i> Editar Escala</h3>
    <form id="formEditEscala" method="POST" action="">
      <?=Auth::csrfField()?>
      <div class="form-group">
        <label class="form-label">Agente</label>
        <select name="agente_id" id="editAgente" class="form-control" required>
          <option value="">— Selecione um agente —</option>
          <?php foreach($agentes as $a): ?>
            <option value="<?=$a['id']?>"><?=htmlspecialchars($a['nome'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="display:flex;gap:.5rem">
        <div class="form-group" style="flex:1">
          <label class="form-label">Data</label>
          <input type="date" name="data" id="editData" class="form-control" required>
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Turno</label>
          <select name="turno" id="editTurno" class="form-control" required>
            <option value="diurno">Diurno</option>
            <option value="noturno">Noturno</option>
            <option value="folga">Folga</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:.5rem">
        <div class="form-group" style="flex:1">
          <label class="form-label">Entrada</label>
          <input type="time" name="entrada" id="editEntrada" class="form-control" required onchange="calcSaida(this.value, 'editSaida')">
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Saída</label>
          <input type="time" name="saida" id="editSaida" class="form-control" required>
        </div>
      </div>
      <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1.5rem">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalEditEscala').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>
<script>
function openEditModal(escalaId, turno, entrada, saida, agenteId, data) {
    document.getElementById('formEditEscala').action = '<?=BASE_URL?>/escalas/' + escalaId + '/editar';
    document.getElementById('editAgente').value = agenteId;
    document.getElementById('editData').value = data;
    document.getElementById('editTurno').value = turno;
    document.getElementById('editEntrada').value = entrada;
    
    if (saida) {
        document.getElementById('editSaida').value = saida;
    } else if (entrada) {
        calcSaida(entrada, 'editSaida');
    }
    
    document.getElementById('modalEditEscala').style.display = 'flex';
}
</script>
<?php endif; ?>

<!-- ============================================================ -->
<!-- SEÇÃO ESCALA ADMINISTRATIVA (ADM) — 5x2, Segunda a Sexta    -->
<!-- ============================================================ -->
<div style="margin-top:2.5rem">
  <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem">
    <div style="flex:1;height:1px;background:var(--border)"></div>
    <h3 style="font-size:1rem;font-weight:700;color:var(--text);white-space:nowrap">
      <i class="fa-solid fa-building-user"></i> Escala Administrativa (ADM)
    </h3>
    <div style="flex:1;height:1px;background:var(--border)"></div>
  </div>

  <?php if(Auth::can('escala.criar')): ?>
  <div class="table-wrapper" style="margin-bottom:1.25rem">

    <!-- Config Horários ADM -->
    <div style="padding:1rem;border-bottom:1px solid var(--border)">
      <h4 style="font-size:.9rem;color:var(--text-muted);margin-bottom:.75rem">
        <i class="fa-solid fa-clock"></i> Horários Padrão ADM (editável)
      </h4>
      <form method="POST" action="<?=BASE_URL?>/escalas/config-adm" style="display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap">
        <?=Auth::csrfField()?>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-size:.7rem">Entrada Manhã</label>
          <input type="time" name="entrada_manha" class="form-control" style="padding:.4rem;font-size:.85rem" value="<?=htmlspecialchars($configAdm['entrada_manha']??'07:30')?>">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-size:.7rem">Saída Manhã</label>
          <input type="time" name="saida_manha" class="form-control" style="padding:.4rem;font-size:.85rem" value="<?=htmlspecialchars($configAdm['saida_manha']??'11:30')?>">
        </div>
        <div style="display:flex;align-items:flex-end;padding-bottom:.4rem;color:var(--text-muted);font-size:.8rem">almoço</div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-size:.7rem">Entrada Tarde</label>
          <input type="time" name="entrada_tarde" class="form-control" style="padding:.4rem;font-size:.85rem" value="<?=htmlspecialchars($configAdm['entrada_tarde']??'13:00')?>">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-size:.7rem">Saída Tarde</label>
          <input type="time" name="saida_tarde" class="form-control" style="padding:.4rem;font-size:.85rem" value="<?=htmlspecialchars($configAdm['saida_tarde']??'17:00')?>">
        </div>
        <button type="submit" class="btn btn-secondary btn-sm" style="padding:.45rem 1rem">
          <i class="fa-solid fa-floppy-disk"></i> Salvar Horários
        </button>
      </form>
    </div>

    <!-- Gerador ADM -->
    <div style="padding:1rem">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
        <h4 style="font-size:.9rem;color:var(--text-muted);margin:0">
          <i class="fa-solid fa-wand-magic-sparkles"></i> Gerar Escala ADM (5x2 — Seg a Sex)
        </h4>
        <?php if(Auth::can('escala.excluir') || Auth::can('escala.editar')): ?>
        <form method="POST" action="<?=BASE_URL?>/escalas/zerar-adm" style="margin:0" onsubmit="return confirm('ATENÇÃO: Isso apagará TODAS as escalas administrativas (ADM) do sistema. Tem certeza absoluta?');">
          <?=Auth::csrfField()?>
          <button type="submit" class="btn btn-sm" style="background:var(--dev-red);color:#fff;padding:.3rem .8rem;font-size:.75rem">
            <i class="fa-solid fa-trash-can"></i> Zerar ADM
          </button>
        </form>
        <?php endif; ?>
      </div>
      <form method="POST" action="<?=BASE_URL?>/escalas/gerar-adm" style="display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap">
        <?=Auth::csrfField()?>
        <!-- horários ocultos com os valores salvos -->
        <input type="hidden" name="entrada_manha"  value="<?=htmlspecialchars($configAdm['entrada_manha']??'07:30')?>">
        <input type="hidden" name="saida_manha"    value="<?=htmlspecialchars($configAdm['saida_manha']??'11:30')?>">
        <input type="hidden" name="entrada_tarde"  value="<?=htmlspecialchars($configAdm['entrada_tarde']??'13:00')?>">
        <input type="hidden" name="saida_tarde"    value="<?=htmlspecialchars($configAdm['saida_tarde']??'17:00')?>">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-size:.7rem">Data Inicial</label>
          <input type="date" name="data_inicial" class="form-control" style="padding:.4rem;font-size:.85rem" required value="<?=date('Y-m-d')?>">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" style="font-size:.7rem">Data Final</label>
          <input type="date" name="data_final" class="form-control" style="padding:.4rem;font-size:.85rem" required value="<?=date('Y-m-t')?>">
        </div>
        <button type="submit" class="btn btn-secondary btn-sm" style="padding:.45rem 1rem">
          <i class="fa-solid fa-bolt"></i> Gerar Escala ADM
        </button>
      </form>
    </div>

  </div>
  <?php endif; ?>

  <!-- Tabela ADM da semana vigente -->
  <?php
  $diasNome = ['Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'];
  $diasAdmDatas = [];
  for($d=0; $d<7; $d++) { // seg a dom
    $diasAdmDatas[] = date('Y-m-d', strtotime("+$d day", strtotime($seg)));
  }
  $admPorDia = [];
  foreach($escalasAdm as $ea) { $admPorDia[$ea['data']][] = $ea; }
  ?>

  <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:.5rem">
    <?php foreach($diasAdmDatas as $i=>$dt): ?>
    <div class="table-wrapper" style="padding:.85rem;<?=$dt===date('Y-m-d')?'border-color:var(--primary);':''?>">
      <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);margin-bottom:.5rem;text-transform:uppercase"><?=$diasNome[$i]?></div>
      <div style="font-size:.9rem;font-weight:600;margin-bottom:.75rem"><?=date('d/m',$dt?strtotime($dt):time())?></div>
      <?php if(empty($admPorDia[$dt])): ?>
        <div style="font-size:.75rem;color:var(--text-muted);text-align:center;padding:.5rem">Sem escala</div>
      <?php else: foreach($admPorDia[$dt] as $ea): ?>
        <div style="background:var(--surface-2);border-radius:4px;padding:.4rem .5rem;margin-bottom:.45rem;font-size:.75rem;border-left:3px solid var(--success);position:relative">
          <div style="font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:20px"><?=htmlspecialchars($ea['nome'])?></div>
          
          <?php if(Auth::can('escala.editar')): ?>
          <div style="position:absolute;top:4px;right:4px;display:flex;gap:.25rem">
            <button onclick="openEditModalAdm(<?=$ea['id']?>, '<?=$ea['turno']?>', '<?=$ea['entrada']?substr($ea['entrada'],0,5):'07:30'?>', '<?=$ea['saida']?substr($ea['saida'],0,5):'11:30'?>', '<?=$ea['entrada_tarde']?substr($ea['entrada_tarde'],0,5):'13:00'?>', '<?=$ea['saida_tarde']?substr($ea['saida_tarde'],0,5):'17:00'?>', <?=$ea['agente_id']?>, '<?=$ea['data']?>')" style="background:none;border:none;color:var(--text-muted);cursor:pointer;padding:2px" title="Editar Plantão ADM"><i class="fa-solid fa-pen"></i></button>
            <form method="POST" action="<?=BASE_URL?>/escalas/<?=$ea['id']?>/excluir" style="display:inline" onsubmit="return confirm('Tem certeza que deseja apagar essa escala permanentemente?');">
              <?=Auth::csrfField()?>
              <button type="submit" style="background:none;border:none;color:var(--dev-red);cursor:pointer;padding:2px" title="Excluir Plantão"><i class="fa-solid fa-trash"></i></button>
            </form>
          </div>
          <?php endif; ?>

          <div style="margin-top:.35rem;font-size:.7rem;color:var(--text-muted)">
            <i class="fa-regular fa-clock"></i>
            <?=isset($ea['entrada'])?substr($ea['entrada'],0,5):'07:30'?> – <?=isset($ea['saida'])?substr($ea['saida'],0,5):'11:30'?>
            &nbsp;|&nbsp;
            <?=isset($ea['entrada_tarde'])?substr($ea['entrada_tarde'],0,5):'13:00'?> – <?=isset($ea['saida_tarde'])?substr($ea['saida_tarde'],0,5):'17:00'?>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- Modal Edição Escala ADM -->
<?php if(Auth::can('escala.editar')): ?>
<div id="modalEditEscalaAdm" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center">
  <div class="modal-content" style="background:var(--surface);padding:1.5rem;border-radius:8px;width:100%;max-width:350px;box-shadow:0 10px 25px rgba(0,0,0,0.5)">
    <h3 style="margin-bottom:1rem;font-size:1.1rem;color:var(--text)"><i class="fa-solid fa-pen-to-square"></i> Editar Escala ADM</h3>
    <form id="formEditEscalaAdm" method="POST" action="">
      <?=Auth::csrfField()?>
      <input type="hidden" name="turno" value="administrativo">
      <div class="form-group">
        <label class="form-label">Agente</label>
        <select name="agente_id" id="editAgenteAdm" class="form-control" required>
          <option value="">— Selecione um agente —</option>
          <?php foreach($agentes as $a): ?>
            <option value="<?=$a['id']?>"><?=htmlspecialchars($a['nome'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Data</label>
        <input type="date" name="data" id="editDataAdm" class="form-control" required>
      </div>
      <div style="display:flex;gap:.5rem">
        <div class="form-group" style="flex:1">
          <label class="form-label">Entrada Manhã</label>
          <input type="time" name="entrada" id="editEntradaAdm" class="form-control" required>
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Saída Manhã</label>
          <input type="time" name="saida" id="editSaidaAdm" class="form-control" required>
        </div>
      </div>
      <div style="display:flex;gap:.5rem">
        <div class="form-group" style="flex:1">
          <label class="form-label">Entrada Tarde</label>
          <input type="time" name="entrada_tarde" id="editEntradaTardeAdm" class="form-control" required>
        </div>
        <div class="form-group" style="flex:1">
          <label class="form-label">Saída Tarde</label>
          <input type="time" name="saida_tarde" id="editSaidaTardeAdm" class="form-control" required>
        </div>
      </div>
      <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1.5rem">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalEditEscalaAdm').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>
<script>
function openEditModalAdm(escalaId, turno, entM, saiM, entT, saiT, agenteId, data) {
    document.getElementById('formEditEscalaAdm').action = '<?=BASE_URL?>/escalas/' + escalaId + '/editar';
    document.getElementById('editAgenteAdm').value = agenteId;
    document.getElementById('editDataAdm').value = data;
    document.getElementById('editEntradaAdm').value = entM;
    document.getElementById('editSaidaAdm').value = saiM;
    document.getElementById('editEntradaTardeAdm').value = entT;
    document.getElementById('editSaidaTardeAdm').value = saiT;
    document.getElementById('modalEditEscalaAdm').style.display = 'flex';
}
</script>
<?php endif; ?>
