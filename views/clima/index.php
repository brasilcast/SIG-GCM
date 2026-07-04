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
 */$pageTitle = 'Clima — Eldorado dos Ventos'; ?>
<style>
/* ============================================================
   CLIMA — PREMIUM WEATHER MODULE
   ============================================================ */
.clima-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 0 2rem;
    font-family: 'Inter', sans-serif;
}

/* ── Hero Banner ─────────────────────────────────────────── */
.clima-hero {
    background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    min-height: 240px;
    display: flex;
    align-items: center;
    gap: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
}
.clima-hero.day {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
}
.clima-hero.rain {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d3561 50%, #1a1a2e 100%);
}
.clima-hero.cloudy {
    background: linear-gradient(135deg, #2c3e50 0%, #3d4f6e 50%, #2c3e50 100%);
}

/* Animated stars/particles */
.clima-particles {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    pointer-events: none;
    overflow: hidden;
}
.clima-particles span {
    position: absolute;
    border-radius: 50%;
    animation: sparkle 4s infinite;
    background: rgba(255,255,255,.6);
}

@keyframes sparkle {
    0%, 100% { opacity: 0; transform: scale(0); }
    50% { opacity: 1; transform: scale(1); }
}

/* Sun animation */
.weather-icon-hero {
    font-size: 7rem;
    line-height: 1;
    position: relative;
    z-index: 2;
    filter: drop-shadow(0 0 30px rgba(255,200,50,.6));
    animation: float 6s ease-in-out infinite;
    text-shadow: 0 0 40px rgba(255,200,50,.4);
    flex-shrink: 0;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

.clima-hero-info {
    flex: 1;
    position: relative;
    z-index: 2;
    color: #fff;
}
.clima-hero-city {
    font-size: .95rem;
    color: rgba(255,255,255,.7);
    letter-spacing: .15em;
    text-transform: uppercase;
    margin-bottom: .25rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.clima-hero-temp {
    font-size: 5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: .35rem;
    letter-spacing: -3px;
}
.clima-hero-desc {
    font-size: 1.3rem;
    color: rgba(255,255,255,.85);
    margin-bottom: 1rem;
    font-weight: 500;
}
.clima-hero-meta {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.clima-hero-meta-item {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .85rem;
    color: rgba(255,255,255,.75);
}
.clima-hero-meta-item i {
    color: rgba(255,255,255,.5);
    font-size: .8rem;
}

/* Right side of hero */
.clima-hero-right {
    position: relative;
    z-index: 2;
    text-align: right;
    flex-shrink: 0;
    color: #fff;
}
.clima-hero-feels {
    font-size: .8rem;
    color: rgba(255,255,255,.6);
    margin-bottom: .3rem;
}
.clima-hero-feels strong {
    font-size: 1.4rem;
    color: #fff;
    display: block;
}
.clima-update-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 20px;
    padding: .3rem .75rem;
    font-size: .75rem;
    color: rgba(255,255,255,.7);
    margin-top: .75rem;
    cursor: pointer;
}
.clima-update-badge:hover {
    background: rgba(255,255,255,.2);
}
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Cards grid ─────────────────────────────────────────── */
.clima-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.clima-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.25rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .4rem;
    transition: transform .2s, box-shadow .2s;
}
.clima-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.clima-card-icon {
    font-size: 1.4rem;
    margin-bottom: .25rem;
}
.clima-card-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--text);
    line-height: 1;
}
.clima-card-label {
    font-size: .75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .08em;
}
.clima-card-sub {
    font-size: .8rem;
    color: var(--text-muted);
}

/* Progress bar */
.clima-bar {
    height: 5px;
    background: var(--border);
    border-radius: 10px;
    overflow: hidden;
    margin-top: .4rem;
}
.clima-bar-fill {
    height: 100%;
    border-radius: 10px;
    transition: width 1.5s ease;
}

/* ── Hourly Scroll ───────────────────────────────────────── */
.clima-section-title {
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--text-muted);
    margin-bottom: .75rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.clima-hourly-scroll {
    display: flex;
    gap: .75rem;
    overflow-x: auto;
    padding-bottom: .5rem;
    scrollbar-width: thin;
    margin-bottom: 1.5rem;
}
.clima-hourly-scroll::-webkit-scrollbar { height: 4px; }
.clima-hourly-scroll::-webkit-scrollbar-track { background: var(--border); border-radius: 4px; }
.clima-hourly-scroll::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }

.clima-hour-card {
    flex: 0 0 70px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: .75rem .5rem;
    text-align: center;
    transition: all .2s;
}
.clima-hour-card.now {
    border-color: var(--primary);
    background: var(--primary);
    color: #fff;
}
.clima-hour-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.clima-hour-time { font-size: .72rem; color: inherit; opacity: .7; margin-bottom: .3rem; }
.clima-hour-icon { font-size: 1.3rem; margin-bottom: .3rem; }
.clima-hour-temp { font-size: .95rem; font-weight: 700; }
.clima-hour-rain { font-size: .65rem; opacity: .7; margin-top: .2rem; }

/* ── 7-day forecast ─────────────────────────────────────── */
.clima-forecast-list {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.clima-forecast-row {
    display: grid;
    grid-template-columns: 130px 50px 1fr 80px 80px;
    align-items: center;
    padding: .85rem 1.25rem;
    gap: 1rem;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.clima-forecast-row:last-child { border-bottom: none; }
.clima-forecast-row:hover { background: var(--surface-2); }
.clima-forecast-row.today { background: rgba(var(--primary-rgb), .07); }
.clima-forecast-day { font-weight: 600; font-size: .9rem; }
.clima-forecast-desc { color: var(--text-muted); font-size: .82rem; }
.clima-forecast-bar { display: flex; align-items: center; gap: .5rem; font-size: .82rem; color: var(--text-muted); }
.clima-forecast-min { color: var(--info); font-weight: 600; font-size: .9rem; }
.clima-forecast-max { color: var(--warning); font-weight: 700; font-size: .95rem; }
.forecast-range-bar {
    flex: 1;
    height: 6px;
    background: linear-gradient(90deg, #56b4f5, #f59a00);
    border-radius: 10px;
    position: relative;
}

/* ── UV + AQI ────────────────────────────────────────────── */
.clima-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 640px) { .clima-two-col { grid-template-columns: 1fr; } }

.clima-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.25rem;
}
.clima-panel-title {
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

/* UV Index */
.uv-meter {
    position: relative;
    height: 10px;
    background: linear-gradient(90deg, #5cb85c 0%, #f0ad4e 40%, #e67e22 65%, #e74c3c 85%, #9b59b6 100%);
    border-radius: 10px;
    margin: .75rem 0;
}
.uv-pointer {
    position: absolute;
    top: -4px;
    width: 18px;
    height: 18px;
    background: #fff;
    border: 3px solid var(--primary);
    border-radius: 50%;
    transform: translateX(-50%);
    transition: left 1s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.uv-labels {
    display: flex;
    justify-content: space-between;
    font-size: .65rem;
    color: var(--text-muted);
    margin-top: .5rem;
}
.uv-big {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: .25rem;
}
.uv-category {
    font-size: .85rem;
    font-weight: 600;
    padding: .2rem .7rem;
    border-radius: 20px;
    display: inline-block;
}

/* AQI */
.aqi-gauge {
    position: relative;
    text-align: center;
    padding: .5rem 0;
}
.aqi-big {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1;
}
.aqi-category {
    font-size: .85rem;
    font-weight: 600;
    padding: .2rem .7rem;
    border-radius: 20px;
    display: inline-block;
    margin-top: .3rem;
}
.aqi-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
    margin-top: .75rem;
}
.aqi-detail-item {
    background: var(--surface-2);
    border-radius: 8px;
    padding: .5rem;
    font-size: .75rem;
    text-align: center;
}
.aqi-detail-val { font-weight: 700; font-size: .9rem; }

/* ── Sun / Moon ───────────────────────────────────────────── */
.clima-sun-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.sun-arc-container {
    position: relative;
    width: 100%;
    height: 90px;
    margin: 1rem 0;
}
.sun-arc-container svg {
    width: 100%;
    height: 100%;
    overflow: visible;
}

/* ── Precipitation chart ─────────────────────────────────── */
.clima-precip-chart {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.precip-bar-container {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    height: 80px;
    margin-top: .75rem;
}
.precip-bar-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .2rem;
    height: 100%;
    justify-content: flex-end;
}
.precip-bar {
    width: 100%;
    border-radius: 4px 4px 0 0;
    position: relative;
    transition: height 1.2s ease;
    cursor: pointer;
}
.precip-day-label { font-size: .62rem; color: var(--text-muted); white-space: nowrap; }
.precip-val-label { font-size: .62rem; color: var(--primary); font-weight: 600; }

/* ── Wind compass ────────────────────────────────────────── */
.wind-compass {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}
.compass-svg { width: 80px; height: 80px; flex-shrink: 0; }
.wind-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
    flex: 1;
}
.wind-info-item {
    background: var(--surface-2);
    border-radius: 8px;
    padding: .5rem;
    font-size: .75rem;
}
.wind-info-val { font-weight: 700; font-size: 1rem; color: var(--text); }

/* ── Loading state ────────────────────────────────────────── */
.clima-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 1rem;
    gap: 1rem;
    color: var(--text-muted);
}
.clima-pulse {
    width: 48px; height: 48px;
    border: 3px solid var(--border);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* ── Error state ─────────────────────────────────────────── */
.clima-error {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--text-muted);
}
.clima-error i { font-size: 3rem; margin-bottom: 1rem; color: var(--danger); }

/* ── Tooltip ─────────────────────────────────────────────── */
.tip { position: relative; cursor: help; }
.tip:after {
    content: attr(data-tip);
    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,.85);
    color: #fff;
    font-size: .72rem;
    padding: .3rem .6rem;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s;
    z-index: 1000;
}
.tip:hover:after { opacity: 1; }

@media (max-width: 768px) {
    .clima-hero { flex-direction: column; text-align: center; }
    .clima-hero-meta { justify-content: center; }
    .clima-hero-right { text-align: center; }
    .weather-icon-hero { font-size: 5rem; }
    .clima-hero-temp { font-size: 3.5rem; }
    .clima-forecast-row { grid-template-columns: 1fr 40px 120px 60px 60px; }
}

/* ── Map Layer Buttons ───────────────────────────────────── */
.map-layer-btn {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .35rem .85rem;
    border-radius: 20px;
    border: 1px solid var(--border);
    background: var(--surface-2, var(--surface));
    color: var(--text-muted);
    font-size: .78rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
}
.map-layer-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(var(--primary-rgb,59,130,246),.08);
}
.map-layer-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
}
</style>

<div class="clima-wrapper">

  <!-- Loading State -->
  <div id="climaLoading" class="clima-loading">
    <div class="clima-pulse"></div>
    <div>Carregando dados meteorológicos de Eldorado dos Ventos...</div>
  </div>

  <!-- Error State -->
  <div id="climaError" class="clima-error" style="display:none">
    <i class="fa-solid fa-cloud-bolt"></i>
    <h3>Conexão indisponível</h3>
    <p>Não foi possível carregar os dados climáticos.<br>Verifique a conexão do servidor e tente novamente.</p>
    <button class="btn btn-primary btn-sm" onclick="loadClima()" style="margin-top:1rem">
      <i class="fa-solid fa-rotate-right"></i> Tentar novamente
    </button>
  </div>

  <!-- Main Content (hidden until loaded) -->
  <div id="climaContent" style="display:none">

    <!-- ============= HERO ============= -->
    <div class="clima-hero" id="climaHero">
      <div class="clima-particles" id="particles"></div>

      <div class="weather-icon-hero" id="heroIcon">🌤️</div>

      <div class="clima-hero-info">
        <div class="clima-hero-city">
          <i class="fa-solid fa-location-dot" style="color:rgba(255,255,255,.5)"></i>
          <?= htmlspecialchars($cidade) ?>/<?= htmlspecialchars($estado) ?> — Brasil
          <span id="heroDate" style="margin-left:.5rem"></span>
        </div>
        <div class="clima-hero-temp" id="heroTemp">--°</div>
        <div class="clima-hero-desc" id="heroDesc">Carregando...</div>
        <div class="clima-hero-meta">
          <span class="clima-hero-meta-item">
            <i class="fa-solid fa-droplet"></i>
            <span>Humidade: <strong id="heroHum">--%</strong></span>
          </span>
          <span class="clima-hero-meta-item">
            <i class="fa-solid fa-wind"></i>
            <span id="heroWind">-- km/h</span>
          </span>
          <span class="clima-hero-meta-item">
            <i class="fa-solid fa-eye"></i>
            <span id="heroVis">-- km</span>
          </span>
          <span class="clima-hero-meta-item">
            <i class="fa-solid fa-gauge"></i>
            <span id="heroPressure">-- hPa</span>
          </span>
          <span class="clima-hero-meta-item">
            <i class="fa-solid fa-cloud-rain"></i>
            <span>Chuva: <strong id="heroPrecip">-- mm</strong></span>
          </span>
        </div>
      </div>

      <div class="clima-hero-right">
        <div class="clima-hero-feels">
          Sensação térmica
          <strong id="heroFeels">--°</strong>
        </div>
        <div style="margin-top:.5rem;font-size:.82rem;color:rgba(255,255,255,.7)">
          <i class="fa-solid fa-sun" style="color:#f0c040"></i> Max: <strong id="heroMax">--°</strong>
          &nbsp;
          <i class="fa-solid fa-snowflake" style="color:#a0d4f5"></i> Min: <strong id="heroMin">--°</strong>
        </div>
        <div class="clima-update-badge" onclick="loadClima()">
          <i class="fa-solid fa-rotate-right" id="refreshIcon"></i>
          <span id="lastUpdate">Atualizando...</span>
        </div>
      </div>
    </div>

    <!-- ============= CARDS ROW ============= -->
    <div class="clima-grid">

      <div class="clima-card tip" data-tip="Índice ultravioleta atual">
        <div class="clima-card-icon"><i class="fa-solid fa-sun" style="color:#f0c040"></i></div>
        <div class="clima-card-value" id="cardUv">--</div>
        <div class="clima-card-label">Índice UV</div>
        <div class="clima-card-sub" id="cardUvCat">--</div>
        <div class="clima-bar"><div class="clima-bar-fill" id="uvBar" style="width:0%;background:linear-gradient(90deg,#5cb85c,#f0ad4e,#e74c3c)"></div></div>
      </div>

      <div class="clima-card tip" data-tip="Umidade relativa do ar">
        <div class="clima-card-icon"><i class="fa-solid fa-droplet" style="color:#56b4f5"></i></div>
        <div class="clima-card-value" id="cardHum">--%</div>
        <div class="clima-card-label">Humidade</div>
        <div class="clima-bar"><div class="clima-bar-fill" id="humBar" style="width:0%;background:#56b4f5"></div></div>
      </div>

      <div class="clima-card tip" data-tip="Pressão atmosférica ao nível do mar">
        <div class="clima-card-icon"><i class="fa-solid fa-gauge" style="color:#a0b4c8"></i></div>
        <div class="clima-card-value" id="cardPressure">-- hPa</div>
        <div class="clima-card-label">Pressão</div>
        <div class="clima-card-sub" id="cardPressSub">--</div>
      </div>

      <div class="clima-card tip" data-tip="Velocidade e direção do vento">
        <div class="clima-card-icon"><i class="fa-solid fa-wind" style="color:#82c4e8"></i></div>
        <div class="clima-card-value" id="cardWind">-- km/h</div>
        <div class="clima-card-label">Vento</div>
        <div class="clima-card-sub" id="cardWindDir">Rajadas: -- km/h</div>
      </div>

      <div class="clima-card tip" data-tip="Visibilidade horizontal em km">
        <div class="clima-card-icon"><i class="fa-solid fa-eye" style="color:#7ec8a0"></i></div>
        <div class="clima-card-value" id="cardVis">-- km</div>
        <div class="clima-card-label">Visibilidade</div>
        <div class="clima-card-sub" id="cardVisSub">--</div>
      </div>

      <div class="clima-card tip" data-tip="Nebulosidade atual em %">
        <div class="clima-card-icon"><i class="fa-solid fa-cloud" style="color:#8899aa"></i></div>
        <div class="clima-card-value" id="cardCloud">--%</div>
        <div class="clima-card-label">Nebulosidade</div>
        <div class="clima-bar"><div class="clima-bar-fill" id="cloudBar" style="width:0%;background:#8899aa"></div></div>
      </div>

    </div>

    <!-- ============= HOURLY FORECAST ============= -->
    <div class="clima-section-title" style="margin-bottom:.5rem">
      <i class="fa-solid fa-clock" style="color:var(--primary)"></i>
      Previsão por Hora — Próximas 24h
    </div>
    <div class="clima-hourly-scroll" id="hourlyScroll"></div>

    <!-- ============= 7-DAY FORECAST ============= -->
    <div class="clima-section-title">
      <i class="fa-solid fa-calendar-week" style="color:var(--primary)"></i>
      Previsão 7 Dias
    </div>
    <div class="clima-forecast-list" id="forecastList"></div>

    <!-- ============= UV + AQI ============= -->
    <div class="clima-two-col">

      <!-- UV Panel -->
      <div class="clima-panel">
        <div class="clima-panel-title"><i class="fa-solid fa-sun"></i> Índice UV Máximo Hoje</div>
        <div class="uv-big" id="panelUvBig">--</div>
        <span class="uv-category" id="panelUvCat" style="background:#5cb85c20;color:#5cb85c">–</span>
        <div class="uv-meter">
          <div class="uv-pointer" id="uvPointer" style="left:0%"></div>
        </div>
        <div class="uv-labels">
          <span>Baixo</span><span>Moderado</span><span>Alto</span><span>Muito Alto</span><span>Extremo</span>
        </div>
        <div style="margin-top:.75rem;font-size:.78rem;color:var(--text-muted)" id="uvAdvice"></div>
      </div>

      <!-- AQI Panel -->
      <div class="clima-panel">
        <div class="clima-panel-title"><i class="fa-solid fa-wind"></i> Qualidade do Ar (AQI Europeu)</div>
        <div class="aqi-big" id="aqiBig">--</div>
        <span class="aqi-category" id="aqiCat" style="background:#5cb85c20;color:#5cb85c">Carregando...</span>
        <div class="aqi-details" id="aqiDetails"></div>
      </div>

    </div>

    <!-- ============= SUN ARC ============= -->
    <div class="clima-sun-panel">
      <div class="clima-panel-title"><i class="fa-solid fa-sun"></i> Nascer & Pôr do Sol</div>
      <div class="sun-arc-container">
        <svg id="sunArcSvg" viewBox="0 0 400 100" preserveAspectRatio="xMidYMid meet">
          <!-- Arc path -->
          <path id="sunArcPath" d="M 20 90 Q 200 -20 380 90" fill="none" stroke="rgba(255,200,50,.2)" stroke-width="3" stroke-dasharray="6 4"/>
          <!-- Active portion -->
          <path id="sunArcActive" d="M 20 90 Q 200 -20 380 90" fill="none" stroke="url(#sunGrad)" stroke-width="3" stroke-dashoffset="1000" stroke-dasharray="1000"/>
          <defs>
            <linearGradient id="sunGrad" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#f77f00"/>
              <stop offset="100%" stop-color="#f0c040"/>
            </linearGradient>
          </defs>
          <!-- Horizon -->
          <line x1="0" y1="90" x2="400" y2="90" stroke="var(--border)" stroke-width="1"/>
          <!-- Sunrise -->
          <text x="20" y="100" fill="var(--text-muted)" font-size="10" text-anchor="middle" id="sunriseLabel">--:--</text>
          <text x="20" y="88" font-size="14" text-anchor="middle">🌅</text>
          <!-- Sunset -->
          <text x="380" y="100" fill="var(--text-muted)" font-size="10" text-anchor="middle" id="sunsetLabel">--:--</text>
          <text x="380" y="88" font-size="14" text-anchor="middle">🌇</text>
          <!-- Current sun position -->
          <circle id="sunDot" cx="20" cy="90" r="10" fill="#f0c040" stroke="#fff" stroke-width="2" filter="url(#glow)"/>
          <defs>
            <filter id="glow">
              <feGaussianBlur stdDeviation="4" result="blur"/>
              <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
            </filter>
          </defs>
          <!-- Daylight label -->
          <text x="200" y="20" fill="var(--text-muted)" font-size="9" text-anchor="middle" id="daylightLabel">Duração do dia: --</text>
        </svg>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:.82rem;color:var(--text-muted);margin-top:.25rem">
        <span>🌅 Nascer: <strong id="sunriseText">--:--</strong></span>
        <span id="sunStatusText" style="color:var(--primary)"></span>
        <span>🌇 Pôr: <strong id="sunsetText">--:--</strong></span>
      </div>
    </div>

    <!-- ============= PRECIPITAÇÃO + WIND ============= -->
    <div class="clima-two-col">

      <!-- Precipitação -->
      <div class="clima-panel">
        <div class="clima-panel-title"><i class="fa-solid fa-cloud-rain"></i> Precipitação Semanal (mm)</div>
        <div class="precip-bar-container" id="precipChart"></div>
      </div>

      <!-- Wind Compass -->
      <div class="clima-panel">
        <div class="clima-panel-title"><i class="fa-solid fa-compass"></i> Vento Detalhado</div>
        <div class="wind-compass">
          <svg class="compass-svg" viewBox="0 0 80 80" id="compassSvg">
            <circle cx="40" cy="40" r="38" fill="none" stroke="var(--border)" stroke-width="2"/>
            <circle cx="40" cy="40" r="30" fill="none" stroke="var(--border)" stroke-width="1" stroke-dasharray="4 8"/>
            <text x="40" y="8" text-anchor="middle" fill="var(--text-muted)" font-size="8">N</text>
            <text x="40" y="77" text-anchor="middle" fill="var(--text-muted)" font-size="8">S</text>
            <text x="5" y="43" text-anchor="middle" fill="var(--text-muted)" font-size="8">O</text>
            <text x="75" y="43" text-anchor="middle" fill="var(--text-muted)" font-size="8">L</text>
            <!-- Arrow -->
            <g id="windArrow" style="transform-origin:40px 40px;transition:transform 1s ease">
              <polygon points="40,12 44,40 40,50 36,40" fill="var(--danger)"/>
              <polygon points="40,68 44,40 40,50 36,40" fill="var(--text-muted)"/>
            </g>
            <circle cx="40" cy="40" r="4" fill="var(--surface)"/>
          </svg>
          <div class="wind-info-grid" id="windInfoGrid"></div>
        </div>
      </div>

    </div>

    <!-- ============= MAPA METEOROLÓGICO ============= -->
    <div class="clima-section-title" style="margin-top:.5rem">
      <i class="fa-solid fa-satellite-dish" style="color:var(--primary)"></i>
      Mapa Meteorológico — Eldorado dos Ventos e Região
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:1.5rem">

      <!-- Overlay switcher -->
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;padding:.85rem 1rem;border-bottom:1px solid var(--border);align-items:center">
        <span style="font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-right:.25rem">Camada:</span>
        <button class="map-layer-btn active" onclick="windySwitch('rain')" id="wBtnRain">
          <i class="fa-solid fa-cloud-showers-heavy"></i> Chuva
        </button>
        <button class="map-layer-btn" onclick="windySwitch('wind')" id="wBtnWind">
          <i class="fa-solid fa-wind"></i> Vento
        </button>
        <button class="map-layer-btn" onclick="windySwitch('temp')" id="wBtnTemp">
          <i class="fa-solid fa-temperature-half"></i> Temperatura
        </button>
        <button class="map-layer-btn" onclick="windySwitch('clouds')" id="wBtnClouds">
          <i class="fa-solid fa-cloud"></i> Nuvens
        </button>
        <button class="map-layer-btn" onclick="windySwitch('satellite')" id="wBtnSat">
          <i class="fa-solid fa-satellite"></i> Satélite
        </button>
        <button class="map-layer-btn" onclick="windySwitch('cosc')" id="wBtnAir">
          <i class="fa-solid fa-smog"></i> Qualidade do Ar
        </button>
        <div style="margin-left:auto">
          <a href="https://www.windy.com/?rain,-23.5505,-46.6333,14" target="_blank"
             style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;color:var(--primary);text-decoration:none;border:1px solid var(--primary);border-radius:6px;padding:.3rem .75rem">
            <i class="fa-solid fa-up-right-from-square"></i> Abrir no Windy
          </a>
        </div>
      </div>

      <!-- Windy embed  -->
      <iframe id="windyFrame"
        src="https://embed.windy.com/embed2.html?lat=-23.5505&lon=-46.6333&detailLat=-23.5505&detailLon=-46.6333&zoom=14&level=surface&overlay=rain&product=ecmwf&menu=&message=true&marker=true&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=km%2Fh&metricTemp=%C2%B0C&radarRange=-1"
        frameborder="0" width="100%" height="540"
        style="display:block;border:0" allowfullscreen loading="lazy">
      </iframe>

      <!-- Footer -->
      <div style="padding:.65rem 1rem;display:flex;gap:1rem;flex-wrap:wrap;border-top:1px solid var(--border);align-items:center;font-size:.72rem;color:var(--text-muted)">
        <span><i class="fa-solid fa-circle-info" style="color:var(--primary)"></i>
          Modelo: <strong style="color:var(--text)">ECMWF</strong> — use <i class="fa-solid fa-plus"></i>/<i class="fa-solid fa-minus"></i> no mapa para ajustar o zoom
        </span>
        <span style="margin-left:auto"><i class="fa-solid fa-location-dot" style="color:var(--danger)"></i> Eldorado dos Ventos — 23.550°S 46.633°O</span>
      </div>

    </div><!-- /mapa panel -->





  </div><!-- /climaContent -->

</div><!-- /clima-wrapper -->

<script>
// ============================================================
//  LEAFLET MAP + OpenWeatherMap Overlay
// ============================================================
const WINDY_BASE = 'https://embed.windy.com/embed2.html?lat=-20.271&lon=-50.543&detailLat=-20.271&detailLon=-50.543&zoom=14&level=surface&product=ecmwf&menu=&message=true&marker=true&calendar=now&pressure=&type=map&location=coordinates&detail=&metricWind=km%2Fh&metricTemp=%C2%B0C&radarRange=-1&overlay=';
const WINDY_BTNS = { rain:'wBtnRain', wind:'wBtnWind', temp:'wBtnTemp', clouds:'wBtnClouds', satellite:'wBtnSat', cosc:'wBtnAir' };

function windySwitch(overlay) {
  const frame = document.getElementById('windyFrame');
  if (frame) frame.src = WINDY_BASE + overlay;
  Object.keys(WINDY_BTNS).forEach(k => {
    const el = document.getElementById(WINDY_BTNS[k]);
    if (el) el.classList.toggle('active', k === overlay);
  });
}

function initMapWhenReady() {}



// ============================================================
//  WEATHER CODES → FA icon class + description (WMO)
// ============================================================
const WMO = {
  0:'fa-sun:Céu limpo',
  1:'fa-sun:Predominantemente limpo',
  2:'fa-cloud-sun:Parcialmente nublado',
  3:'fa-cloud:Nublado',
  45:'fa-smog:Névoa',48:'fa-smog:Névoa com gelo',
  51:'fa-cloud-drizzle:Garoa leve',53:'fa-cloud-drizzle:Garoa moderada',55:'fa-cloud-drizzle:Garoa intensa',
  56:'fa-icicles:Garoa com gelo leve',57:'fa-icicles:Garoa com gelo forte',
  61:'fa-cloud-rain:Chuva fraca',63:'fa-cloud-rain:Chuva moderada',65:'fa-cloud-showers-heavy:Chuva forte',
  66:'fa-icicles:Chuva com gelo fraca',67:'fa-icicles:Chuva com gelo forte',
  71:'fa-snowflake:Neve fraca',73:'fa-snowflake:Neve moderada',75:'fa-snowflake:Neve forte',
  77:'fa-cloud-meatball:Granizo',
  80:'fa-cloud-sun-rain:Chuva passageira fraca',81:'fa-cloud-rain:Chuva passageira moderada',82:'fa-cloud-showers-heavy:Chuva passageira forte',
  85:'fa-snowflake:Neve passageira',86:'fa-snowflake:Neve passageira forte',
  95:'fa-cloud-bolt:Tempestade',96:'fa-cloud-bolt:Tempestade com granizo',99:'fa-cloud-bolt:Tempestade severa',
};

function wmo(code) {
  const v = WMO[code] || 'fa-temperature-half:Sem dados';
  const [faClass, desc] = v.split(':');
  return { faClass, desc };
}


// Wind direction
const windDirs = ['N','NNE','NE','ENE','L','ESE','SE','SSE','S','SSO','SO','OSO','O','ONO','NO','NNO'];
function windDir(deg) { return windDirs[Math.round(deg / 22.5) % 16] || '–'; }

// UV
function uvInfo(uv) {
  if (uv < 3) return { cat:'Baixo', color:'#5cb85c', advice:'Proteção mínima necessária. Aproveite ao ar livre!', pct: uv/11*100 };
  if (uv < 6) return { cat:'Moderado', color:'#f0ad4e', advice:'Use protetor solar FPS 30+ e óculos escuros.', pct: uv/11*100 };
  if (uv < 8) return { cat:'Alto', color:'#e67e22', advice:'Use protetor solar FPS 50+, chapéu e roupas de proteção.', pct: uv/11*100 };
  if (uv < 11) return { cat:'Muito Alto', color:'#e74c3c', advice:'Evite exposição solar entre 10h–16h.', pct: uv/11*100 };
  return { cat:'Extremo', color:'#9b59b6', advice:'Perigo! Proteja-se ao máximo e evite sair ao sol.', pct:100 };
}

// AQI
function aqiInfo(aqi) {
  if (aqi <= 20) return { cat:'Muito Bom', color:'#27ae60' };
  if (aqi <= 40) return { cat:'Bom', color:'#5cb85c' };
  if (aqi <= 60) return { cat:'Moderado', color:'#f0ad4e' };
  if (aqi <= 80) return { cat:'Ruim', color:'#e67e22' };
  if (aqi <= 100) return { cat:'Muito Ruim', color:'#e74c3c' };
  return { cat:'Extremamente Ruim', color:'#9b59b6' };
}

// Format time HH:MM from ISO string
function fmtTime(iso) {
  if (!iso) return '--:--';
  return iso.length >= 16 ? iso.substring(11, 16) : '--:--';
}

// Short day names
const days = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
const months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];

function fmtDay(dateStr) {
  const d = new Date(dateStr + 'T12:00:00');
  const today = new Date(); today.setHours(12,0,0,0);
  if (d.toDateString() === today.toDateString()) return 'Hoje';
  const tomorrow = new Date(today); tomorrow.setDate(today.getDate()+1);
  if (d.toDateString() === tomorrow.toDateString()) return 'Amanhã';
  return days[d.getDay()] + ' ' + d.getDate() + '/' + (d.getMonth()+1);
}

// Pressure interpretation
function pressureInfo(hpa) {
  if (hpa > 1022) return 'Alta pressão ▲';
  if (hpa < 1009) return 'Baixa pressão ▼';
  return 'Pressão normal';
}

// Visibility interpretation
function visInfo(m) {
  const km = (m / 1000).toFixed(1);
  if (m >= 10000) return { km, label: 'Excelente' };
  if (m >= 5000) return { km, label: 'Boa' };
  if (m >= 1000) return { km, label: 'Moderada' };
  return { km, label: 'Reduzida' };
}

// Animate SVG sun arc
function animateSunArc(sunriseStr, sunsetStr) {
  const now = new Date();
  const todayBase = now.toISOString().substring(0,10);
  const sunrise = new Date(todayBase + 'T' + sunriseStr + ':00');
  const sunset  = new Date(todayBase + 'T' + sunsetStr  + ':00');

  const totalMs = sunset - sunrise;
  const elapsedMs = now - sunrise;
  const ratio = Math.max(0, Math.min(1, elapsedMs / totalMs));

  // Sun position on arc (parametric bezier: P0=20,90 P1=200,-20 P2=380,90)
  const t = ratio;
  const mt = 1 - t;
  const x = mt*mt*20 + 2*mt*t*200 + t*t*380;
  const y = mt*mt*90 + 2*mt*t*(-20) + t*t*90;

  const sun = document.getElementById('sunDot');
  if (sun) { sun.setAttribute('cx', x); sun.setAttribute('cy', y); }

  // Active arc length
  const pathEl = document.getElementById('sunArcActive');
  if (pathEl) {
    const len = pathEl.getTotalLength ? pathEl.getTotalLength() : 1000;
    pathEl.style.strokeDasharray = len;
    pathEl.style.strokeDashoffset = len * (1 - ratio);
  }

  // Time labels
  const sr = document.getElementById('sunriseLabel');
  const ss = document.getElementById('sunsetLabel');
  const dl = document.getElementById('daylightLabel');
  if (sr) sr.textContent = sunriseStr;
  if (ss) ss.textContent = sunsetStr;

  // Daylight duration
  const dlMin = Math.round(totalMs / 60000);
  const dlH = Math.floor(dlMin / 60);
  const dlM = dlMin % 60;
  if (dl) dl.textContent = `Duração do dia: ${dlH}h ${dlM}min`;

  // Texts
  const srText = document.getElementById('sunriseText');
  const ssText = document.getElementById('sunsetText');
  const statusText = document.getElementById('sunStatusText');
  if (srText) srText.textContent = sunriseStr;
  if (ssText) ssText.textContent = sunsetStr;
  if (statusText) {
    if (now < sunrise) statusText.innerHTML = '<i class="fa-solid fa-moon"></i> Antes do nascer';
    else if (now > sunset) statusText.innerHTML = '<i class="fa-solid fa-moon"></i> Após o pôr do sol';
    else {
      const remaining = Math.round((sunset - now) / 60000);
      statusText.innerHTML = `<i class="fa-solid fa-sun"></i> Faltam ${Math.floor(remaining/60)}h ${remaining%60}min para o pôr`;
    }
  }
}

// ============================================================
//  MAIN LOAD FUNCTION
// ============================================================
async function loadClima() {
  const icon = document.getElementById('refreshIcon');
  if (icon) icon.classList.add('spin');

  try {
    const res = await fetch('<?= BASE_URL ?>/clima/dados');
    const data = await res.json();

    if (data.error || !data.weather) throw new Error(data.error || 'Sem dados');

    renderClima(data);

    document.getElementById('climaLoading').style.display = 'none';
    document.getElementById('climaError').style.display = 'none';
    document.getElementById('climaContent').style.display = 'block';

    const now = new Date();
    const upd = document.getElementById('lastUpdate');
    if (upd) upd.textContent = 'Atualizado às ' + now.toLocaleTimeString('pt-BR');

  } catch(e) {
    console.error(e);
    document.getElementById('climaLoading').style.display = 'none';
    document.getElementById('climaError').style.display = 'block';
    document.getElementById('climaContent').style.display = 'none';
  } finally {
    if (icon) icon.classList.remove('spin');
  }
}

function renderClima(data) {
  const w = data.weather;
  const c = w.current;
  const daily = w.daily;
  const hourly = w.hourly;
  const aq = data.air_quality?.current || {};

  const isDay = c.is_day === 1;
  const code = c.weather_code;
  const { faClass, desc } = wmo(code);

  // ── Hero ──
  document.getElementById('heroIcon').innerHTML = `<i class="fa-solid ${faClass}" style="color:inherit"></i>`;
  document.getElementById('heroIcon').style.fontSize = '6rem';
  document.getElementById('heroIcon').style.textShadow = 'none';
  document.getElementById('heroTemp').textContent = Math.round(c.temperature_2m) + '°C';
  document.getElementById('heroDesc').textContent = desc;
  document.getElementById('heroHum').textContent = c.relative_humidity_2m + '%';
  document.getElementById('heroWind').textContent = `Vento ${c.wind_speed_10m} km/h — ${windDir(c.wind_direction_10m)}`;
  const visData = visInfo(c.visibility);
  document.getElementById('heroVis').textContent = visData.km + ' km';
  document.getElementById('heroPressure').textContent = Math.round(c.pressure_msl) + ' hPa';
  document.getElementById('heroPrecip').textContent = c.precipitation.toFixed(1) + ' mm';
  document.getElementById('heroFeels').textContent = Math.round(c.apparent_temperature) + '°C';

  // Hero daily min/max
  const todayIdx = 0;
  document.getElementById('heroMax').textContent = Math.round(daily.temperature_2m_max[0]) + '°C';
  document.getElementById('heroMin').textContent = Math.round(daily.temperature_2m_min[0]) + '°C';

  // Hero date
  const now = new Date();
  const dateStr = now.toLocaleDateString('pt-BR', { weekday:'long', day:'numeric', month:'long' });
  document.getElementById('heroDate').textContent = '| ' + dateStr.charAt(0).toUpperCase() + dateStr.slice(1);

  // Hero background class
  const hero = document.getElementById('climaHero');
  hero.className = 'clima-hero';
  if ([61,63,65,80,81,82,95,96,99].includes(code)) hero.classList.add('rain');
  else if ([3,45,48].includes(code)) hero.classList.add('cloudy');
  else if (isDay) hero.classList.add('day');

  // Particles
  renderParticles(code, isDay);

  // ── Cards ──
  const uv = c.uv_index;
  const uvI = uvInfo(uv);
  document.getElementById('cardUv').textContent = uv.toFixed(1);
  document.getElementById('cardUvCat').textContent = uvI.cat;
  setTimeout(() => document.getElementById('uvBar').style.width = Math.min(uvI.pct, 100) + '%', 100);

  document.getElementById('cardHum').textContent = c.relative_humidity_2m + '%';
  setTimeout(() => document.getElementById('humBar').style.width = c.relative_humidity_2m + '%', 100);

  document.getElementById('cardPressure').textContent = Math.round(c.pressure_msl) + ' hPa';
  document.getElementById('cardPressSub').textContent = pressureInfo(c.pressure_msl);

  document.getElementById('cardWind').textContent = c.wind_speed_10m + ' km/h';
  document.getElementById('cardWindDir').textContent = `${windDir(c.wind_direction_10m)} — Rajadas: ${c.wind_gusts_10m} km/h`;

  document.getElementById('cardVis').textContent = visData.km + ' km';
  document.getElementById('cardVisSub').textContent = visData.label;

  document.getElementById('cardCloud').textContent = c.cloud_cover + '%';
  setTimeout(() => document.getElementById('cloudBar').style.width = c.cloud_cover + '%', 100);

  // ── Hourly ──
  renderHourly(hourly, w.hourly_units);

  // ── Daily Forecast ──
  renderForecast(daily);

  // ── UV Panel ──
  const todayMaxUv = daily.uv_index_max[0] || 0;
  const uvpI = uvInfo(todayMaxUv);
  document.getElementById('panelUvBig').textContent = todayMaxUv.toFixed(1);
  const uvCatEl = document.getElementById('panelUvCat');
  uvCatEl.textContent = uvpI.cat;
  uvCatEl.style.background = uvpI.color + '20';
  uvCatEl.style.color = uvpI.color;
  setTimeout(() => {
    document.getElementById('uvPointer').style.left = Math.min(uvpI.pct, 100) + '%';
  }, 200);
  document.getElementById('uvAdvice').innerHTML = '<i class="fa-solid fa-lightbulb" style="color:#f0ad4e"></i> ' + uvpI.advice;

  // ── AQI Panel ──
  renderAqi(aq);

  // ── Sun Arc ──
  const sunrise = fmtTime(daily.sunrise[0]);
  const sunset  = fmtTime(daily.sunset[0]);
  animateSunArc(sunrise, sunset);

  // ── Precipitation Chart ──
  renderPrecipChart(daily);

  // ── Wind Compass ──
  renderWindCompass(c);

  // Init Leaflet map after content is displayed
  setTimeout(initMapWhenReady, 300);
}

function renderParticles(code, isDay) {
  const container = document.getElementById('particles');
  container.innerHTML = '';
  const count = [61,63,65,80,81,82,95,96,99].includes(code) ? 20 : (isDay ? 15 : 30);
  for (let i = 0; i < count; i++) {
    const span = document.createElement('span');
    const size = Math.random() * 3 + 1;
    const rain = [61,63,65,80,81,82,95,96,99].includes(code);
    span.style.cssText = `
      width:${size}px; height:${rain ? size*3 : size}px;
      left:${Math.random()*100}%;
      top:${Math.random()*100}%;
      animation-delay:${Math.random()*4}s;
      animation-duration:${Math.random()*3+2}s;
      background:${rain ? 'rgba(150,200,255,.4)' : 'rgba(255,255,255,.5)'};
      border-radius:${rain ? '2px' : '50%'};
    `;
    container.appendChild(span);
  }
}

function renderHourly(hourly, units) {
  const container = document.getElementById('hourlyScroll');
  container.innerHTML = '';
  const now = new Date();
  const currentHour = now.getHours();

  // Find current hour index
  const times = hourly.time || [];
  let startIdx = 0;
  for (let i = 0; i < times.length; i++) {
    const h = parseInt(times[i].substring(11, 13));
    const d = new Date(times[i]);
    if (d >= now) { startIdx = i; break; }
  }

  const end = Math.min(startIdx + 24, times.length);
  for (let i = startIdx; i < end; i++) {
    const timeStr = times[i].substring(11, 16);
    const temp = Math.round(hourly.temperature_2m[i]);
    const { faClass } = wmo(hourly.weather_code[i]);
    const rain = hourly.precipitation_probability[i];
    const isNow = i === startIdx;

    const card = document.createElement('div');
    card.className = 'clima-hour-card' + (isNow ? ' now' : '');
    card.innerHTML = `
      <div class="clima-hour-time">${isNow ? 'Agora' : timeStr}</div>
      <div class="clima-hour-icon"><i class="fa-solid ${faClass}"></i></div>
      <div class="clima-hour-temp">${temp}°</div>
      <div class="clima-hour-rain"><i class="fa-solid fa-droplet" style="font-size:.6rem"></i>${rain}%</div>
    `;
    container.appendChild(card);
  }
}

function renderForecast(daily) {
  const list = document.getElementById('forecastList');
  list.innerHTML = '';
  const dates = daily.time || [];
  const maxTemps = daily.temperature_2m_max || [];
  const minTemps = daily.temperature_2m_min || [];
  const codes = daily.weather_code || [];
  const precipProb = daily.precipitation_probability_max || [];
  const precipSum = daily.precipitation_sum || [];

  // global min/max for bar scaling
  const gMin = Math.min(...minTemps);
  const gMax = Math.max(...maxTemps);

  const today = new Date().toISOString().substring(0,10);

  dates.forEach((date, i) => {
    const isToday = date === today;
    const { faClass, desc } = wmo(codes[i]);
    const maxT = Math.round(maxTemps[i]);
    const minT = Math.round(minTemps[i]);
    const prob = precipProb[i] || 0;
    const sum  = (precipSum[i] || 0).toFixed(1);

    // Bar width
    const range = gMax - gMin || 1;
    const barLeft = ((minT - gMin) / range * 100).toFixed(1);
    const barWidth = ((maxT - minT) / range * 100).toFixed(1);

    const row = document.createElement('div');
    row.className = 'clima-forecast-row' + (isToday ? ' today' : '');
    row.innerHTML = `
      <div>
        <div class="clima-forecast-day">${fmtDay(date)}</div>
        <div class="clima-forecast-desc"><i class="fa-solid ${faClass}" style="width:16px"></i> ${desc}</div>
      </div>
      <div style="font-size:1.1rem;text-align:center;color:var(--primary)"><i class="fa-solid ${faClass}"></i></div>
      <div class="clima-forecast-bar">
        <span style="font-size:.8rem;color:var(--info);min-width:30px">${minT}°</span>
        <div style="flex:1;height:6px;background:var(--border);border-radius:10px;position:relative">
          <div style="position:absolute;left:${barLeft}%;width:${barWidth}%;height:100%;background:linear-gradient(90deg,#56b4f5,#f59a00);border-radius:10px"></div>
        </div>
        <span style="font-size:.8rem;color:var(--warning);min-width:30px;text-align:right">${maxT}°</span>
      </div>
      <div style="text-align:center;font-size:.8rem"><i class="fa-solid fa-droplet" style="color:#56b4f5"></i> ${prob}%</div>
      <div style="text-align:right;font-size:.8rem;color:var(--text-muted)">${sum} mm</div>
    `;
    list.appendChild(row);
  });
}

function renderAqi(aq) {
  const aqi = aq.european_aqi ?? null;
  const aqiBig = document.getElementById('aqiBig');
  const aqiCat = document.getElementById('aqiCat');
  const aqiDet = document.getElementById('aqiDetails');

  if (aqi === null) {
    aqiBig.textContent = 'N/A';
    aqiCat.textContent = 'Indisponível';
    return;
  }

  const info = aqiInfo(aqi);
  aqiBig.textContent = aqi;
  aqiBig.style.color = info.color;
  aqiCat.textContent = info.cat;
  aqiCat.style.background = info.color + '20';
  aqiCat.style.color = info.color;

  const pollutants = [
    { key:'pm2_5', label:'PM2.5', unit:'μg/m³' },
    { key:'pm10', label:'PM10', unit:'μg/m³' },
    { key:'nitrogen_dioxide', label:'NO₂', unit:'μg/m³' },
    { key:'ozone', label:'O₃', unit:'μg/m³' },
  ];

  aqiDet.innerHTML = pollutants.map(p => `
    <div class="aqi-detail-item">
      <div class="aqi-detail-val">${aq[p.key]?.toFixed(1) ?? 'N/A'} <small>${p.unit}</small></div>
      <div style="color:var(--text-muted);font-size:.7rem">${p.label}</div>
    </div>
  `).join('');
}

function renderPrecipChart(daily) {
  const container = document.getElementById('precipChart');
  container.innerHTML = '';
  const dates = daily.time || [];
  const sums  = daily.precipitation_sum || [];
  const maxV  = Math.max(...sums, 1);

  dates.forEach((date, i) => {
    const v = sums[i] || 0;
    const h = Math.max((v / maxV) * 100, 2);
    const hue = v < 1 ? 200 : v < 5 ? 210 : v < 15 ? 220 : 230;
    const today = new Date().toISOString().substring(0,10) === date;

    const wrap = document.createElement('div');
    wrap.className = 'precip-bar-wrap';
    wrap.innerHTML = `
      <div class="precip-val-label">${v > 0 ? v.toFixed(1)+'mm' : ''}</div>
      <div class="precip-bar" style="height:${h}%;background:hsl(${hue},70%,${today?'45':'60'}%);box-shadow:${today?'0 0 8px rgba(70,130,220,.5)':''}"
           title="${fmtDay(date)}: ${v} mm"></div>
      <div class="precip-day-label">${fmtDay(date).substring(0,3)}</div>
    `;
    container.appendChild(wrap);
  });
}

function renderWindCompass(c) {
  const arrow = document.getElementById('windArrow');
  if (arrow) {
    const deg = c.wind_direction_10m;
    arrow.style.transform = `rotate(${deg}deg)`;
  }

  const grid = document.getElementById('windInfoGrid');
  if (grid) {
    const dir = windDir(c.wind_direction_10m);
    grid.innerHTML = `
      <div class="wind-info-item">
        <div class="wind-info-val">${c.wind_speed_10m} km/h</div>
        <div style="color:var(--text-muted);font-size:.7rem">Velocidade</div>
      </div>
      <div class="wind-info-item">
        <div class="wind-info-val">${c.wind_gusts_10m} km/h</div>
        <div style="color:var(--text-muted);font-size:.7rem">Rajadas</div>
      </div>
      <div class="wind-info-item">
        <div class="wind-info-val">${dir}</div>
        <div style="color:var(--text-muted);font-size:.7rem">Direção</div>
      </div>
      <div class="wind-info-item">
        <div class="wind-info-val">${c.wind_direction_10m}°</div>
        <div style="color:var(--text-muted);font-size:.7rem">Graus</div>
      </div>
    `;
  }
}

// Auto-refresh every 10 minutes
loadClima();
setInterval(loadClima, 10 * 60 * 1000);
</script>
