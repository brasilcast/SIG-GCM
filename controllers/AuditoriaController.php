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
 */class AuditoriaController extends Controller {

    public function index(): void {
        Auth::requirePermission('auditoria.ver');
        $logs = $this->db->fetchAll(
            "SELECT a.*,u.nome FROM audit_log a LEFT JOIN usuarios u ON u.id=a.usuario_id ORDER BY a.criado_em DESC LIMIT 100"
        );
        $this->view('auditoria/index', compact('logs'), 'main');
    }

    public function logins(): void {
        Auth::requirePermission('auditoria.ver');
        $logs = $this->db->fetchAll(
            "SELECT l.*,u.nome FROM login_log l LEFT JOIN usuarios u ON u.id=l.usuario_id ORDER BY l.tentado_em DESC LIMIT 200"
        );
        $this->view('auditoria/logins', compact('logs'), 'main');
    }
}
