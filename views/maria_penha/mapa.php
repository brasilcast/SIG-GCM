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
 */$pageTitle='Mapa de Vítimas'; ?>
<div class="page-header">
  <div><h2><i class='fa-solid fa-map-location-dot'></i> Mapa de Vítimas — Maria da Penha</h2><div class="breadcrumb">Maria da Penha › Mapa</div></div>
  <a href="<?=BASE_URL?>/maria-penha" class="btn btn-secondary">← Voltar</a>
</div>
<div class="table-wrapper" style="overflow:hidden">
  <div id="mapa" style="height:500px"></div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const mapa = L.map('mapa').setView([-20.2669, -50.5479], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapa);
fetch('<?=BASE_URL?>/maria-penha/mapa?json=1')
  .then(r=>r.json())
  .then(data=>{
    const cores={1:'#ef4444',2:'#f97316',3:'#eab308',4:'#22c55e',5:'#94a3b8'};
    data.forEach(v=>{
      const cor=cores[v.prioridade]||'#64748b';
      const icon=L.divIcon({html:`<div style="background:${cor};width:22px;height:22px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;color:white;font-size:10px;font-weight:bold;">P${v.prioridade}</div>`,className:'',iconSize:[22,22]});
      L.marker([v.latitude,v.longitude],{icon})
       .addTo(mapa)
       .bindPopup(`<b>${v.nome}</b><br>Bairro: ${v.bairro||'—'}<br><span style="color:${cor}">Prioridade P${v.prioridade}</span><br><a href="<?=BASE_URL?>/maria-penha/${v.id}" style="color:#3b82f6">Ver cadastro →</a>`);
    });
  });
</script>
