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
 */// =============================================
// SIG-GCM — Entry Point (index.php)
// =============================================

define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/core/Database.php';

// =============================================
// MODO MANUTENÇÃO — verifica antes de tudo
// =============================================
$_manutFile = ROOT_PATH . '/config/manutencao.php';
if (file_exists($_manutFile)) {
    $_manut = include $_manutFile;
    if (is_array($_manut) && !empty($_manut['ativa'])) {
        // Permite acesso DEV e ao próprio painel /dev/
        $_reqUri = $_SERVER['REQUEST_URI'] ?? '';
        $isDev = strpos($_reqUri, '/dev') === 0;
        if (!$isDev) {
            $msg = $_manut['mensagem'] ?? 'Sistema em manutenção. Voltaremos em breve.';
            $fim = $_manut['fim'] ?? '';
            http_response_code(503);
            echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
            echo '<title>Manutenção — SIG-GCM</title>';
            echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
            echo '<style>body{font-family:system-ui,sans-serif;background:#0d1117;color:#e6edf3;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:1rem;text-align:center}.card{max-width:480px;background:#161b22;border:1px solid #30363d;border-radius:12px;padding:3rem 2rem;box-shadow:0 16px 40px rgba(0,0,0,.5)}.icon{font-size:3.5rem;color:#d29922;margin-bottom:1rem}h1{font-size:1.5rem;margin-bottom:.75rem}.msg{color:#8b949e;font-size:.95rem;line-height:1.6;margin-bottom:1.5rem}.fim{font-size:.82rem;color:#58a6ff}</style>';
            echo '</head><body><div class="card">';
            echo '<div class="icon"><i class="fa-solid fa-wrench"></i></div>';
            echo '<h1>Sistema em Manutenção</h1>';
            echo '<div class="msg">' . htmlspecialchars($msg) . '</div>';
            if ($fim) echo '<div class="fim"><i class="fa-solid fa-clock"></i> Previsão de retorno: ' . htmlspecialchars($fim) . '</div>';
            echo '</div></body></html>';
            exit;
        }
    }
}
// =============================================

// =============================================
// COLETA DE ERROS HTTP — regista após dispatch
// =============================================

require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/AuditLogger.php';

// Carregar todos os controllers
require_once ROOT_PATH . '/controllers/AuthController.php';
require_once ROOT_PATH . '/controllers/DashboardController.php';
require_once ROOT_PATH . '/controllers/UsuariosController.php';
require_once ROOT_PATH . '/controllers/TaloesController.php';
require_once ROOT_PATH . '/controllers/FrotaController.php';
require_once ROOT_PATH . '/controllers/EscalasController.php';
require_once ROOT_PATH . '/controllers/MariaPenhaController.php';
require_once ROOT_PATH . '/controllers/RondaEscolarController.php';
require_once ROOT_PATH . '/controllers/CorregedoriaController.php';
require_once ROOT_PATH . '/controllers/AuditoriaController.php';
require_once ROOT_PATH . '/controllers/ComunicadosController.php';
require_once ROOT_PATH . '/controllers/EscolasController.php';
require_once ROOT_PATH . '/controllers/ClimaController.php';
require_once ROOT_PATH . '/controllers/ConsultasController.php';


Auth::init();

$router = new Router();

// =============================================
// ROTAS PÚBLICAS
// =============================================
$router->get('',              'AuthController', 'index');
$router->get('login',         'AuthController', 'index');
$router->post('login',        'AuthController', 'login');
$router->get('logout',        'AuthController', 'logout');
$router->get('alterar-senha', 'AuthController', 'alterarSenha');
$router->post('alterar-senha','AuthController', 'salvarSenha');
$router->get('setup-taloes',  'AuthController', 'setupTaloes');

// =============================================
// ROTA — DASHBOARD
// =============================================
$router->get('dashboard', 'DashboardController', 'index');

// =============================================
// Usuários (Requer usuarios.criar para CRUD, usuarios.alterar_permissoes para permissões)
// =============================================
$router->get('usuarios',                    'UsuariosController', 'index');
$router->get('usuarios/novo',               'UsuariosController', 'novo');
$router->post('usuarios/novo',              'UsuariosController', 'salvar');
$router->get('usuarios/{id}',               'UsuariosController', 'ver');
$router->get('usuarios/{id}/editar',        'UsuariosController', 'editar');
$router->post('usuarios/{id}/editar',       'UsuariosController', 'atualizar');
$router->post('usuarios/{id}/toggle',       'UsuariosController', 'toggle');
$router->post('usuarios/{id}/gerar-senha',  'UsuariosController', 'gerarSenhaTemporaria');
$router->get('usuarios/{id}/permissoes',    'UsuariosController', 'permissoes');
$router->post('usuarios/{id}/permissoes',   'UsuariosController', 'salvarPermissoes');

// =============================================
// ROTAS — TALÕES
// =============================================
$router->get('taloes',                      'TaloesController', 'index');
$router->get('taloes/mapa-calor',           'TaloesController', 'mapaCalor'); // ANTES de {id}
$router->get('taloes/novo',                 'TaloesController', 'novo');
$router->post('taloes/novo',                'TaloesController', 'salvar');
$router->get('taloes/{id}',                 'TaloesController', 'ver');
$router->get('taloes/{id}/editar',          'TaloesController', 'editar');
$router->post('taloes/{id}/editar',         'TaloesController', 'atualizar');
$router->post('taloes/{id}/status',         'TaloesController', 'atualizarStatus');
$router->post('taloes/{id}/update',         'TaloesController', 'addAtualizacao');
$router->get('taloes/{id}/pdf',             'TaloesController', 'pdf');

// =============================================
// ROTAS — FROTA
// =============================================
$router->get('frota/viaturas',              'FrotaController', 'viaturas');
$router->get('frota/viaturas/nova',         'FrotaController', 'novaViatura');
$router->post('frota/viaturas/nova',        'FrotaController', 'salvarViatura');
$router->get('frota/viaturas/{id}',         'FrotaController', 'verViatura');
$router->get('frota/viaturas/{id}/editar',  'FrotaController', 'editarViatura');
$router->post('frota/viaturas/{id}/editar', 'FrotaController', 'atualizarViatura');
$router->post('frota/viaturas/{id}/excluir', 'FrotaController', 'excluirViatura');
$router->get('frota/checklists',            'FrotaController', 'checklists');
$router->get('frota/checklists/novo',       'FrotaController', 'novoChecklist');
$router->post('frota/checklists/novo',      'FrotaController', 'salvarChecklist');
$router->get('frota/abastecimentos',        'FrotaController', 'abastecimentos');
$router->get('frota/abastecimentos/novo',   'FrotaController', 'novoAbastecimento');
$router->post('frota/abastecimentos/novo',  'FrotaController', 'salvarAbastecimento');

// =============================================
// ROTAS — ESCALAS / BANCO DE HORAS / TROCAS
// =============================================
$router->get('escalas',                     'EscalasController', 'index');
$router->get('escalas/pdf',                 'EscalasController', 'pdf');
$router->post('escalas',                    'EscalasController', 'salvar');
$router->post('escalas/{id}/editar',        'EscalasController', 'atualizar');
$router->post('escalas/{id}/excluir',       'EscalasController', 'excluir');
$router->post('escalas/zerar-12x36',        'EscalasController', 'zerar12x36');
$router->post('escalas/zerar-adm',          'EscalasController', 'zerarAdm');
$router->post('escalas/equipe',             'EscalasController', 'salvarEquipe');
$router->post('escalas/gerar-12x36',        'EscalasController', 'gerarEscala12x36');
$router->post('escalas/gerar-adm',          'EscalasController', 'gerarEscalaAdm');
$router->post('escalas/config-adm',         'EscalasController', 'salvarConfigAdm');
$router->get('escalas/debug-equipes',       'EscalasController', 'debugEquipes');
$router->get('trocas',                      'EscalasController', 'trocas');
$router->post('trocas/solicitar',           'EscalasController', 'solicitarTroca');
    $router->post('trocas/{id}/aprovar',        'EscalasController', 'aprovarTroca');
// =============================================
// ROTAS — ESCOLAS E RONDA ESCOLAR
// =============================================
$router->get('escolas/nova',                'EscolasController', 'nova');
$router->post('escolas/salvar',             'EscolasController', 'salvar');
$router->get('escolas/{id}/editar',         'EscolasController', 'editar');
$router->post('escolas/{id}/excluir',       'EscolasController', 'excluir');

// Rondas
$router->get('ronda-escolar',               'RondaEscolarController', 'index');
$router->get('ronda-escolar/registrar',     'RondaEscolarController', 'registrar');
$router->post('ronda-escolar/registrar',    'RondaEscolarController', 'salvarRegistro');
$router->get('ronda-escolar/{id}',          'RondaEscolarController', 'escola');

// =============================================
// ROTAS — MARIA DA PENHA
// =============================================
$router->get('maria-penha',                 'MariaPenhaController', 'index');
$router->get('maria-penha/mapa',            'MariaPenhaController', 'mapa');    // ANTES de {id}
$router->get('maria-penha/nova',            'MariaPenhaController', 'nova');
$router->post('maria-penha/nova',           'MariaPenhaController', 'salvar');
$router->get('maria-penha/{id}/editar',     'MariaPenhaController', 'editar');
$router->post('maria-penha/{id}/excluir',   'MariaPenhaController', 'excluir');
$router->get('maria-penha/{id}',            'MariaPenhaController', 'ver');
$router->post('maria-penha/{id}/visita',    'MariaPenhaController', 'registrarVisita');
$router->post('maria-penha/{id}/medida',    'MariaPenhaController', 'novaMedida');

// =============================================
// ROTAS — CORREGEDORIA
// =============================================
$router->get('corregedoria',                'CorregedoriaController', 'index');
$router->get('corregedoria/novo',           'CorregedoriaController', 'novo');
$router->post('corregedoria/novo',          'CorregedoriaController', 'salvar');
$router->get('corregedoria/{id}',           'CorregedoriaController', 'ver');
$router->post('corregedoria/{id}/movimentar','CorregedoriaController','addMovimentacao');

// =============================================
// ROTAS — AUDITORIA
// =============================================
$router->get('auditoria',                   'AuditoriaController', 'index');
$router->get('auditoria/logins',            'AuditoriaController', 'logins');

// =============================================
// ROTAS — COMUNICADOS
// =============================================
$router->get('comunicados',                 'ComunicadosController', 'index');
$router->get('comunicados/novo',            'ComunicadosController', 'novo');
$router->post('comunicados/novo',           'ComunicadosController', 'salvar');
$router->post('comunicados/{id}/confirmar', 'ComunicadosController', 'confirmar');

// =============================================
// ROTAS — CLIMA
// =============================================
$router->get('clima',        'ClimaController', 'index');
$router->get('clima/dados',  'ClimaController', 'dados');

// =============================================
// ROTAS — CONSULTAS
// =============================================
$router->get('consultas',               'ConsultasController', 'index');
$router->get('consultas/fipe/marcas',   'ConsultasController', 'fipeMarcas');
$router->get('consultas/fipe/modelos',  'ConsultasController', 'fipeModelos');
$router->get('consultas/fipe/anos',     'ConsultasController', 'fipeAnos');
$router->get('consultas/fipe/valor',    'ConsultasController', 'fipeValor');
$router->get('consultas/cep',           'ConsultasController', 'cep');
$router->get('consultas/cnpj',         'ConsultasController', 'cnpj');
$router->get('consultas/feriados',     'ConsultasController', 'feriados');
$router->get('consultas/ibge',         'ConsultasController', 'ibge');
$router->get('consultas/validar',      'ConsultasController', 'validar');


// =============================================
// ROTAS — RELATÓRIOS
// =============================================
$router->get('relatorios',                   'RelatoriosController', 'index');
$router->get('relatorios/taloes',            'RelatoriosController', 'taloes');
$router->get('relatorios/escalas',           'RelatoriosController', 'escalas');
$router->get('relatorios/maria-penha',       'RelatoriosController', 'mariaPenha');
$router->get('relatorios/ronda-escolar',     'RelatoriosController', 'rondaEscolar');
$router->get('relatorios/frota',             'RelatoriosController', 'frota');
$router->get('relatorios/corregedoria',      'RelatoriosController', 'corregedoria');
$router->get('relatorios/auditoria',         'RelatoriosController', 'auditoria');

// Despachar
$router->dispatch();

