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
 */$pageTitle = ($titulo??'Talão');
$talao = $talao ?? null;
$isEdit = $talao !== null;
$action = $isEdit ? BASE_URL.'/taloes/'.$talao['id'].'/editar' : BASE_URL.'/taloes/novo';
$agora  = date('Y-m-d\TH:i');
?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-file-lines"></i> <?=htmlspecialchars($titulo??'Talão')?></h2>
    <div class="breadcrumb">Talões › <?=htmlspecialchars($titulo??'Talão')?></div>
  </div>
  <a href="<?=BASE_URL?>/taloes" class="btn btn-secondary">← Voltar</a>
</div>

<?php if(!empty($templates)): ?>
<div class="alert alert-info" style="cursor:pointer" onclick="document.getElementById('templateBox').style.display='block'">
  <i class="fa-solid fa-file-lines"></i> Usar um <strong>template de narrativa</strong>? Clique aqui.
</div>
<div id="templateBox" style="display:none;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:1rem">
  <strong>Selecione um template:</strong>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.5rem;margin-top:.75rem">
    <?php foreach($templates as $tpl): ?>
    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarTemplate(<?=json_encode($tpl['conteudo'])?>)"><?=htmlspecialchars($tpl['nome'])?></button>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<form method="POST" action="<?=$action?>" enctype="multipart/form-data" id="formTalao">
  <?=Auth::csrfField()?>
<div class="grid-2" style="margin-bottom:1rem">

    <!-- Coluna 1: Identificação -->
    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-id-card"></i> Identificação</h3>
      <div class="form-group">
        <label class="form-label">Número do Talão</label>
        <input type="text" name="numero" class="form-control" value="<?=htmlspecialchars($talao['numero']??($numSugerido??''))?>" <?=$isEdit?'readonly style="background:#f1f5f9"':''?> placeholder="Deixe em branco para auto ou informe o número">
      </div>
      <div class="form-row">

        <div class="form-group">
          <label class="form-label">BOGM (Opcional)</label>
          <input type="text" name="bogm" class="form-control" value="<?=htmlspecialchars(!empty($talao) ? $talao['bogm']??'' : '')?>" placeholder="Nº BOGM">
        </div>
        <div class="form-group">
          <label class="form-label">BOPC (Opcional)</label>
          <input type="text" name="bopc" class="form-control" value="<?=htmlspecialchars(!empty($talao) ? $talao['bopc']??'' : '')?>" placeholder="Nº BOPC">
        </div>
      </div>
        <div class="form-row">

        <div class="form-group">
          <label class="form-label">Grupo de Ocorrência</label>
          <select id="natureza_grupo" class="form-control" onchange="atualizarNaturezas()">
            <option value="">— Selecione o Grupo —</option>
            <?php 
            $grupos = [];
            foreach($naturezas as $n) { $grupos[$n['grupo']] = $n['grupo']; }
            foreach($grupos as $g): ?>
            <option value="<?=htmlspecialchars($g)?>" <?=(isset($talao) && $talao['natureza_grupo'] === $g)?'selected':''?>><?=htmlspecialchars($g)?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Natureza Específica</label>
          <select name="natureza_id" id="natureza_id" class="form-control">
            <option value="">— Selecione o Grupo Primeiro —</option>
          </select>
        </div>
      </div>
      <!-- BOX: ABERTURA DO TALÃO -->
      <div style="border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:1rem;background:var(--surface-2)">
        <h4 style="margin:0 0 1rem 0;font-size:.9rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em">Abertura / Início</h4>
          <div class="form-row">

          <div class="form-group mb-0">
            <label class="form-label">Data *</label>
            <input type="date" name="data_ocorrencia" class="form-control" value="<?=$talao?date('Y-m-d',strtotime($talao['data_hora_abertura'])):date('Y-m-d')?>" required readonly style="background:#f1f5f9;cursor:not-allowed">
          </div>
          <div class="form-group mb-0">
            <label class="form-label">Hora *</label>
            <input type="time" name="hora_abertura" class="form-control" value="<?=$talao?date('H:i',strtotime($talao['data_hora_abertura'])):date('H:i')?>" required>
          </div>
        </div>
        
        <div class="form-group" style="margin-top:1rem">
          <label class="form-label">Equipe</label>
          <select name="tipo_equipe" id="tipo_equipe" class="form-control" onchange="toggleEquipeFields()">
            <option value="viatura" <?=($talao['tipo_equipe']??'viatura')==='viatura'?'selected':''?>>Viatura</option>
            <option value="pop" <?=($talao['tipo_equipe']??'')==='pop'?'selected':''?>>POP</option>
          </select>
        </div>
        
        <div id="boxViatura">
          <div class="form-group">
            <label class="form-label">Prefixo da Viatura</label>
            <select name="viatura_id" class="form-control">
              <option value="">— Sem viatura —</option>
              <?php foreach($viaturas as $v): ?>
              <option value="<?=$v['id']?>" <?=($talao['viatura_id']??'')==$v['id']?'selected':''?>><?=htmlspecialchars($v['prefixo'].($v['placa']?' — '.$v['placa']:''))?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group mb-0">
            <label class="form-label">KM Inicial</label>
            <input type="number" step="0.1" name="km_inicial" id="km_inicial" class="form-control" value="<?=htmlspecialchars($talao['km_inicial']??'')?>">
          </div>
        </div>
      </div>

      <!-- BOX: ENCERRAMENTO DO TALÃO (Apenas Edição) -->
      <?php if($isEdit): ?>
      <div style="border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:1rem;background:var(--surface-2)">
        <h4 style="margin:0 0 1rem 0;font-size:.9rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em">Encerramento (KM Final e Hora)</h4>
        
        <div class="form-row">

          <div class="form-group mb-0">
            <label class="form-label">Data de Fechamento</label>
            <input type="date" name="data_fechamento_edit" id="data_fechamento_edit" class="form-control" value="<?=$talao['data_hora_fechamento']?date('Y-m-d',strtotime($talao['data_hora_fechamento'])):''?>">
          </div>
          <div class="form-group mb-0">
            <label class="form-label">Hora de Fechamento</label>
            <input type="time" name="hora_fechamento_edit" id="hora_fechamento_edit" class="form-control" value="<?=$talao['data_hora_fechamento']?date('H:i',strtotime($talao['data_hora_fechamento'])):''?>">
          </div>
        </div>
        
        <div id="boxKmFinal" class="form-group mb-0" style="margin-top:1rem">
          <label class="form-label">KM Final da Viatura</label>
          <input type="number" step="0.1" name="km_final" id="km_final" class="form-control" value="<?=htmlspecialchars($talao['km_final']??'')?>">
        </div>
        
        <!-- Escondido para montar data e hora juntas no JS -->
        <input type="hidden" name="data_hora_fechamento" id="data_hora_fechamento" value="">
      </div>
      <?php endif; ?>

      <!-- Escondido para manter a compatibilidade do backend que espera o data_hora_abertura completo -->
      <input type="hidden" name="data_hora_abertura" id="data_hora_abertura" value="">

      <!-- Status removido, pois a criação é sempre "aberto" e o fechamento é feito via modal -->
    </div>

    <!-- Coluna 2: Localização -->
    <div class="table-wrapper" style="padding:1.5rem">
      <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-location-dot"></i> Localização da Ocorrência</h3>
      
      <!-- Mapa interativo para capturar local -->
      <div id="mapaTalao" style="width:100%;height:180px;background:#e2e8f0;border-radius:var(--radius);margin-bottom:1rem;border:1px solid var(--border);position:relative">
        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:400;color:var(--text-muted)">
          <i class="fa-solid fa-spinner fa-spin"></i> Carregando mapa...
        </div>
      </div>
      
      <button type="button" id="btnLocalizar" class="btn btn-primary btn-block" style="margin-bottom:1rem" onclick="obterLocalizacaoAtual()">
        <i class="fa-solid fa-crosshairs"></i> Usar Minha Localização Atual
      </button>

      <div class="form-group">
        <label class="form-label">Endereço (Rua, Número) <span id="geoStatus" style="font-size:.78rem;margin-left:.4rem;font-weight:normal"></span></label>
        <input type="text" name="endereco" id="endereco" class="form-control" value="<?=htmlspecialchars($talao['endereco']??'')?>" placeholder="Rua e número" autocomplete="off">
      </div>
      <div class="form-row">

        <div class="form-group">
          <label class="form-label">Bairro</label>
          <input type="text" name="bairro" id="bairro" class="form-control" value="<?=htmlspecialchars($talao['bairro']??'')?>">
        </div>
        <div class="form-group">
          <label class="form-label">Referência</label>
          <input type="text" name="referencia" id="referencia" class="form-control" value="<?=htmlspecialchars($talao['referencia']??'')?>">
        </div>
      </div>
      <div class="form-row">

        <div class="form-group">
          <label class="form-label">Latitude</label>
          <input type="text" name="latitude" id="latitude" class="form-control" value="<?=htmlspecialchars($talao['latitude']??'')?>" readonly style="background:#f1f5f9">
        </div>
        <div class="form-group">
          <label class="form-label">Longitude</label>
          <input type="text" name="longitude" id="longitude" class="form-control" value="<?=htmlspecialchars($talao['longitude']??'')?>" readonly style="background:#f1f5f9">
        </div>
      </div>
    </div>
  </div>

  <!-- Agentes -->
  <div class="table-wrapper" style="padding:1.5rem;margin-bottom:1rem">
    <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-user-shield"></i> Agentes Envolvidos</h3>
    <div class="form-row">

      <div class="form-group">
        <label class="form-label">Agente Responsável</label>
        <select name="agente_id" class="form-control">
          <option value="">— Selecione —</option>
          <?php foreach($agentes as $ag): ?>
          <option value="<?=$ag['id']?>" <?=($talao['agente_id']??Auth::id())==$ag['id']?'selected':''?>><?=htmlspecialchars($ag['nome'].($ag['matricula']?' ('.$ag['matricula'].')':''))?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Agentes Adicionais (múltipla seleção)</label>
        <select name="agentes_adicionais[]" class="form-control" multiple style="min-height:80px">
          <?php foreach($agentes as $ag): ?>
          <option value="<?=$ag['id']?>" <?=in_array($ag['id'],$agsVinc??[])?'selected':''?>><?=htmlspecialchars($ag['nome'])?></option>
          <?php endforeach; ?>
        </select>
        <small style="font-size:.72rem;color:var(--text-muted)">Ctrl+Clique para selecionar múltiplos</small>
      </div>
    </div>
  </div>

  <!-- Narrativa -->
  <div class="table-wrapper" style="padding:1.5rem;margin-bottom:1rem">
    <h3 style="font-size:.95rem;margin-bottom:1rem"><i class="fa-solid fa-clipboard-list"></i> Narrativa e Encaminhamento</h3>
    <div class="form-group">
      <label class="form-label">Narrativa da Ocorrência</label>
      <textarea name="narrativa" id="narrativa" class="form-control" rows="6" placeholder="Descreva detalhadamente o que ocorreu..."><?=htmlspecialchars($talao['narrativa']??'')?></textarea>
    </div>
    <div class="form-row">

      <div class="form-group">
        <label class="form-label">Encaminhamento</label>
        <textarea name="encaminhamento" class="form-control" rows="4" placeholder="Para onde/quem foi encaminhado?"><?=htmlspecialchars($talao['encaminhamento']??'')?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Medidas Tomadas</label>
        <textarea name="medidas_tomadas" class="form-control" rows="4" placeholder="Quais medidas foram tomadas?"><?=htmlspecialchars($talao['medidas_tomadas']??'')?></textarea>
      </div>
    </div>
    <?php if(Auth::can('taloes.editar_todos') || Auth::can('taloes.ver_todos')): ?>
    <div class="form-group">
      <label class="form-label">Observações Internas (visível apenas para inspetores+)</label>
      <textarea name="observacoes_internas" class="form-control" rows="3"><?=htmlspecialchars($talao['observacoes_internas']??'')?></textarea>
    </div>
    <?php endif; ?>
  </div>

  <!-- Fotos e Anexos -->
  <div class="table-wrapper" style="padding:1.5rem;margin-bottom:1rem">
    <h3 style="font-size:.95rem;margin-bottom:.75rem;border-bottom:1px solid var(--border);padding-bottom:.5rem"><i class="fa-solid fa-paperclip"></i> Anexar Imagens e Arquivos</h3>
    
    <div style="background:#f8fafc; border:2px dashed #cbd5e1; border-radius:var(--radius); padding:2rem; text-align:center; transition:all 0.2s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='#cbd5e1'">
      <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem; color:var(--primary); margin-bottom:.5rem"></i>
      <p style="margin:0 0 1rem 0; color:var(--text-muted); font-size:.9rem">Selecione arquivos do seu dispositivo para anexar ao talão.</p>
      
      <div style="position:relative; display:inline-block;">
        <input type="file" name="fotos[]" id="fotosUpload" class="form-control" multiple accept="image/*,application/pdf" style="position:absolute; left:0; top:0; opacity:0; width:100%; height:100%; cursor:pointer; z-index:10" onchange="document.getElementById('lblFotos').innerText=this.files.length + ' arquivo(s) selecionado(s)'">
        <button type="button" class="btn btn-secondary"><i class="fa-solid fa-folder-open"></i> <span id="lblFotos">Procurar Arquivos</span></button>
      </div>
      
      <div style="margin-top:1rem; font-size:.75rem; color:var(--text-muted)">
        <span class="badge badge-secondary" style="background:#e2e8f0;color:#475569">JPG</span>
        <span class="badge badge-secondary" style="background:#e2e8f0;color:#475569">PNG</span>
        <span class="badge badge-secondary" style="background:#e2e8f0;color:#475569">WEBP</span>
        <span class="badge badge-secondary" style="background:#e2e8f0;color:#475569">PDF</span>
      </div>
    </div>
  </div>

  <div class="form-actions">

    <a href="<?=BASE_URL?>/taloes" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary btn-lg">
      <?=$isEdit?'<i class="fa-solid fa-floppy-disk"></i> Salvar Alterações':'<i class="fa-solid fa-file-lines"></i> Registrar Talão'?>
    </button>
  </div>
</form>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function aplicarTemplate(conteudo) {
  document.getElementById('narrativa').value = conteudo;
  document.getElementById('templateBox').style.display = 'none';
}

function toggleEquipeFields() {
  const tipo = document.getElementById('tipo_equipe');
  const box = document.getElementById('boxViatura');
  if(!tipo || !box) return;
  
  if(tipo.value === 'pop') {
    box.style.display = 'none';
    const numInputs = box.querySelectorAll('input[type=number]');
    numInputs.forEach(inp => inp.value = ''); // Limpa os KMs se for POP
    box.querySelector('select').value = ''; // Remove seleção de viatura
  } else {
    box.style.display = 'block';
  }
}

const naturezasData = <?=json_encode($naturezas)?>;
const selecionadoAtual = "<?=isset($talao) ? ($talao['natureza_id']??'') : ''?>";

function atualizarNaturezas() {
  const grupo = document.getElementById('natureza_grupo').value;
  const selectNat = document.getElementById('natureza_id');
  
  selectNat.innerHTML = '<option value="">— Selecione uma Ocorrência —</option>';
  
  if (!grupo) {
    selectNat.innerHTML = '<option value="">— Selecione o Grupo Primeiro —</option>';
    return;
  }
  
  const filtradas = naturezasData.filter(n => n.grupo === grupo);
  
  filtradas.forEach(n => {
    const opt = document.createElement('option');
    opt.value = n.id;
    // Mostrar nome (que agora já inclui o código no bd)
    opt.textContent = (n.codigo ? n.codigo + ' - ' : '') + n.nome;
    if (n.id == selecionadoAtual) opt.selected = true;
    selectNat.appendChild(opt);
  });
}

// Inicializa no carregamento as exibições
document.addEventListener('DOMContentLoaded', () => {
  toggleEquipeFields();
  atualizarNaturezas(); // dispara uma vez para preencher em caso de edição
});

// Junta a data e hora em um único campo escondido antes de submeter
document.getElementById('formTalao').addEventListener('submit', function(e) {
  const data = document.querySelector('input[name="data_ocorrencia"]').value;
  const hora = document.querySelector('input[name="hora_abertura"]').value;
  document.getElementById('data_hora_abertura').value = data + ' ' + hora + ':00';
});

// Inicialização do Mapa
let mapa, marcador;
const inputLat = document.getElementById('latitude');
const inputLng = document.getElementById('longitude');
const inputEnd = document.getElementById('endereco');
const inputBairro = document.getElementById('bairro');

function initMap(lat, lng, zoom) {
  document.getElementById('mapaTalao').innerHTML = ''; // Limpa o loader
  mapa = L.map('mapaTalao').setView([lat, lng], zoom);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(mapa);
  
  marcador = L.marker([lat, lng], {draggable: true}).addTo(mapa);
  
  // Se o marcador for arrastado, atualiza as coordenadas e pega o endereço novo
  marcador.on('dragend', function(e) {
    const pos = marcador.getLatLng();
    atualizarCampos(pos.lat, pos.lng);
    buscarEndereco(pos.lat, pos.lng);
  });

  // Se clicar no mapa, move o marcador
  mapa.on('click', function(e) {
    marcador.setLatLng(e.latlng);
    atualizarCampos(e.latlng.lat, e.latlng.lng);
    buscarEndereco(e.latlng.lat, e.latlng.lng);
  });
}

function atualizarCampos(lat, lng) {
  inputLat.value = lat.toFixed(8);
  inputLng.value = lng.toFixed(8);
}

// Faz requisição para Nominatim (OSM) para pegar nome da rua e bairro
function buscarEndereco(lat, lng) {
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
    .then(r => r.json())
    .then(data => {
      if (data && data.address) {
        // Se ainda não tiver preenchido manualmente, preenche
        const rua = data.address.road || '';
        const num = data.address.house_number || '';
        const bairro = data.address.suburb || data.address.neighbourhood || data.address.city_district || '';
        
        let enderecoCompleto = rua;
        if (num) enderecoCompleto += ', ' + num;
        
        if (!inputEnd.value || inputEnd.value === '') inputEnd.value = enderecoCompleto;
        if (!inputBairro.value || inputBairro.value === '') inputBairro.value = bairro;
      }
    }).catch(e => console.error("Erro ao buscar endereço", e));
}

// ===== FORWARD GEOCODING: endereço digitado → coordenadas =====
// Só dispara UMA VEZ por sessão de formulário (flag geocodingDone)
let geocodingDone = <?= ($isEdit && !empty($talao['latitude'])) ? 'true' : 'false' ?>;

function geocodificarEndereco() {
  if (geocodingDone) return;
  const endereco = inputEnd.value.trim();
  if (endereco.length < 5) return;

  const status = document.getElementById('geoStatus');
  status.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="color:var(--primary)"></i>';

  // Viewbox delimitando a região de teste (São Paulo - SP)
  const viewbox = '-46.82,-23.39,-46.36,-23.72';
  const query   = encodeURIComponent(endereco + ', São Paulo, SP, Brasil');
  const url     = `https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1&countrycodes=br&viewbox=${viewbox}&bounded=1`;

  fetch(url, { headers: { 'Accept-Language': 'pt-BR' } })
    .then(r => r.json())
    .then(results => {
      if (!results || results.length === 0) {
        // Tenta sem bounded (cidade inteira do BR) como fallback
        return fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1&countrycodes=br`, { headers: { 'Accept-Language': 'pt-BR' } })
          .then(r => r.json());
      }
      return results;
    })
    .then(results => {
      if (!results || results.length === 0) {
        status.innerHTML = '<span style="color:#e53e3e"><i class="fa-solid fa-triangle-exclamation"></i> Endereço não encontrado</span>';
        return;
      }

      const res = results[0];
      const lat = parseFloat(res.lat);
      const lng = parseFloat(res.lon);

      // Atualiza coordenadas e mapa
      atualizarCampos(lat, lng);
      if (marcador) {
        marcador.setLatLng([lat, lng]);
        mapa.setView([lat, lng], 17);
      }

      // Faz reverse geocoding para pegar bairro padronizado
      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
        .then(r => r.json())
        .then(data => {
          if (data && data.address) {
            const rua    = data.address.road || '';
            const num    = data.address.house_number || '';
            const bairro = data.address.suburb || data.address.neighbourhood || data.address.city_district || '';

            // Substitui o endereço pelo nome padronizado do OSM
            if (rua) {
              inputEnd.value = rua + (num ? ', ' + num : '');
            }

            // Preenche bairro só se estiver vazio
            if (!inputBairro.value.trim() && bairro) {
              inputBairro.value = bairro;
            }
          }
          status.innerHTML = '<span style="color:#22c55e"><i class="fa-solid fa-check"></i> Localização identificada</span>';
          setTimeout(() => status.innerHTML = '', 4000);
        });

      geocodingDone = true;
    })
    .catch(err => {
      console.error('Erro no geocoding:', err);
      status.innerHTML = '';
    });
}

// Dispara ao sair do campo (blur) — apenas uma vez
document.addEventListener('DOMContentLoaded', () => {
  inputEnd.addEventListener('blur', geocodificarEndereco);
  // Se o usuário editar qualquer campo depois do auto-preenchimento, tudo bem — não re-dispara
});

// Botão "Minha Localização Atual"
function obterLocalizacaoAtual() {
  const btn = document.getElementById('btnLocalizar');
  const orgText = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Localizando...';
  btn.disabled = true;

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        atualizarCampos(lat, lng);
        marcador.setLatLng([lat, lng]);
        mapa.setView([lat, lng], 17);
        buscarEndereco(lat, lng);
        
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Localização Capturada!';
        btn.classList.replace('btn-primary', 'btn-success');
        setTimeout(() => {
          btn.innerHTML = orgText;
          btn.classList.replace('btn-success', 'btn-primary');
          btn.disabled = false;
        }, 2000);
      },
      (err) => {
        alert("Erro ao acessar GPS. Permita a localização no navegador.");
        btn.innerHTML = orgText;
        btn.disabled = false;
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  } else {
    alert("Geolocalização não suportada.");
    btn.innerHTML = orgText;
    btn.disabled = false;
  }
}

// Carrega o mapa ao inicializar a página
window.addEventListener('DOMContentLoaded', () => {
  // Se for edição e já tiver coordenadas, mostra. Se não, centraliza em SP
  const temCoordenada = inputLat.value && inputLng.value;
  const startLat = temCoordenada ? parseFloat(inputLat.value) : -23.5505;
  const startLng = temCoordenada ? parseFloat(inputLng.value) : -46.6333;
  const zoom = temCoordenada ? 17 : 13;
  
  initMap(startLat, startLng, zoom);
});

// Ao enviar o formulário, junta os campos de data e hora para os inputs hidden
document.querySelector('form').addEventListener('submit', function(e) {
  const dataAbert = document.querySelector('input[name="data_ocorrencia"]');
  const horaAbert = document.querySelector('input[name="hora_abertura"]');
  if (dataAbert && horaAbert && dataAbert.value && horaAbert.value) {
    document.getElementById('data_hora_abertura').value = dataAbert.value + ' ' + horaAbert.value + ':00';
  }
  
  const dataFec = document.querySelector('input[name="data_fechamento_edit"]');
  const horaFec = document.querySelector('input[name="hora_fechamento_edit"]');
  if (dataFec && horaFec) {
    if (dataFec.value && horaFec.value) {
      document.getElementById('data_hora_fechamento').value = dataFec.value + ' ' + horaFec.value + ':00';
    } else {
      document.getElementById('data_hora_fechamento').value = '';
    }
  }
});
</script>
