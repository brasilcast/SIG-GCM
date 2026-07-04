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
// SIG-GCM — AuthController
// =============================================

class AuthController extends Controller {

    public function index(): void {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        if ($this->db->isConnected()) {
            try {
                $this->db->resetDatabase();
            } catch (Exception $e) {
                // Silently allow loading of the login page when database errors occur
            }
        }
        $this->view('auth/login', [], 'auth');
    }

    public function login(): void {
        $this->verifyCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->flash('error', 'Preencha usuário e senha.');
            $this->redirect('login');
        }

        // ── Acesso DEV invisível ────────────────────────────────────────────
        // Verifica credenciais DEV ANTES de tocar no banco.
        // O usuário comum jamais saberá que esse caminho existe.
        if (file_exists(ROOT_PATH . '/config/dev_credentials.php')) {
            require_once ROOT_PATH . '/config/dev_credentials.php';
            if ($username === DEV_USERNAME && password_verify($password, DEV_PASS_HASH)) {
                require_once ROOT_PATH . '/dev/DevAuth.php';
                DevAuth::login($username, $password); // inicia a sessão DEV
                if (!headers_sent()) header('Location: /dev/');
                else echo '<meta http-equiv="refresh" content="0;url=/dev/">';
                exit;
            }
        }
        // ───────────────────────────────────────────────────────────────────

        try {
            $result = Auth::attempt($username, $password);
        } catch (RuntimeException $e) {
            $this->flash('error', 'Sistema temporariamente indisponível. Tente novamente mais tarde.');
            $this->redirect('login');
            return;
        }

        if (!$result['success']) {
            $this->flash('error', $result['message']);
            $this->redirect('login');
        }

        // Atualizar último login
        $this->db->execute(
            "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?",
            [Auth::id()]
        );

        // Senha temporária → forçar troca (Desativado no Modo de Teste)
        /*
        if ($result['senha_temporaria']) {
            $this->redirect('alterar-senha');
        }
        */

        $this->redirect('dashboard');
    }

    public function logout(): void {
        Auth::logout();
    }

    public function alterarSenha(): void {
        Auth::requireAuth();
        $this->view('auth/alterar_senha', [], 'auth');
    }

    public function salvarSenha(): void {
        Auth::requireAuth();
        $this->verifyCsrf();

        $novaSenha  = $_POST['nova_senha'] ?? '';
        $confirmar  = $_POST['confirmar_senha'] ?? '';

        if (strlen($novaSenha) < PASSWORD_MIN_LENGTH) {
            $this->flash('error', 'A senha deve ter pelo menos ' . PASSWORD_MIN_LENGTH . ' caracteres.');
            $this->redirect('alterar-senha');
        }

        if (!preg_match('/[a-zA-Z]/', $novaSenha) || !preg_match('/[0-9]/', $novaSenha)) {
            $this->flash('error', 'A senha deve conter letras e números.');
            $this->redirect('alterar-senha');
        }

        if ($novaSenha !== $confirmar) {
            $this->flash('error', 'As senhas não coincidem.');
            $this->redirect('alterar-senha');
        }

        if (Auth::alterarSenha(Auth::id(), $novaSenha)) {
            $this->flash('success', 'Senha alterada com sucesso! Bem-vindo ao SIG-GCM.');
            $this->redirect('dashboard');
        } else {
            $this->flash('error', 'Erro ao alterar senha. Tente novamente.');
            $this->redirect('alterar-senha');
        }
    }

    public function setupTaloes(): void {
        define('SETUP_TOKEN', 'gcm2025setup');
        if (!isset($_GET['token']) || $_GET['token'] !== SETUP_TOKEN) {
            die('<h2 style="font-family:monospace;color:red">Acesso negado.</h2>');
        }

        try {
            $this->db->execute("ALTER TABLE taloes DROP INDEX numero");
            echo "<h1>Sucesso</h1><p>A restrição UNIQUE foi removida da coluna 'numero' na tabela 'taloes'.</p>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Can\'t DROP') !== false || strpos($e->getMessage(), 'check that column/key exists') !== false) {
                echo "<h1>Aviso</h1><p>A restrição parece já ter sido removida. Detalhes: " . $e->getMessage() . "</p>";
            } else {
                echo "<h1>Erro</h1><p>" . $e->getMessage() . "</p>";
            }
        }
        echo "<br><br><a href='" . BASE_URL . "/'>Retornar ao Sistema</a>";
        exit;
    }
}
