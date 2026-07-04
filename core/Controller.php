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
// SIG-GCM — Controller Base
// =============================================

abstract class Controller {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
        Auth::init();
    }

    protected function view(string $view, array $data = [], string $layout = 'main'): void {
        // Disponibilizar dados para a view
        extract($data);
        $viewFile   = ROOT_PATH . '/views/' . $view . '.php';
        $layoutFile = ROOT_PATH . '/views/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            die("View não encontrada: {$view}");
        }

        // Capturar o conteúdo da view
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Renderizar dentro do layout
        if ($layout && file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    protected function back(): void {
        $ref = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/';
        header('Location: ' . $ref);
        exit;
    }

    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function getFlash(): ?array {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    protected function paginate(int $total, int $page = 1, int $perPage = ITEMS_PER_PAGE): array {
        $totalPages = (int)ceil($total / $perPage);
        $page       = max(1, min($page, $totalPages));
        $offset     = ($page - 1) * $perPage;
        return [
            'total'       => $total,
            'per_page'    => $perPage,
            'current'     => $page,
            'total_pages' => $totalPages,
            'offset'      => $offset,
            'has_prev'    => $page > 1,
            'has_next'    => $page < $totalPages,
        ];
    }

    protected function verifyCsrf(): void {
        $token = $_POST['csrf_token'] ?? '';
        if (!Auth::verifyCsrf($token)) {
            $this->flash('error', 'Token de segurança inválido. Tente novamente.');
            $this->back();
        }
        // Regenerar token após uso
        unset($_SESSION['csrf_token']);
    }
}
