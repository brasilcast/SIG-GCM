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
 */
// =============================================
// SIG-GCM — JavaScript Principal
// =============================================

// Sidebar mobile
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebarOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

// Sub-navegação accordion
function toggleNav(navId, btn) {
  const nav = document.getElementById(navId);
  if (!nav) return;
  const isOpen = nav.classList.contains('open');
  // Fechar todos
  document.querySelectorAll('.nav-sub').forEach(el => el.classList.remove('open'));
  document.querySelectorAll('.nav-group-toggle').forEach(el => el.classList.remove('open'));
  // Abrir o clicado
  if (!isOpen) {
    nav.classList.add('open');
    btn.classList.add('open');
  }
}

// ---- Dark / Light Mode ----
function toggleTheme() {
  const isDark = document.documentElement.classList.toggle('dark-mode');
  document.documentElement.classList.toggle('light-mode', !isDark);
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  const icon = document.getElementById('themeIcon');
  if (icon) icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
}

// Auto-abrir seção ativa no carregamento
document.addEventListener('DOMContentLoaded', function () {
  // Aplicar tema salvo — padrão é CLARO
  let saved = localStorage.getItem('theme');
  if (!saved) {
    saved = 'light';
    localStorage.setItem('theme', saved);
  }
  const isDark = saved === 'dark';
  document.documentElement.classList.toggle('dark-mode', isDark);
  document.documentElement.classList.toggle('light-mode', !isDark);
  const icon = document.getElementById('themeIcon');
  if (icon) icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';

  const activeLink = document.querySelector('.nav-sub .nav-item.active');
  if (activeLink) {
    const navSub = activeLink.closest('.nav-sub');
    if (navSub) {
      navSub.classList.add('open');
      const toggle = navSub.previousElementSibling;
      if (toggle && toggle.classList.contains('nav-group-toggle')) {
        toggle.classList.add('open');
      }
    }
  }

  // Auto-dismiss toast após 5s
  const toast = document.getElementById('mainToast');
  if (toast) setTimeout(() => dismissToast('mainToast'), 5000);
});

// Dismiss toast
function dismissToast(id) {
  const toast = document.getElementById(id);
  if (!toast) return;
  toast.classList.add('hiding');
  setTimeout(() => toast.remove(), 300);
}

// Mostrar toast via JS
function showToast(message, type = 'success') {
  const container = document.querySelector('.toast-container') || createToastContainer();
  const id = 'toast-' + Date.now();
  const icons = { success: 'circle-check', error: 'circle-xmark', warning: 'triangle-exclamation', info: 'circle-info' };
  const html = `
    <div class="toast toast-${type}" id="${id}">
      <span class="toast-icon"><i class="fa-solid fa-${icons[type] || 'circle-info'}"></i></span>
      <div class="toast-body">${message}</div>
      <button class="toast-close" onclick="dismissToast('${id}')"><i class="fa-solid fa-xmark"></i></button>
    </div>`;
  container.insertAdjacentHTML('beforeend', html);
  setTimeout(() => dismissToast(id), 5000);
}

function createToastContainer() {
  const div = document.createElement('div');
  div.className = 'toast-container';
  div.id = 'toastContainer';
  document.body.appendChild(div);
  return div;
}

// Confirmar ações destrutivas
function confirmar(msg, formId) {
  if (confirm(msg)) {
    document.getElementById(formId).submit();
  }
  return false;
}

// Formatar data BR
function formatarData(isoStr) {
  if (!isoStr) return '—';
  const [d, t] = isoStr.split('T');
  const [y, m, dy] = d.split('-');
  return `${dy}/${m}/${y}` + (t ? ' ' + t.substring(0, 5) : '');
}

// Capturar geolocalização
function capturarGPS(latId, lngId, btnId) {
  if (!navigator.geolocation) {
    showToast('GPS não suportado neste dispositivo.', 'warning');
    return;
  }
  const btn = document.getElementById(btnId);
  if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Capturando...';
  navigator.geolocation.getCurrentPosition(
    pos => {
      document.getElementById(latId).value = pos.coords.latitude.toFixed(8);
      document.getElementById(lngId).value = pos.coords.longitude.toFixed(8);
      if (btn) btn.innerHTML = '<i class="fa-solid fa-check"></i> GPS Capturado';
      showToast('Localização capturada com sucesso!', 'success');
    },
    err => {
      if (btn) btn.innerHTML = '<i class="fa-solid fa-location-dot"></i> Capturar GPS';
      showToast('Erro ao capturar GPS: ' + err.message, 'error');
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

// Pré-visualizar imagem antes do upload
// Aceita element ou ID string para ambos os parâmetros
function previewImagem(inputArg, previewArg) {
  const input   = (typeof inputArg   === 'string') ? document.getElementById(inputArg)   : inputArg;
  const preview = (typeof previewArg === 'string') ? document.getElementById(previewArg) : previewArg;
  if (!input || !preview) return;
  // Se chamado inline (onchange="previewImagem(this,'id')"), o arquivo já existe
  const doPreview = (file) => {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(file);
  };
  if (input.files && input.files[0]) {
    doPreview(input.files[0]);
  } else {
    input.addEventListener('change', function () { doPreview(this.files && this.files[0]); });
  }
}
