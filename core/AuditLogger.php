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
// SIG-GCM — AuditLogger
// Registra automaticamente ações no sistema
// =============================================

class AuditLogger {

    public static function log(
        string  $acao,
        string  $tabela,
        ?int    $registroId = null,
        ?array  $dadosAntes = null,
        ?array  $dadosDepois = null
    ): void {
        try {
            $db     = Database::getInstance();
            $userId = Auth::id();
            $ip     = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $ua     = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $db->execute(
                "INSERT INTO audit_log 
                 (usuario_id, acao, tabela, registro_id, dados_antes, dados_depois, ip, criado_em)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $userId,
                    $acao,
                    $tabela,
                    $registroId,
                    $dadosAntes  ? json_encode($dadosAntes,  JSON_UNESCAPED_UNICODE) : null,
                    $dadosDepois ? json_encode($dadosDepois, JSON_UNESCAPED_UNICODE) : null,
                    $ip,
                ]
            );
        } catch (Exception $e) {
            // Silencioso
        }
    }

    public static function create(string $tabela, int $id, array $dados): void {
        self::log('CREATE', $tabela, $id, null, $dados);
    }

    public static function update(string $tabela, int $id, array $antes, array $depois): void {
        self::log('UPDATE', $tabela, $id, $antes, $depois);
    }

    public static function delete(string $tabela, int $id, array $dados): void {
        self::log('DELETE', $tabela, $id, $dados, null);
    }

    public static function view(string $tabela, int $id): void {
        self::log('VIEW', $tabela, $id);
    }
}
