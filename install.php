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
// SIG-GCM — Instalador / Configuração do Banco
// =============================================
session_start();

$error = '';
$success = '';

// Se já temos as constantes de configuração, vamos usar para pré-preencher
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? 'localhost';
    $port = $_POST['port'] ?? '3306';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $name = $_POST['name'] ?? '';

    if (empty($host) || empty($user) || empty($name)) {
        $error = "Host, Usuário e Nome do Banco são obrigatórios.";
    } else {
        try {
            // Testando a conexão apenas no host (sem conectar no banco específico ainda)
            $test_dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $pdo = new PDO($test_dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            
            // Tenta criar o banco de dados caso não exista
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Grava as novas configurações no config/database.php
            $config_content = "<?php\n"
                 . "// =============================================\n"
                 . "// SIG-GCM — Configuração do Banco de Dados\n"
                 . "// =============================================\n\n"
                 . "define('DB_HOST', '$host');\n"
                 . "define('DB_PORT', '$port');\n"
                 . "define('DB_NAME', '$name');\n"
                 . "define('DB_USER', '$user');\n"
                 . "define('DB_PASS', '$pass');\n"
                 . "define('DB_CHARSET', 'utf8mb4');\n";
                 
            if (file_put_contents(__DIR__ . '/config/database.php', $config_content) !== false) {
                // Configuração gravada com sucesso!
                // Redireciona para o `setup.php` para instalar as tabelas
                header("Location: /migrations/setup.php");
                exit;
            } else {
                $error = "Não foi possível gravar o arquivo config/database.php. Verifique as permissões de gravação da pasta config/.";
            }

        } catch (PDOException $e) {
            $error = "Falha ao conectar no servidor MySQL: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Banco de Dados - SIG-GCM</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, var(--bg-body), #f0f4f8); 
            display: flex; align-items: center; justify-content: center; 
            min-height: 100vh; padding: 1rem;
        }
        .install-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
        }
        .install-header { text-align: center; margin-bottom: 2rem; }
        .install-header h1 { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin-top: .5rem; }
        .install-header i { font-size: 3rem; color: var(--primary-color); }
        .install-header p { color: var(--text-muted); font-size: .95rem; margin-top: .5rem; }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="install-header">
            <i class="fa-solid fa-server"></i>
            <h1>Configuração do Banco</h1>
            <p>O sistema não conseguiu se conectar ao banco de dados usando as credenciais atuais. Informe os novos dados abaixo.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> <strong>Erro:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Servidor (Host)</label>
                <input type="text" name="host" class="form-control" value="<?= defined('DB_HOST') ? DB_HOST : 'localhost' ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Porta</label>
                <input type="text" name="port" class="form-control" value="<?= defined('DB_PORT') ? DB_PORT : '3306' ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Nome do Banco de Dados</label>
                <input type="text" name="name" class="form-control" value="<?= defined('DB_NAME') ? DB_NAME : 'sig_gcm' ?>" required placeholder="ex: sig_gcm, u123_gcm">
            </div>

            <div class="form-group">
                <label class="form-label">Usuário do Banco</label>
                <input type="text" name="user" class="form-control" value="<?= defined('DB_USER') ? DB_USER : 'root' ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label">Senha do Banco</label>
                <div style="position: relative;">
                    <input type="password" id="pass" name="pass" class="form-control" value="<?= defined('DB_PASS') ? DB_PASS : '' ?>">
                    <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 10px; background: none; border: none; color: var(--text-muted); cursor: pointer;">
                        <i class="fa-solid fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: var(--radius-lg); padding: .75rem; font-size: 1rem;">
                <i class="fa-solid fa-plug" style="margin-right: .5rem;"></i> Testar Conexão e Gravar
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('pass');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
