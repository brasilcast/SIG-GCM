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
 */$pageTitle = 'Consultas Integradas';
?>

<style>
/* ============================================================ */
/*  CONSULTAS - LAYOUT PREMIUM                                 */
/* ============================================================ */

.consultas-wrapper {
  max-width: 1400px;
  margin: 0 auto;
}

.consultas-header {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border);
}

.consultas-header-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: var(--primary-dark);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
}

.consultas-header-titles h1 {
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--text);
  margin: 0 0 0.25rem 0;
  letter-spacing: -0.02em;
}

.consultas-header-titles p {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin: 0;
}

.consultas-grid {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2rem;
  align-items: start;
}

@media(max-width: 900px) {
  .consultas-grid { grid-template-columns: 1fr; }
}

/* ============================================================ */
/*  MENU LATERAL (TABS)                                        */
/* ============================================================ */
.consulta-menu {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 0.75rem;
  position: sticky;
  top: 80px;
}

.consulta-menu-item {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.85rem 1rem;
  color: var(--text-muted);
  font-weight: 600;
  font-size: 0.95rem;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 0.25rem;
  border: 1px solid transparent;
}

.consulta-menu-item:last-child { margin-bottom: 0; }

.consulta-menu-item:hover {
  background: var(--hover);
  color: var(--text);
}

.consulta-menu-item.active {
  background: var(--primary);
  color: #fff;
  border-color: rgba(var(--primary-rgb), 0.2);
}

.consulta-menu-item i {
  width: 20px;
  text-align: center;
  font-size: 1.1rem;
}

.consulta-menu-item.active i {
  color: #fff;
}

/* ============================================================ */
/*  CONTEÚDO DAS ABAS                                          */
/* ============================================================ */
.consulta-tab-content {
  display: none;
  animation: fadeIn 0.3s ease;
}

.consulta-tab-content.active {
  display: block;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.consulta-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 2rem;
  position: relative;
  overflow: hidden;
}

.consulta-card-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin: 0 0 1.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.consulta-card-title i {
  color: var(--primary);
}

/* ============================================================ */
/*  FORMULÁRIOS E BUSCA                                        */
/* ============================================================ */
.consulta-form {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: flex-end;
  flex-wrap: wrap;
}

.form-group-flex {
  flex: 1;
  min-width: 200px;
}

.consulta-input {
  width: 100%;
  background: var(--bg);
  border: 1px solid var(--border);
  color: var(--text);
  padding: 0.75rem 1rem;
  border-radius: 10px;
  font-size: 1rem;
  transition: border-color 0.2s;
  outline: none;
}

.consulta-input:focus {
  border-color: var(--primary);
}

.consulta-btn {
  background: var(--primary);
  color: #fff;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  height: 46px; /* match input height */
}

.consulta-btn:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}

.consulta-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* ============================================================ */
/*  RESULTADOS                                                 */
/* ============================================================ */
.consulta-result {
  display: none;
  margin-top: 2rem;
  border-top: 1px dashed var(--border);
  padding-top: 2rem;
}

.consulta-result.active { display: block; }

.result-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 1rem;
}

.result-item {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1.25rem;
}

.result-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted);
  font-weight: 700;
  margin-bottom: 0.4rem;
}

.result-value {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text);
  word-break: break-word;
}

.result-value.highlight {
  color: var(--primary);
  font-size: 1.4rem;
}

.result-value.success {
  color: var(--success);
}
.result-value.danger {
  color: var(--danger);
}

/* ============================================================ */
/*  AVISO LEGAL (Badge)                                        */
/* ============================================================ */
.legal-notice {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  background: rgba(var(--warning-rgb), 0.1);
  border: 1px solid rgba(var(--warning-rgb), 0.2);
  padding: 1rem 1.25rem;
  border-radius: 12px;
  margin-top: 2rem;
  color: var(--text-muted);
  font-size: 0.85rem;
  line-height: 1.5;
}

.legal-notice i {
  color: var(--warning);
  font-size: 1.1rem;
  margin-top: 0.1rem;
}

.legal-notice strong {
  color: var(--text);
}

/* Loading Spinner */
.loader {
  display: none;
  width: 24px;
  height: 24px;
  border: 3px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Error Message */
.error-msg {
  display: none;
  background: rgba(var(--danger-rgb), 0.1);
  color: var(--danger);
  padding: 1rem;
  border-radius: 10px;
  border: 1px solid rgba(var(--danger-rgb), 0.2);
  margin-bottom: 1.5rem;
  font-weight: 500;
}
</style>


<div class="consultas-wrapper">

  <!-- ==================== HEADER ==================== -->
  <div class="consultas-header">
    <div class="consultas-header-icon">
      <i class="fa-solid fa-magnifying-glass-chart"></i>
    </div>
    <div class="consultas-header-titles">
      <h1>Consultas Integradas</h1>
      <p>Acesso rápido a APIs oficiais e públicas para consulta de dados institucionais.</p>
    </div>
  </div>

  <div class="consultas-grid">

    <!-- ==================== MENU LATERAL ==================== -->
    <div class="consulta-menu">
      <div class="consulta-menu-item active" onclick="switchTab('fipe')">
        <i class="fa-solid fa-car"></i> Veículo (Tabela FIPE)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('cep')">
        <i class="fa-solid fa-map-location-dot"></i> Endereço (CEP)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('cnpj')">
        <i class="fa-solid fa-building"></i> Empresas (CNPJ)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('validacao')">
        <i class="fa-solid fa-id-card"></i> Validador CPF/CNPJ
      </div>
      <div class="consulta-menu-item" onclick="switchTab('feriados')">
        <i class="fa-regular fa-calendar-days"></i> Feriados Nacionais
      </div>
      <div class="consulta-menu-item" onclick="switchTab('imei')">
        <i class="fa-solid fa-mobile-screen"></i> Celular Roubado (IMEI)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('bnmp')">
        <i class="fa-solid fa-handcuffs"></i> Mandados (BNMP)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('sinesp')">
        <i class="fa-solid fa-car-on"></i> Veículo Roubado (SINESP)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('cnpd')">
        <i class="fa-solid fa-person-circle-question"></i> Desaparecidos (CNPD)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('receita')">
        <i class="fa-solid fa-file-invoice-dollar"></i> CPF Cadastral (Receita)
      </div>
      <div class="consulta-menu-item" onclick="switchTab('ibge')">
        <i class="fa-solid fa-city"></i> Dados do Município
      </div>
    </div>


    <!-- ==================== CONTEÚDO TABS ==================== -->
    <div class="consulta-tabs">

      <!-- ==========================================
           ABA 1: FIPE (Veículos)
      =============================================== -->
      <div class="consulta-tab-content active" id="tab-fipe">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-car"></i> Consulta de Veículo (Tabela FIPE)</h2>
          
          <div class="error-msg" id="fipe-error"></div>

          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label">Tipo de Veículo</label>
              <select id="fipe-tipo" class="consulta-input" onchange="loadFipeMarcas()">
                <option value="carros">Carros</option>
                <option value="motos">Motos</option>
                <option value="caminhoes">Caminhões</option>
              </select>
            </div>
            <div class="form-group-flex">
              <label class="result-label">Marca</label>
              <select id="fipe-marca" class="consulta-input" disabled onchange="loadFipeModelos()">
                <option value="">Selecione uma marca...</option>
              </select>
            </div>
          </div>
          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label">Modelo</label>
              <select id="fipe-modelo" class="consulta-input" disabled onchange="loadFipeAnos()">
                <option value="">Selecione um modelo...</option>
              </select>
            </div>
            <div class="form-group-flex">
              <label class="result-label">Ano</label>
              <select id="fipe-ano" class="consulta-input" disabled>
                <option value="">Selecione o ano...</option>
              </select>
            </div>
            <button class="consulta-btn" id="fipe-btn" onclick="consultarFipe()" disabled>
              <span class="btn-text">Consultar Preço</span>
              <div class="loader"></div>
            </button>
          </div>

          <div class="consulta-result" id="fipe-result">
            <div class="result-grid">
              <div class="result-item" style="grid-column: 1 / -1; background: var(--primary-dark); border-color: var(--primary);">
                <div class="result-label" style="color: var(--primary);">Valor de Mercado (FIPE)</div>
                <div class="result-value highlight" id="fipe-valor"></div>
              </div>
              <div class="result-item"><div class="result-label">Marca</div><div class="result-value" id="fipe-res-marca"></div></div>
              <div class="result-item"><div class="result-label">Modelo</div><div class="result-value" id="fipe-res-modelo"></div></div>
              <div class="result-item"><div class="result-label">Ano Modelo</div><div class="result-value" id="fipe-res-ano"></div></div>
              <div class="result-item"><div class="result-label">Combustível</div><div class="result-value" id="fipe-res-comb"></div></div>
              <div class="result-item"><div class="result-label">Código FIPE</div><div class="result-value" id="fipe-res-cod"></div></div>
              <div class="result-item"><div class="result-label">Mês Referência</div><div class="result-value" id="fipe-res-mes"></div></div>
            </div>
          </div>

          <div class="legal-notice">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Fundação Instituto de Pesquisas Econômicas (FIPE) via BrasilAPI.</strong><br>
              A Tabela Fipe expressa preços médios de veículos anunciados pelos vendedores, no mercado nacional, servindo de parâmetro para negociações ou avaliações operacionais. Uso público e gratuito.
            </div>
          </div>
        </div>
      </div>


      <!-- ==========================================
           ABA 2: CEP (Endereço)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-cep">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-map-location-dot"></i> Consulta de Endereço (CEP)</h2>
          
          <div class="error-msg" id="cep-error"></div>

          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label">CEP (Apenas números)</label>
              <input type="text" id="cep-input" class="consulta-input" placeholder="Ex: 15700000" maxlength="9" onkeypress="handleEnter(event, consultarCep)">
            </div>
            <button class="consulta-btn" id="cep-btn" onclick="consultarCep()">
              <span class="btn-text">Consultar</span>
              <div class="loader"></div>
            </button>
          </div>

          <div class="consulta-result" id="cep-result">
            <div class="result-grid">
              <div class="result-item" style="grid-column: 1 / -1;">
                <div class="result-label">Logradouro</div>
                <div class="result-value highlight" id="cep-res-rua"></div>
              </div>
              <div class="result-item"><div class="result-label">Bairro</div><div class="result-value" id="cep-res-bairro"></div></div>
              <div class="result-item"><div class="result-label">Cidade / UF</div><div class="result-value" id="cep-res-cidade"></div></div>
              <div class="result-item"><div class="result-label">CEP</div><div class="result-value" id="cep-res-cep"></div></div>
              <div class="result-item"><div class="result-label">Código IBGE</div><div class="result-value" id="cep-res-ibge"></div></div>
              <div class="result-item"><div class="result-label">DDD</div><div class="result-value" id="cep-res-ddd"></div></div>
            </div>
          </div>

          <div class="legal-notice">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Correios via ViaCEP.</strong><br>
              Base de dados nacional de Códigos de Endereçamento Postal. Dados públicos mantidos pelos Correios.
            </div>
          </div>
        </div>
      </div>


      <!-- ==========================================
           ABA 3: CNPJ (Empresas)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-cnpj">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-building"></i> Consulta Cadastral (CNPJ)</h2>
          
          <div class="error-msg" id="cnpj-error"></div>

          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label">CNPJ (Apenas números)</label>
              <input type="text" id="cnpj-input" class="consulta-input" placeholder="Ex: 00000000000191" maxlength="18" onkeypress="handleEnter(event, consultarCnpj)">
            </div>
            <button class="consulta-btn" id="cnpj-btn" onclick="consultarCnpj()">
              <span class="btn-text">Consultar</span>
              <div class="loader"></div>
            </button>
          </div>

          <div class="consulta-result" id="cnpj-result">
            <div class="result-grid">
              <div class="result-item" style="grid-column: 1 / -1;">
                <div class="result-label">Razão Social</div>
                <div class="result-value highlight" id="cnpj-res-razao"></div>
              </div>
              <div class="result-item" style="grid-column: 1 / -1;">
                <div class="result-label">Nome Fantasia</div>
                <div class="result-value" id="cnpj-res-fantasia"></div>
              </div>
              <div class="result-item"><div class="result-label">Situação Cadastral</div><div class="result-value" id="cnpj-res-situacao"></div></div>
              <div class="result-item"><div class="result-label">Data de Abertura</div><div class="result-value" id="cnpj-res-abertura"></div></div>
              <div class="result-item"><div class="result-label">CNAE Principal</div><div class="result-value" id="cnpj-res-cnae"></div></div>
              <div class="result-item"><div class="result-label">Natureza Jurídica</div><div class="result-value" id="cnpj-res-natureza"></div></div>
              <div class="result-item" style="grid-column: 1 / -1;">
                <div class="result-label">Endereço</div>
                <div class="result-value" id="cnpj-res-endereco"></div>
              </div>
              <div class="result-item"><div class="result-label">Telefone</div><div class="result-value" id="cnpj-res-telefone"></div></div>
              <div class="result-item" style="grid-column: span 2;"><div class="result-label">Quadro Societário</div><div class="result-value" id="cnpj-res-qsa" style="font-size: 0.95rem; line-height: 1.5;"></div></div>
            </div>
          </div>

          <div class="legal-notice">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Receita Federal do Brasil via BrasilAPI.</strong><br>
              As informações de CNPJ são dados públicos (Art. 5º da Lei nº 12.527/2011 — Lei de Acesso à Informação) disponibilizados para transparência ativa da administração pública.
            </div>
          </div>
        </div>
      </div>


      <!-- ==========================================
           ABA 4: VALIDADOR DE CPF / CNPJ
      =============================================== -->
      <div class="consulta-tab-content" id="tab-validacao">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-id-card"></i> Validador de Documentos (Offline)</h2>
          
          <div class="error-msg" id="val-error"></div>

          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label">CPF ou CNPJ (Apenas números)</label>
              <input type="text" id="val-input" class="consulta-input" placeholder="Ex: 12345678900" maxlength="18" onkeypress="handleEnter(event, validarDoc)">
            </div>
            <button class="consulta-btn" id="val-btn" onclick="validarDoc()">
              <span class="btn-text">Verificar Dígito</span>
              <div class="loader"></div>
            </button>
          </div>

          <div class="consulta-result" id="val-result">
            <div class="result-grid">
              <div class="result-item" style="text-align: center;">
                <div class="result-label">Documento Informado</div>
                <div class="result-value" id="val-res-doc" style="font-family: monospace; font-size: 1.25rem; margin-top: 0.5rem;"></div>
              </div>
              <div class="result-item" style="text-align: center; border-width: 2px;" id="val-res-box">
                <div class="result-label">Status Matemático</div>
                <div class="result-value" id="val-res-status" style="font-size: 1.5rem; margin-top: 0.5rem; font-weight: 700;"></div>
              </div>
            </div>
          </div>

          <div class="legal-notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-shield-halved"></i>
            <div>
              <strong>Validação Segura / LGPD Compliant</strong><br>
              Esta ferramenta apenas calcula os dígitos verificadores (matemática do documento) para confirmar se um CPF ou CNPJ foi digitado corretamente. <strong>Não é feita nenhuma consulta em base de dados</strong> e nenhum dado pessoal é retornado ou registrado, respeitando integralmente a LGPD.
            </div>
          </div>
        </div>
      </div>




      <!-- ==========================================
           ABA 6: FERIADOS
      =============================================== -->
      <div class="consulta-tab-content" id="tab-feriados">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-regular fa-calendar-days"></i> Feriados Nacionais</h2>
          
          <div class="error-msg" id="fere-error"></div>

          <div class="consulta-form">
            <div class="form-group-flex" style="max-width: 300px;">
              <label class="result-label">Selecione o Ano</label>
              <div style="display:flex;gap:.5rem;">
                <button class="consulta-btn" style="flex:1;background:var(--bg);color:var(--text);border:1px solid var(--border);" onclick="consultarFeriados(<?= date('Y')-1 ?>)"><?= date('Y')-1 ?></button>
                <button class="consulta-btn" style="flex:1;" onclick="consultarFeriados(<?= date('Y') ?>)"><?= date('Y') ?></button>
                <button class="consulta-btn" style="flex:1;background:var(--bg);color:var(--text);border:1px solid var(--border);" onclick="consultarFeriados(<?= date('Y')+1 ?>)"><?= date('Y')+1 ?></button>
              </div>
            </div>
          </div>

          <div class="consulta-result" id="fere-result">
            <h3 style="font-size:1.1rem;margin:0 0 1rem 0;color:var(--primary);">Calendário Oficial <span id="fere-ano-lbl"></span></h3>
            <div class="result-grid" id="fere-grid">
              <!-- gerado por via JS -->
            </div>
          </div>

          <div class="legal-notice">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Anbima / Diário Oficial da União.</strong><br>
              Lista atualizada de Feriados Nacionais para planejamento administrativo, escalas e compensação de horas.
            </div>
          </div>
        </div>
      </div>


      <!-- ==========================================
           ABA 7: MUNICÍPIO (IBGE)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-ibge">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-city"></i> Dados do Município — Eldorado dos Ventos</h2>
          
          <div class="error-msg" id="ibge-error"></div>

          <button class="consulta-btn" id="ibge-btn" onclick="consultarIbge()" style="margin-bottom: 2rem;">
            <i class="fa-solid fa-arrows-rotate"></i> <span class="btn-text">Carregar Dados Recentes do IBGE</span>
            <div class="loader"></div>
          </button>

          <div class="consulta-result" id="ibge-result">
            <div class="result-grid">
              <div class="result-item" style="grid-column: 1 / -1; border-color: var(--primary);">
                <div class="result-label" style="color: var(--primary);">Município</div>
                <div class="result-value highlight">Eldorado dos Ventos — Estado de São Paulo</div>
              </div>
              <div class="result-item"><div class="result-label">Mesorregião</div><div class="result-value" id="ibge-meso"></div></div>
              <div class="result-item"><div class="result-label">Microrregião</div><div class="result-value" id="ibge-micro"></div></div>
              <div class="result-item"><div class="result-label">Região Imediata</div><div class="result-value" id="ibge-imediata"></div></div>
              <div class="result-item"><div class="result-label">Região Intermediária</div><div class="result-value" id="ibge-inter"></div></div>
              
              <div class="result-item" style="grid-column: 1 / -1; margin-top: 1rem;">
                <hr style="border:0;border-top:1px dashed var(--border);margin-bottom:1.5rem;">
                <div class="result-label">População (Censo 2022)</div>
                <div class="result-value highlight" id="ibge-populacao" style="font-size:2rem;"></div>
              </div>
            </div>
          </div>

          <div class="legal-notice">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Instituto Brasileiro de Geografia e Estatística (IBGE).</strong><br>
              Dados geográficos e censitários abertos sob domínio público no portal IBGE Sidra e API de Localidades.
            </div>
          </div>
        </div>
      </div>


      <!-- ==========================================
           ABA 8: CELULAR ROUBADO (IMEI)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-imei">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-mobile-screen"></i> Consulta de Celular Impedido/Roubado (IMEI)</h2>
          
          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label" style="font-size: 1rem; color: var(--text);">Para consultar, você precisará do IMEI do aparelho (digite *#06# no celular).</label>
              <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                O Projeto Celular Legal da Anatel permite verificar se um aparelho possui registro de roubo, furto ou extravio. Devido a exigências de <strong>Captcha de Segurança</strong> do governo, essa consulta deve ser realizada diretamente no portal oficial do <strong>Cadastro Nacional de Estações Móveis Impedidas (CEMI)</strong>.
              </p>
            </div>
          </div>

          <a href="https://consultaaparelhoimpedido.com.br/public-web/welcome" target="_blank" style="display:inline-flex;align-items:center;gap:.75rem;background:var(--primary);color:#fff;padding:1rem 2rem;border-radius:12px;text-decoration:none;font-weight:600;font-size:1.1rem;box-shadow: 0 4px 12px rgba(var(--primary-rgb),0.3); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar Portal Oficial (Anatel)
          </a>

          <div class="legal-notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Anatel / ABR Telecom.</strong><br>
              A base de dados do CEMI (Cadastro Nacional de Estações Móveis Impedidas) unifica as informações sobre bloqueios e restrições de aparelhos celulares no Brasil.
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================
           ABA 9: MANDADOS DE PRISÃO (BNMP)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-bnmp">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-handcuffs"></i> Banco Nacional de Mandados de Prisão (BNMP)</h2>
          
          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label" style="font-size: 1rem; color: var(--text);">Pesquisa de Mandados Expedidos</label>
              <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                O portal público do BNMP permite verificar se há expedientes de prisão em aberto contra uma determinada pessoa. A pesquisa oficial requer validação por Captcha antirobôs.
              </p>
            </div>
          </div>

          <a href="https://portalbnmp.cnj.jus.br" target="_blank" style="display:inline-flex;align-items:center;gap:.75rem;background:var(--primary);color:#fff;padding:1rem 2rem;border-radius:12px;text-decoration:none;font-weight:600;font-size:1.1rem;box-shadow: 0 4px 12px rgba(var(--primary-rgb),0.3); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar Certidão / Consulta BNMP
          </a>

          <div class="legal-notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Conselho Nacional de Justiça (CNJ).</strong><br>
              As informações referem-se aos mandados registrados pelos tribunais conforme Resolução nº 251/2018 do CNJ.
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================
           ABA 10: VEÍCULOS ROUBADOS (SINESP)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-sinesp">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-car-on"></i> Veículos Roubados ou Furtados (SINESP)</h2>
          
          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label" style="font-size: 1rem; color: var(--text);">Consulta Pública Base Nacional SINESP</label>
              <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                Verifique o status de furto/roubo de veículos utilizando a placa. O portal é mantido pelo Ministério da Justiça.
              </p>
            </div>
          </div>

          <a href="https://portalservicos.senatran.serpro.gov.br/#/veiculos/consultar-roubo-furto" target="_blank" style="display:inline-flex;align-items:center;gap:.75rem;background:var(--primary);color:#fff;padding:1rem 2rem;border-radius:12px;text-decoration:none;font-weight:600;font-size:1.1rem;box-shadow: 0 4px 12px rgba(var(--primary-rgb),0.3); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar Portal Senatran (Gov.br)
          </a>

          <div class="legal-notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Ministério dos Transportes / SENATRAN.</strong><br>
              A busca oficial e gratuita de informações restritivas sobre veículos na web passou a ser unificada pelo Portal de Serviços da Senatran via Gov.br.
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================
           ABA 11: PESSOAS DESAPARECIDAS (CNPD)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-cnpd">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-person-circle-question"></i> Cadastro Nacional de Pessoas Desaparecidas (CNPD)</h2>
          
          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label" style="font-size: 1rem; color: var(--text);">Painel Público de Desaparecimentos</label>
              <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                Consulta pública aos registros de pessoas declaradas desaparecidas no Brasil, contendo foto e informações básicas. O banco é atualizado através dos Boletins de Ocorrência Policial.
              </p>
            </div>
          </div>

          <a href="https://cnpd.mj.gov.br/painel-publico" target="_blank" style="display:inline-flex;align-items:center;gap:.75rem;background:var(--primary);color:#fff;padding:1rem 2rem;border-radius:12px;text-decoration:none;font-weight:600;font-size:1.1rem;box-shadow: 0 4px 12px rgba(var(--primary-rgb),0.3); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Consultar Painel CNPD (Gov.br)
          </a>

          <div class="legal-notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Ministério da Justiça e Segurança Pública (MJSP).</strong><br>
              Instituído pela Lei nº 13.812/2019, o CNPD integra o banco de informações nacional focado na busca e localização de desaparecidos.
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================
           ABA 12: SITUAÇÃO CADASTRAL CPF (RECEITA)
      =============================================== -->
      <div class="consulta-tab-content" id="tab-receita">
        <div class="consulta-card">
          <h2 class="consulta-card-title"><i class="fa-solid fa-file-invoice-dollar"></i> Comprovante de Situação Cadastral no CPF</h2>
          
          <div class="consulta-form">
            <div class="form-group-flex">
              <label class="result-label" style="font-size: 1rem; color: var(--text);">Receita Federal do Brasil</label>
              <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                Consulta a situação cadastral do contribuinte (Regular, Suspensa, Cancelada ou Nula). Requer data de nascimento e validação de reCAPTCHA em site oficial da Secretaria da RFB.
              </p>
            </div>
          </div>

          <a href="https://servicos.receita.fazenda.gov.br/Servicos/CPF/ConsultaSituacao/ConsultaPublica.asp" target="_blank" style="display:inline-flex;align-items:center;gap:.75rem;background:var(--primary);color:#fff;padding:1rem 2rem;border-radius:12px;text-decoration:none;font-weight:600;font-size:1.1rem;box-shadow: 0 4px 12px rgba(var(--primary-rgb),0.3); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar Consulta na RFB
          </a>

          <div class="legal-notice" style="margin-top: 2.5rem;">
            <i class="fa-solid fa-circle-info"></i>
            <div>
              <strong>Fonte: Ministério da Fazenda / Receita Federal.</strong><br>
              Acesso exclusivo para emissão do comprovante de situação cadastral, conforme Instrução Normativa RFB vigente.
            </div>
          </div>
        </div>
      </div>

    </div><!-- /tabs -->

  </div><!-- /grid -->
</div>

<!-- ============================================================ -->
<!-- JS LOGIC - FETCH DADOS                                       -->
<!-- ============================================================ -->
<script>
// ---------------------------------------------------------
// UI Navigation
// ---------------------------------------------------------
function switchTab(tabId) {
  document.querySelectorAll('.consulta-menu-item').forEach(e => e.classList.remove('active'));
  document.querySelectorAll('.consulta-tab-content').forEach(e => e.classList.remove('active'));
  
  event.currentTarget.classList.add('active');
  document.getElementById('tab-' + tabId).classList.add('active');
}

function handleEnter(e, callback) {
  if (e.key === 'Enter') {
    e.preventDefault();
    callback();
  }
}

function startLoading(btnId, errorId) {
  const btn = document.getElementById(btnId);
  if (btn) {
    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.loader').style.display = 'block';
  }
  const err = document.getElementById(errorId);
  if (err) err.style.display = 'none';
}

function stopLoading(btnId) {
  const btn = document.getElementById(btnId);
  if (btn) {
    btn.disabled = false;
    btn.querySelector('.btn-text').style.display = 'block';
    btn.querySelector('.loader').style.display = 'none';
  }
}

function showError(errorId, msg) {
  const err = document.getElementById(errorId);
  if (err) {
    err.textContent = msg;
    err.style.display = 'block';
  }
}

function v(id, text) {
  const e = document.getElementById(id);
  if (e) e.textContent = text || '---';
}

function showResult(id) {
  document.getElementById(id).classList.add('active');
}

// ---------------------------------------------------------
// 1. FIPE
// ---------------------------------------------------------
async function loadFipeMarcas() {
  const tipo = document.getElementById('fipe-tipo').value;
  const selMarca = document.getElementById('fipe-marca');
  selMarca.innerHTML = '<option value="">Carregando...</option>';
  selMarca.disabled = true;
  document.getElementById('fipe-modelo').innerHTML = '<option value="">Selecione a marca primeiro...</option>';
  document.getElementById('fipe-modelo').disabled = true;
  document.getElementById('fipe-ano').innerHTML = '<option value="">Selecione o modelo primeiro...</option>';
  document.getElementById('fipe-ano').disabled = true;
  document.getElementById('fipe-btn').disabled = true;
  
  try {
    const res = await fetch(`<?= BASE_URL ?>/consultas/fipe/marcas?tipo=${tipo}`).then(r => r.json());
    if (res.error) throw new Error(res.error);
    selMarca.innerHTML = '<option value="">Selecione a marca...</option>';
    res.forEach(m => selMarca.innerHTML += `<option value="${m.codigo}">${m.nome}</option>`);
    selMarca.disabled = false;
  } catch(e) {
    selMarca.innerHTML = '<option value="">Erro ao carregar</option>';
  }
}

async function loadFipeModelos() {
  const tipo = document.getElementById('fipe-tipo').value;
  const marca = document.getElementById('fipe-marca').value;
  const selMod = document.getElementById('fipe-modelo');
  
  if (!marca) {
    selMod.innerHTML = '<option value="">Selecione a marca primeiro...</option>';
    selMod.disabled = true;
    return;
  }
  
  selMod.innerHTML = '<option value="">Carregando...</option>';
  selMod.disabled = true;
  document.getElementById('fipe-ano').innerHTML = '<option value="">Selecione o modelo primeiro...</option>';
  document.getElementById('fipe-ano').disabled = true;
  document.getElementById('fipe-btn').disabled = true;

  try {
    const res = await fetch(`<?= BASE_URL ?>/consultas/fipe/modelos?tipo=${tipo}&marca=${marca}`).then(r => r.json());
    if (res.error) throw new Error(res.error);
    selMod.innerHTML = '<option value="">Selecione o modelo...</option>';
    res.forEach(m => selMod.innerHTML += `<option value="${m.codigo}">${m.nome}</option>`);
    selMod.disabled = false;
  } catch(e) {
    selMod.innerHTML = '<option value="">Erro ao carregar</option>';
  }
}

async function loadFipeAnos() {
  const tipo = document.getElementById('fipe-tipo').value;
  const marca = document.getElementById('fipe-marca').value;
  const modelo = document.getElementById('fipe-modelo').value;
  const selAno = document.getElementById('fipe-ano');
  
  if (!modelo) {
    selAno.innerHTML = '<option value="">Selecione o modelo primeiro...</option>';
    selAno.disabled = true;
    return;
  }
  
  selAno.innerHTML = '<option value="">Carregando...</option>';
  selAno.disabled = true;
  document.getElementById('fipe-btn').disabled = true;

  try {
    const res = await fetch(`<?= BASE_URL ?>/consultas/fipe/anos?tipo=${tipo}&marca=${marca}&modelo=${modelo}`).then(r => r.json());
    if (res.error) throw new Error(res.error);
    selAno.innerHTML = '<option value="">Selecione o ano...</option>';
    res.forEach(m => selAno.innerHTML += `<option value="${m.codigo}">${m.nome}</option>`);
    selAno.disabled = false;
    
    // Auto enable btn if ano is selected (needs an event listener or just let user click)
    selAno.onchange = () => { document.getElementById('fipe-btn').disabled = !selAno.value; };
  } catch(e) {
    selAno.innerHTML = '<option value="">Erro ao carregar</option>';
  }
}

async function consultarFipe() {
  const tipo = document.getElementById('fipe-tipo').value;
  const marca = document.getElementById('fipe-marca').value;
  const modelo = document.getElementById('fipe-modelo').value;
  const ano = document.getElementById('fipe-ano').value;
  
  if (!ano) return;
  startLoading('fipe-btn', 'fipe-error');

  try {
    const data = await fetch(`<?= BASE_URL ?>/consultas/fipe/valor?tipo=${tipo}&marca=${marca}&modelo=${modelo}&ano=${ano}`).then(r => r.json());
    if (data.error) throw new Error(data.error);
    
    v('fipe-valor', data.Valor);
    v('fipe-res-marca', data.Marca);
    v('fipe-res-modelo', data.Modelo);
    v('fipe-res-ano', data.AnoModelo);
    v('fipe-res-comb', data.Combustivel);
    v('fipe-res-cod', data.CodigoFipe);
    v('fipe-res-mes', data.MesReferencia);
    
    showResult('fipe-result');
  } catch(e) {
    showError('fipe-error', 'Erro ao consultar tabela FIPE.');
  } finally {
    stopLoading('fipe-btn');
  }
}

// ---------------------------------------------------------
// 2. CEP
// ---------------------------------------------------------
async function consultarCep() {
  const cep = document.getElementById('cep-input').value.replace(/\D/g, '');
  if (cep.length !== 8) return showError('cep-error', 'Digite um CEP válido com 8 números');
  
  startLoading('cep-btn', 'cep-error');
  try {
    const data = await fetch(`<?= BASE_URL ?>/consultas/cep?cep=${cep}`).then(r => r.json());
    if (data.error) throw new Error(data.error);
    
    v('cep-res-rua', data.logradouro);
    v('cep-res-bairro', data.bairro);
    v('cep-res-cidade', `${data.localidade} / ${data.uf}`);
    v('cep-res-cep', data.cep);
    v('cep-res-ibge', data.ibge);
    v('cep-res-ddd', data.ddd);
    
    showResult('cep-result');
  } catch (err) {
    showError('cep-error', err.message);
  } finally {
    stopLoading('cep-btn');
  }
}

// ---------------------------------------------------------
// 3. CNPJ
// ---------------------------------------------------------
async function consultarCnpj() {
  const cnpj = document.getElementById('cnpj-input').value.replace(/\D/g, '');
  if (cnpj.length !== 14) return showError('cnpj-error', 'Digite um CNPJ válido com 14 números');
  
  startLoading('cnpj-btn', 'cnpj-error');
  try {
    const data = await fetch(`<?= BASE_URL ?>/consultas/cnpj?cnpj=${cnpj}`).then(r => r.json());
    if (data.error) throw new Error(data.error);
    
    v('cnpj-res-razao', data.razao_social);
    v('cnpj-res-fantasia', data.nome_fantasia || 'Não informado');
    v('cnpj-res-situacao', data.descricao_situacao_cadastral);
    v('cnpj-res-abertura', data.data_inicio_atividade);
    v('cnpj-res-cnae', `${data.cnae_fiscal} - ${data.cnae_fiscal_descricao}`);
    v('cnpj-res-natureza', data.natureza_juridica);
    v('cnpj-res-telefone', data.ddd_telefone_1);
    
    const end = `${data.logradouro}, ${data.numero} ${data.complemento||''} - ${data.bairro}, ${data.municipio}/${data.uf} CEP: ${data.cep}`;
    v('cnpj-res-endereco', end);
    
    let qsaStr = '';
    if (data.qsa && data.qsa.length > 0) {
      qsaStr = data.qsa.map(s => `• ${s.nome_socio} (${s.qualificacao_socio})`).join('<br>');
    } else {
      qsaStr = 'Nenhum sócio registrado.';
    }
    document.getElementById('cnpj-res-qsa').innerHTML = qsaStr;
    
    showResult('cnpj-result');
  } catch (err) {
    showError('cnpj-error', err.message);
  } finally {
    stopLoading('cnpj-btn');
  }
}

// ---------------------------------------------------------
// 4. VALIDADOR CPF/CNPJ
// ---------------------------------------------------------
async function validarDoc() {
  const doc = document.getElementById('val-input').value.replace(/\D/g, '');
  if (doc.length !== 11 && doc.length !== 14) return showError('val-error', 'Digite 11 números para CPF ou 14 para CNPJ');
  
  startLoading('val-btn', 'val-error');
  try {
    const data = await fetch(`<?= BASE_URL ?>/consultas/validar?doc=${doc}`).then(r => r.json());
    if (data.error) throw new Error(data.error);
    
    v('val-res-doc', `${data.tipo}: ${data.formatado}`);
    
    const box = document.getElementById('val-res-box');
    const lbl = document.getElementById('val-res-status');
    
    if (data.valido) {
      box.style.borderColor = 'var(--success)';
      box.style.backgroundColor = 'rgba(var(--success-rgb), 0.1)';
      lbl.style.color = 'var(--success)';
      lbl.innerHTML = '<i class="fa-solid fa-check-circle"></i> VÁLIDO';
    } else {
      box.style.borderColor = 'var(--danger)';
      box.style.backgroundColor = 'rgba(var(--danger-rgb), 0.1)';
      lbl.style.color = 'var(--danger)';
      lbl.innerHTML = '<i class="fa-solid fa-times-circle"></i> INVÁLIDO';
    }
    
    showResult('val-result');
  } catch (err) {
    showError('val-error', err.message);
  } finally {
    stopLoading('val-btn');
  }
}

// ---------------------------------------------------------
// 6. FERIADOS
// ---------------------------------------------------------
async function consultarFeriados(ano) {
  v('fere-ano-lbl', ano);
  document.getElementById('fere-grid').innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2rem;"><div class="loader" style="display:inline-block;border-top-color:var(--primary);"></div></div>';
  showResult('fere-result');
  
  try {
    const data = await fetch(`<?= BASE_URL ?>/consultas/feriados?ano=${ano}`).then(r => r.json());
    if (data.error) throw new Error(data.error);
    
    let html = '';
    data.forEach(f => {
      const parts = f.date.split('-');
      const str = `${parts[2]}/${parts[1]}/${parts[0]}`;
      html += `
        <div class="result-item" style="display:flex;align-items:center;gap:1rem;">
          <div style="background:var(--bg);color:var(--text);border:1px solid var(--border);padding:.5rem;border-radius:8px;font-weight:700;font-size:1.1rem;min-width:65px;text-align:center;">${str.substring(0,5)}</div>
          <div>
            <div class="result-value" style="font-size:1rem;">${f.name}</div>
            <div class="result-label" style="margin:0;">Feriado Nacional</div>
          </div>
        </div>
      `;
    });
    document.getElementById('fere-grid').innerHTML = html;
    
  } catch (err) {
    showError('fere-error', err.message);
    document.getElementById('fere-grid').innerHTML = '';
  }
}

// ---------------------------------------------------------
// 7. IBGE
// ---------------------------------------------------------
async function consultarIbge() {
  startLoading('ibge-btn', 'ibge-error');
  try {
    const data = await fetch(`<?= BASE_URL ?>/consultas/ibge`).then(r => r.json());
    
    if (data.municipio && data.municipio.microrregiao) {
      v('ibge-micro', data.municipio.microrregiao.nome);
      v('ibge-meso', data.municipio.microrregiao.mesorregiao.nome);
      v('ibge-imediata', data.municipio['regiao-imediata'].nome);
      v('ibge-inter', data.municipio['regiao-imediata']['regiao-intermediaria'].nome);
    }
    
    if (data.indicadores && data.indicadores.length > 0) {
      // Find populacao
      const popData = data.indicadores[0].resultados[0].series[0].serie['2022'];
      if (popData) {
        v('ibge-populacao', parseInt(popData).toLocaleString('pt-BR') + ' habitantes');
      }
    }
    
    showResult('ibge-result');
  } catch (err) {
    showError('ibge-error', 'Erro ao acessar dados do IBGE.');
  } finally {
    stopLoading('ibge-btn');
  }
}


// Init
document.addEventListener('DOMContentLoaded', () => {
  loadFipeMarcas();
  // Call once silently on mount
  consultarFeriados(new Date().getFullYear());
  consultarIbge();
});
</script>
