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
 */$pageTitle = 'Talões de Ocorrência'; ?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-file-lines"></i> Talões de Ocorrência</h2>
    <div class="breadcrumb">Operacional › Talões</div>
  </div>
  <?php if(Auth::can('taloes.criar')): ?>
  <a href="<?=BASE_URL?>/taloes/novo" class="btn btn-primary">+ Novo Talão</a>
  <?php endif; ?>
</div>

<!-- Dashboard de Talões Abertos -->
<?php if(!empty($taloesAbertos)): ?>
<div style="margin-bottom:1.5rem">
  <h3 style="font-size:1rem;color:var(--danger);margin-bottom:.75rem;text-transform:uppercase;letter-spacing:.05em">
    <i class="fa-solid fa-triangle-exclamation"></i> Ocorrências em Andamento (<?=count($taloesAbertos)?>)
  </h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1rem">
    <?php foreach($taloesAbertos as $ta): ?>
      <?php
        $qthta = explode('-', $ta['endereco'] ?: '—')[0];
        $eqpta = ($ta['tipo_equipe'] === 'pop') ? 'POP' : ($ta['viatura_prefixo'] ?: 'VTR Não Inf.');
        $natta = ($ta['natureza_codigo'] ? $ta['natureza_codigo'].' - ' : '') . ($ta['natureza_nome'] ?: 'Não Informada');
      ?>
      <div style="background:var(--surface);border-left:4px solid var(--danger);border-radius:var(--radius);padding:1rem;box-shadow:var(--shadow);cursor:pointer;transition:transform 0.2s" onclick="location.href='<?=BASE_URL?>/taloes/<?=$ta['id']?>'" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.5rem">
          <strong style="font-family:monospace;font-size:1.1rem;color:var(--text)">#<?=htmlspecialchars($ta['numero'])?></strong>
          <span class="talao-timer" data-abertura="<?=date('Y-m-d H:i:s', strtotime($ta['data_hora_abertura']))?>" style="font-weight:bold;font-size:.9rem;background:#fee2e2;padding:.2rem .5rem;border-radius:4px">
            <i class="fa-solid fa-spinner fa-spin"></i>
          </span>
        </div>

        <div style="font-size:.85rem;color:var(--text-muted);margin-bottom:.3rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="<?=htmlspecialchars($natta)?>">
          <i class="fa-solid fa-tag" style="width:16px;text-align:center"></i> <strong style="color:var(--text)"><?=htmlspecialchars($natta)?></strong>
        </div>
        
        <div style="font-size:.85rem;color:var(--text-muted);margin-bottom:.3rem">
          <i class="fa-solid fa-location-dot" style="width:16px;text-align:center"></i> <?=htmlspecialchars(trim($qthta))?>
        </div>
        
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--border);font-size:.8rem">
          <span><i class="fa-regular fa-clock"></i> QTRI: <strong><?=date('H:i',strtotime($ta['data_hora_abertura']))?></strong></span>
          <span class="badge badge-info"><i class="fa-solid fa-car-side"></i> <?=htmlspecialchars($eqpta)?></span>
        </div>

      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="table-wrapper" style="margin-bottom:1rem">
  <div style="padding:.85rem 1.25rem">
    <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
      <input type="text" name="q" class="form-control" placeholder="Nº, bairro, narrativa..." value="<?=htmlspecialchars($busca??'')?>" style="max-width:230px">
      <select name="status" class="form-control" style="max-width:160px">
        <option value="">Todos os status</option>
        <option value="aberto" <?=($status??'')==='aberto'?'selected':''?>><i class="fa-solid fa-folder-open"></i> Aberto</option>
        <option value="fechado" <?=($status??'')==='fechado'?'selected':''?>><i class="fa-solid fa-lock"></i> Fechado</option>
      </select>
      <select name="natureza" class="form-control" style="max-width:200px">
        <option value="">Todas as naturezas</option>
        <?php $grupo=''; foreach(($naturezas??[]) as $n): 
          if($n['grupo']!==$grupo){ $grupo=$n['grupo']; echo "<optgroup label='".htmlspecialchars($grupo)."'>"; } 
        ?>
        <option value="<?=$n['id']?>" <?=($nat??'')==$n['id']?'selected':''?>><?=htmlspecialchars(($n['codigo']?$n['codigo'].' - ':'').$n['nome'])?></option>
        <?php endforeach; echo "</optgroup>"; ?>
      </select>
      <input type="date" name="data" class="form-control" value="<?=htmlspecialchars($data??'')?>" style="max-width:150px">
      <button class="btn btn-secondary">Filtrar</button>
      <a href="<?=BASE_URL?>/taloes" class="btn btn-secondary"><i class='fa-solid fa-xmark'></i></a>
      <a href="<?=BASE_URL?>/taloes/mapa-calor" class="btn btn-secondary" style="margin-left:auto"><i class='fa-solid fa-map-location-dot'></i> Mapa de Calor</a>
    </form>
  </div>
</div>

<div class="table-wrapper">
  <div class="table-header">
    <h3>Total: <?=$pag['total']?> talão(ões)</h3>
  </div>
  <div class="table-responsive">
    <table>
      <thead><tr><th>Nº</th><th>QTRI</th><th>KMI</th><th>QTH</th><th>QTRF</th><th>KMF</th><th>Nat</th><th>Equipe</th><th>Status</th><th>Ação</th></tr></thead>
      <tbody>
      <?php if(empty($taloes)): ?>
        <tr><td colspan="10" style="text-align:center;padding:2rem;color:var(--text-muted)">Nenhum talão encontrado.</td></tr>
      <?php else: foreach($taloes as $t): ?>
        <?php
          $stBadge = ['aberto'=>'badge-danger','fechado'=>'badge-success'];
          $stIcon  = ['aberto'=>'<i class="fa-solid fa-folder-open"></i>','fechado'=>'<i class="fa-solid fa-lock"></i>'];
          
          // Formata Endereço (tenta pegar até a primeira vírgula ou hífen para focar na rua/numero)
          $qthFull = $t['endereco'] ?: '—';
          $qth = explode('-', $qthFull)[0];
          
          // Formata Equipe
          $equipeDisp = ($t['tipo_equipe'] === 'pop') ? '<span class="badge badge-secondary">POP</span>' : ($t['viatura_prefixo'] ? '<span class="badge badge-info">'.$t['viatura_prefixo'].'</span>' : '—');
        ?>
        <tr style="cursor:pointer" onclick="location.href='<?=BASE_URL?>/taloes/<?=$t['id']?>'">
          <td><strong style="font-family:monospace"><?=htmlspecialchars($t['numero'])?></strong></td>
          <td style="white-space:nowrap"><?=date('H:i',strtotime($t['data_hora_abertura']))?></td>
          <td><?=$t['km_inicial'] ? number_format($t['km_inicial'],0,'.',',') : '—'?></td>
          <td title="<?=htmlspecialchars($qthFull)?>"><?=htmlspecialchars(trim($qth))?></td>
          <td style="white-space:nowrap"><?=$t['data_hora_fechamento'] ? date('H:i',strtotime($t['data_hora_fechamento'])) : '—'?></td>
          <td><?=$t['km_final'] ? number_format($t['km_final'],0,'.',',') : '—'?></td>
          <td><span class="badge badge-secondary" title="<?=htmlspecialchars($t['natureza_nome']??'')?>"><?=htmlspecialchars($t['natureza_codigo']??'—')?></span></td>
          <td><?=$equipeDisp?></td>
          <?php $sb=$stBadge[$t['status']]??'badge-secondary'; $si=($stIcon[$t['status']]??'').' '.ucfirst($t['status']); ?>
          <td><span class="badge <?=$sb?>"><?=$si?></span></td>
          <td onclick="event.stopPropagation()" style="white-space:nowrap">
            <a href="<?=BASE_URL?>/taloes/<?=$t['id']?>" class="btn btn-secondary btn-sm" title="Ver Detalhes"><i class="fa-solid fa-eye"></i></a>
            <?php if($t['status'] === 'aberto'): ?>
            <button type="button" class="btn btn-success btn-sm" title="Encerrar Talão" onclick="abrirModalFechar(event, <?=$t['id']?>, '<?=htmlspecialchars($t['numero'])?>', '<?=$t['tipo_equipe']?>', '<?=htmlspecialchars($t['km_inicial']??'')?>')">
              <i class="fa-solid fa-lock"></i>
            </button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($pag['total_pages']>1): ?>
  <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 1.25rem;border-top:1px solid var(--border);font-size:.85rem">
    <span style="color:var(--text-muted)">Página <?=$pag['current']?> de <?=$pag['total_pages']?></span>
    <div style="display:flex;gap:.35rem">
      <?php if($pag['has_prev']): ?><a href="?page=<?=$pag['current']-1?>&q=<?=urlencode($busca??'')?>&status=<?=$status??''?>&natureza=<?=$nat??''?>&data=<?=$data??''?>" class="btn btn-secondary btn-sm">‹</a><?php endif; ?>
      <?php if($pag['has_next']): ?><a href="?page=<?=$pag['current']+1?>&q=<?=urlencode($busca??'')?>&status=<?=$status??''?>&natureza=<?=$nat??''?>&data=<?=$data??''?>" class="btn btn-secondary btn-sm">›</a><?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Fechar Talão (Index) -->
<div id="modalFecharIndex" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:var(--surface);width:100%;max-width:400px;border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <h3 style="margin:0;font-size:1.1rem">Encerrar Talão <span id="modalTalaoNumero" style="font-family:monospace"></span></h3>
      <button type="button" onclick="document.getElementById('modalFecharIndex').style.display='none'" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text-muted)">&times;</button>
    </div>
    <form method="POST" id="formFecharIndex" action="" style="display:block;padding:1.25rem">
      <?=Auth::csrfField()?>
      <input type="hidden" name="status" value="fechado">
      
      <div style="margin-bottom:1rem">
        <label class="form-label" style="font-size:.85rem">Data de Encerramento</label>
        <input type="date" name="data_fechamento" class="form-control" value="<?=date('Y-m-d')?>" required>
      </div>
      <div style="margin-bottom:1rem">
        <label class="form-label" style="font-size:.85rem">Hora de Encerramento</label>
        <input type="time" name="hora_fechamento" class="form-control" value="<?=date('H:i')?>" required>
      </div>
      <div id="boxKmFinalModal" style="margin-bottom:1.5rem">
        <label class="form-label" style="font-size:.85rem">KM Final da Viatura</label>
        <input type="number" step="0.1" name="km_final" id="km_final_modal" class="form-control" placeholder="Ex: 12345.6">
      </div>
      <div style="display:flex;gap:.5rem;justify-content:flex-end">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalFecharIndex').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-lock"></i> Confirmar</button>
      </div>
    </form>
  </div>
</div>

<script>
function abrirModalFechar(e, id, numero, tipoEquipe, kmInicial) {
  e.stopPropagation(); // Previne o redicionamento da linha da tabela
  document.getElementById('modalTalaoNumero').innerText = numero;
  document.getElementById('formFecharIndex').action = '<?=BASE_URL?>/taloes/' + id + '/status';
  
  const boxKm = document.getElementById('boxKmFinalModal');
  const inputKm = document.getElementById('km_final_modal');
  if (tipoEquipe === 'pop') {
    boxKm.style.display = 'none';
    inputKm.removeAttribute('required');
    inputKm.value = '';
  } else {
    boxKm.style.display = 'block';
    inputKm.setAttribute('required', 'required');
    inputKm.value = kmInicial || '';
  }
  
  document.getElementById('modalFecharIndex').style.display = 'flex';
}

// Lógica do Timer em Tempo Real para os Talões em Aberto
function updateTimers() {
  const timers = document.querySelectorAll('.talao-timer');
  const now = new Date(); // Tempo local do navegador
  
  timers.forEach(timer => {
    // Pega a data de abertura armazenada no atributo (que está em formato UTC ou Servidor ISO)
    // Convertemos substituindo espaço por T para garantir parsing seguro no Safari/iOS
    const aberturaStr = timer.getAttribute('data-abertura').replace(' ', 'T'); 
    const abertaEm = new Date(aberturaStr);
    
    // Diferença em milissegundos
    let diffMs = now - abertaEm;
    if(diffMs < 0) diffMs = 0; // Proteção contra fuso horário ou servidores adiantados
    
    // Converter para minutos
    const diffMins = Math.floor(diffMs / 60000);
    
    // Atualizar no DOM com formatação (Ex: 5m, 1h 20m)
    let textoTimer = '';
    if (diffMins < 60) {
        textoTimer = diffMins + 'm';
        timer.style.color = diffMins > 30 ? 'var(--danger)' : 'var(--warning)';
    } else {
        const h = Math.floor(diffMins / 60);
        const m = diffMins % 60;
        textoTimer = h + 'h ' + m + 'm';
        timer.style.color = 'var(--danger)';
    }
    
    timer.innerHTML = '<i class="fa-solid fa-clock"></i> ' + textoTimer;
  });
}

// Rodar imediatamente e depois a cada 30 segundos
if(document.querySelectorAll('.talao-timer').length > 0) {
    updateTimers();
    setInterval(updateTimers, 30000); // 30s para ser fluido
}
</script>
