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
 */$pageTitle = 'Talão #'.htmlspecialchars($talao['numero']);
$stBadge = ['aberto'=>'badge-danger','fechado'=>'badge-success'];
$stLabels= ['aberto'=>'Aberto','fechado'=>'Fechado'];
?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-file-lines"></i> Talão <span style="font-family:monospace"><?=htmlspecialchars($talao['numero'])?></span></h2>
    <div class="breadcrumb">Talões › Detalhes</div>
  </div>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    <?php if(Auth::can('taloes.editar_todos')||(int)$talao['criado_por']===Auth::id()): ?>
    <a href="<?=BASE_URL?>/taloes/<?=$talao['id']?>/editar" class="btn btn-secondary"><i class="fa-solid fa-pencil"></i> Editar</a>
    <?php endif; ?>
    <a href="<?=BASE_URL?>/taloes/<?=$talao['id']?>/pdf" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print"></i> PDF</a>
    <a href="<?=BASE_URL?>/taloes" class="btn btn-secondary">← Voltar</a>
  </div>
</div>

<!-- Status rápido -->
<?php if(Auth::can('taloes.editar_parcial')||Auth::can('taloes.editar_todos')): ?>
<div style="border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:1.5rem;background:var(--surface-2);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
    <div>
        <span style="color:var(--text-muted);font-size:.85rem;display:block;margin-bottom:.25rem;text-transform:uppercase;letter-spacing:0.05em">Status Atual da Ocorrência</span>
        <?php $sb2=$stBadge[$talao['status']]??'badge-secondary'; ?>
        <span class="badge <?=$sb2?>" style="font-size:1.1rem;padding:.5rem 1rem"><?=$stLabels[$talao['status']]??$talao['status']?></span>
    </div>
    <div style="display:flex;gap:.75rem">
        <?php if($talao['status'] === 'aberto'): ?>
          <button type="button" class="btn btn-success" onclick="document.getElementById('modalFechar').style.display='flex'">
            <i class="fa-solid fa-lock"></i> Encerrar Talão
          </button>
        <?php else: ?>
          <form method="POST" action="<?=BASE_URL?>/taloes/<?=$talao['id']?>/status" style="display:inline">
            <?=Auth::csrfField()?>
            <input type="hidden" name="status" value="aberto">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja reabrir este talão?')">
              <i class="fa-solid fa-folder-open"></i> Reabrir Talão
            </button>
          </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;margin-bottom:1rem">
  <!-- Dados principais -->
  <div style="display:flex;flex-direction:column;gap:1rem">
    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.9rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.85rem">Identificação</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;font-size:.875rem">
        <div><span style="color:var(--text-muted)">Nº Talão:</span><br><strong style="font-family:monospace"><?=htmlspecialchars($talao['numero'])?></strong></div>
        <div><span style="color:var(--text-muted)">Natureza:</span><br><strong><?=htmlspecialchars(($talao['natureza_codigo']?$talao['natureza_codigo'].' - ':'').($talao['natureza_nome']??'—'))?></strong></div>
        <div><span style="color:var(--text-muted)">Data/Hora:</span><br><strong><?=date('d/m/Y H:i',strtotime($talao['data_hora_abertura']))?></strong></div>
        <?php if($talao['data_hora_fechamento']): ?>
        <div><span style="color:var(--text-muted)">Fechado em:</span><br><strong><?=date('d/m/Y H:i',strtotime($talao['data_hora_fechamento']))?></strong></div>
        <?php endif; ?>
        <div><span style="color:var(--text-muted)">Endereço:</span><br><strong><?=htmlspecialchars($talao['endereco']?:'—')?>, <?=htmlspecialchars($talao['bairro']?:'—')?></strong></div>
        <div><span style="color:var(--text-muted)">Referência:</span><br><strong><?=htmlspecialchars($talao['referencia']?:'—')?></strong></div>
        <div><span style="color:var(--text-muted)">Equipe:</span><br><strong><?=$talao['tipo_equipe']==='pop'?'<span class="badge badge-secondary">POP</span>':'<span class="badge badge-info">Viatura</span>'?></strong></div>
        <?php if($talao['tipo_equipe'] !== 'pop'): ?>
        <div><span style="color:var(--text-muted)">Viatura:</span><br><strong><?=$talao['viatura_prefixo']?'<span class="badge badge-info">'.$talao['viatura_prefixo'].'</span>':'—'?></strong></div>
        <div><span style="color:var(--text-muted)">KM:</span><br><strong><?=$talao['km_inicial']?number_format($talao['km_inicial'],0,'.',',').' km':'—'?><?=$talao['km_final']?' → '.number_format($talao['km_final'],0,'.',',').' km':''?></strong></div>
        <?php endif; ?>
      </div>
    </div>

    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.9rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem">Agentes</h3>
      <div style="display:flex;flex-wrap:wrap;gap:.4rem">
        <span class="badge badge-primary"><i class="fa-solid fa-user-shield"></i> <?=htmlspecialchars($talao['agente_nome']??'—')?> (responsável)</span>
        <?php foreach($agentes as $ag): ?>
        <span class="badge badge-secondary"><i class="fa-solid fa-user-shield"></i> <?=htmlspecialchars($ag['nome'])?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="table-wrapper" style="padding:1.25rem">
      <h3 style="font-size:.9rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.75rem">Narrativa</h3>
      <p style="font-size:.875rem;line-height:1.8;white-space:pre-wrap"><?=htmlspecialchars($talao['narrativa']?:'Não informada.')?></p>
    </div>

    <?php if($talao['encaminhamento']||$talao['medidas_tomadas']): ?>
    <div class="table-wrapper" style="padding:1.25rem">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:.875rem">
        <?php if($talao['encaminhamento']): ?>
        <div><h3 style="font-size:.85rem;color:var(--text-muted);margin-bottom:.5rem">ENCAMINHAMENTO</h3>
          <p style="white-space:pre-wrap"><?=htmlspecialchars($talao['encaminhamento'])?></p></div>
        <?php endif; ?>
        <?php if($talao['medidas_tomadas']): ?>
        <div><h3 style="font-size:.85rem;color:var(--text-muted);margin-bottom:.5rem">MEDIDAS TOMADAS</h3>
          <p style="white-space:pre-wrap"><?=htmlspecialchars($talao['medidas_tomadas'])?></p></div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if($talao['observacoes_internas'] && (Auth::can('taloes.ver_todos')||Auth::can('taloes.editar_todos'))): ?>
    <div class="table-wrapper" style="padding:1.25rem;border-color:var(--warning)">
      <h3 style="font-size:.85rem;color:var(--warning);margin-bottom:.5rem"><i class="fa-solid fa-lock"></i> OBSERVAÇÕES INTERNAS</h3>
      <p style="font-size:.875rem;white-space:pre-wrap"><?=htmlspecialchars($talao['observacoes_internas'])?></p>
    </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar direita -->
  <div style="display:flex;flex-direction:column;gap:1rem">

    <?php if($talao['latitude']&&$talao['longitude']): ?>
    <!-- Mini mapa -->
    <div class="table-wrapper" style="overflow:hidden">
      <div id="miniMapa" style="height:200px"></div>
      <div style="padding:.75rem 1rem;font-size:.8rem;color:var(--text-muted)">
        <i class="fa-solid fa-location-dot"></i> <?=number_format($talao['latitude'],6)?>, <?=number_format($talao['longitude'],6)?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Fotos -->
    <?php if(!empty($fotos)): ?>
    <div class="table-wrapper" style="padding:1rem">
      <h3 style="font-size:.9rem;margin-bottom:.75rem"><i class='fa-solid fa-camera'></i> Fotos (<?=count($fotos)?>)</h3>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.5rem">
        <?php foreach($fotos as $f): ?>
        <a href="<?=BASE_URL?>/uploads/<?=htmlspecialchars($f['caminho'])?>" target="_blank">
          <img src="<?=BASE_URL?>/uploads/<?=htmlspecialchars($f['caminho'])?>" alt="Foto" style="width:100%;height:80px;object-fit:cover;border-radius:4px;border:1px solid var(--border)">
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Envolvidos -->
    <?php if(!empty($envolvidos)): ?>
    <div class="table-wrapper" style="padding:1rem">
      <h3 style="font-size:.9rem;margin-bottom:.75rem"><i class="fa-solid fa-users"></i> Pessoas Envolvidas (<?=count($envolvidos)?>)</h3>
      <?php foreach($envolvidos as $e): ?>
      <div style="border-bottom:1px solid var(--border);padding:.5rem 0;font-size:.83rem">
        <span class="badge badge-<?=$e['tipo']==='autor'?'danger':($e['tipo']==='vitima'?'warning':'secondary')?> badge-sm"><?=ucfirst($e['tipo'])?></span>
        <strong style="margin-left:.4rem"><?=htmlspecialchars($e['nome']??'—')?></strong>
        <?php if($e['cpf']): ?><span style="color:var(--text-muted)"> — <?=htmlspecialchars($e['cpf'])?></span><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Metadados -->
    <div class="table-wrapper" style="padding:1rem;font-size:.8rem;color:var(--text-muted)">
      <div><i class="fa-solid fa-clipboard-list"></i> Criado por: <strong style="color:var(--text)"><?=htmlspecialchars($talao['criado_por_nome']??'—')?></strong></div>
      <div style="margin-top:.35rem"><i class='fa-solid fa-clock'></i> <?=date('d/m/Y H:i',strtotime($talao['created_at']))?></div>
      <?php if($talao['updated_at']!==$talao['created_at']): ?>
      <div style="margin-top:.35rem"><i class="fa-solid fa-pencil"></i> Editado: <?=date('d/m/Y H:i',strtotime($talao['updated_at']))?></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Feed de Atualizações -->
<div class="table-wrapper">
  <div class="table-header">
    <h3><i class='fa-solid fa-comments'></i> Atualizações do Talão</h3>
  </div>
  <?php if(!empty($atualizacoes)): ?>
    <?php foreach($atualizacoes as $a): ?>
    <div style="padding:.85rem 1.25rem;border-bottom:1px solid var(--border);display:flex;gap:.75rem">
      <div style="width:32px;height:32px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0">
        <?=strtoupper(substr($a['autor']??'?',0,1))?>
      </div>
      <div style="flex:1">
        <div style="font-size:.85rem;margin-bottom:.25rem">
          <strong><?=htmlspecialchars($a['autor'])?></strong>
          <span style="color:var(--text-muted);font-size:.8rem;margin-left:.5rem"><?=date('d/m/Y H:i',strtotime($a['criado_em']))?></span>
        </div>
        <p style="font-size:.9rem;white-space:pre-wrap"><?=htmlspecialchars($a['texto'])?></p>
      </div>
    </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div style="padding:1.5rem;text-align:center;color:var(--text-muted);font-size:.9rem">Nenhuma atualização registrada.</div>
  <?php endif; ?>
  
  <div style="padding:1.25rem;background:var(--surface-2)">
    <form method="POST" action="<?=BASE_URL?>/taloes/<?=$talao['id']?>/atualizacao">
      <?=Auth::csrfField()?>
      <div style="display:flex;gap:.75rem">
        <textarea name="texto" class="form-control" rows="2" placeholder="Adicionar atualização ao talão..." style="flex:1" required></textarea>
        <button type="submit" class="btn btn-primary" style="align-self:flex-end">Postar</button>
      </div>
    </form>
  </div>
</div>

<?php if($talao['latitude']&&$talao['longitude']): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const mapa = L.map('miniMapa').setView([<?=$talao['latitude']?>,<?=$talao['longitude']?>], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapa);
L.marker([<?=$talao['latitude']?>,<?=$talao['longitude']?>]).addTo(mapa)
 .bindPopup('<i class="fa-solid fa-location-dot"></i> <?=htmlspecialchars(addslashes($talao['bairro']??''))?>').openPopup();
</script>
<?php endif; ?>

<!-- Modal Fechar Talão -->
<div id="modalFechar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:var(--surface);width:100%;max-width:400px;border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <h3 style="margin:0;font-size:1.1rem">Encerrar Talão</h3>
      <button type="button" onclick="document.getElementById('modalFechar').style.display='none'" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--text-muted)">&times;</button>
    </div>
    <form method="POST" action="<?=BASE_URL?>/taloes/<?=$talao['id']?>/status" style="display:block;padding:1.25rem">
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

      <?php if(($talao['tipo_equipe'] ?? 'viatura') !== 'pop'): ?>
      <div style="margin-bottom:1.5rem">
        <label class="form-label" style="font-size:.85rem">KM Final da Viatura</label>
        <input type="number" step="0.1" name="km_final" class="form-control" placeholder="Ex: 12345.6" value="<?=htmlspecialchars($talao['km_final'] ?: ($talao['km_inicial']??''))?>" required>
      </div>
      <?php endif; ?>

      <div style="display:flex;gap:.5rem;justify-content:flex-end">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalFechar').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-lock"></i> Confirmar Encerramento</button>
      </div>
    </form>
  </div>
</div>
