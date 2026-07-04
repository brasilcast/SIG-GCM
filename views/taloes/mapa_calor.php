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
 */$pageTitle = 'Mapa de Calor'; ?>
<div class="page-header">
  <div>
    <h2><i class="fa-solid fa-map"></i> Mapa de Calor de Ocorrências</h2>
    <div class="breadcrumb">Talões › Mapa de Calor</div>
  </div>
  <a href="<?=BASE_URL?>/taloes" class="btn btn-secondary">← Voltar</a>
</div>

<div class="table-wrapper" style="overflow:hidden;margin-bottom:1rem">
  <div id="mapa" style="height:500px"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
const mapa = L.map('mapa').setView([-23.5505, -46.6333], 13); // Eldorado dos Ventos (Fictício - Centro em SP)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(mapa);

// Buscar pontos via AJAX
fetch('<?=BASE_URL?>/taloes/mapa-calor?json=1')
  .then(r=>r.json())
  .then(data=>{
    const pontos = data.filter(p=>p.latitude&&p.longitude)
                       .map(p=>[parseFloat(p.latitude),parseFloat(p.longitude),1]);
    if(pontos.length>0){
      L.heatLayer(pontos,{radius:25,blur:15,maxZoom:17}).addTo(mapa);
    }
    // Marcadores com popup
    data.forEach(p=>{
      if(!p.latitude||!p.longitude) return;
      const cor = p.status==='fechado'?'green':p.status==='em_atendimento'?'orange':'red';
      const icon = L.divIcon({html:`<div style="background:${cor};width:10px;height:10px;border-radius:50%;border:2px solid white"></div>`,className:'',iconSize:[10,10]});
      L.marker([p.latitude,p.longitude],{icon})
       .addTo(mapa)
       .bindPopup(`<b>${p.numero}</b><br>${p.natureza||'Sem natureza'}<br>${p.bairro||''}<br><small>${new Date(p.data_hora_abertura).toLocaleDateString('pt-BR')}</small>`);
    });
  });
</script>
