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
// SIG-GCM — DashboardController
// =============================================

class DashboardController extends Controller {

    public function index(): void {
        Auth::requireAuth();

        $perfil = Auth::perfil();
        $userId = Auth::id();
        $stats  = [];

        try {
            switch ($perfil) {
                case 'operador':
                    $stats = $this->statsOperador($userId);
                    break;
                case 'inspetor':
                    $stats = $this->statsInspetor();
                    break;
                case 'subcomandante':
                case 'comandante':
                    $stats = $this->statsComandante();
                    break;
                case 'corregedor':
                    $stats = $this->statsCorregedor();
                    break;
                case 'admin':
                    $stats = $this->statsAdmin();
                    break;
            }
        } catch (Exception $e) {
            $stats = []; // Dashboard não quebra se uma query falhar
        }

        // Comunicados não lidos
        try {
            $comunicados = $this->db->fetchAll(
                "SELECT c.* FROM comunicados c
                 LEFT JOIN comunicados_leitura cl ON cl.comunicado_id = c.id AND cl.usuario_id = ?
                 WHERE cl.comunicado_id IS NULL
                   AND (c.valido_ate IS NULL OR c.valido_ate >= CURDATE())
                   AND (c.destino = 'todos'
                        OR (c.destino = 'perfil' AND c.perfil_destino = ?)
                        OR (c.destino = 'usuario' AND c.usuario_destino_id = ?))
                 ORDER BY c.tipo DESC, c.criado_em DESC
                 LIMIT 5",
                [$userId, $perfil, $userId]
            );
        } catch (Exception $e) {
            $comunicados = [];
        }

        $this->view('dashboard/index', compact('stats', 'comunicados', 'perfil'));
    }

    // -------- Stats por Perfil --------

    private function statsOperador(int $userId): array {
        $agora = new \DateTime();
        $hora  = (int)$agora->format('H');

        // Determina o início do turno atual:
        // 00:00–05:59 → ainda no turno da noite que começou às 18:00 de ontem
        // 06:00–17:59 → turno diurno que começou às 06:00 de hoje
        // 18:00–23:59 → turno da noite que começou às 18:00 de hoje
        if ($hora < 6) {
            $inicioTurno = (clone $agora)->modify('yesterday')->setTime(18, 0, 0);
        } elseif ($hora < 18) {
            $inicioTurno = (clone $agora)->setTime(6, 0, 0);
        } else {
            $inicioTurno = (clone $agora)->setTime(18, 0, 0);
        }
        $inicioStr = $inicioTurno->format('Y-m-d H:i:s');

        $hoje = $agora->format('Y-m-d');

        $escala = $this->db->fetchAll(
            "SELECT * FROM escalas WHERE agente_id = ? AND data BETWEEN ? AND ? ORDER BY data",
            [$userId, date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week'))]
        );

        $meusTaloes = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM taloes WHERE data_hora_abertura >= ?",
            [$inicioStr]
        );

        $ultimaRonda = $this->db->fetchOne(
            "SELECT r.*, e.nome as escola_nome FROM rondas_escolares r
             JOIN escolas e ON e.id = r.escola_id
             WHERE r.agente_id = ? ORDER BY r.realizado_em DESC LIMIT 1",
            [$userId]
        );

        $bancoHoras = $this->db->fetchOne(
            "SELECT COALESCE(SUM(CASE WHEN tipo='credito' THEN horas ELSE -horas END),0) as saldo
             FROM banco_horas WHERE agente_id = ?",
            [$userId]
        );

        $taloesAbertos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM taloes WHERE status IN ('aberto')"
        );

        return compact('escala', 'meusTaloes', 'ultimaRonda', 'bancoHoras', 'taloesAbertos');
    }

    private function statsInspetor(): array {
        $hoje = date('Y-m-d');

        $taloesAbertos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM taloes WHERE status IN ('aberto')"
        );
        $escalaHoje = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM escalas WHERE data = ?", [$hoje]
        );
        $trocasPendentes = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM trocas_plantao WHERE status = 'pendente'"
        );
        $medidasVencendo = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM medidas_protetivas
             WHERE status='vigente' AND data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );
        $rondasSemVisita = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM escolas e
             WHERE e.ativa = 1 AND (
                SELECT MAX(r.realizado_em) FROM rondas_escolares r WHERE r.escola_id = e.id
             ) < DATE_SUB(NOW(), INTERVAL 7 DAY) OR NOT EXISTS (
                SELECT 1 FROM rondas_escolares r WHERE r.escola_id = e.id
             )"
        );

        return compact('taloesAbertos', 'escalaHoje', 'trocasPendentes', 'medidasVencendo', 'rondasSemVisita');
    }

    private function statsComandante(): array {
        $hoje = date('Y-m-d');

        $totalAgentes     = $this->db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE ativo=1");
        $escaladosHoje    = $this->db->fetchOne("SELECT COUNT(*) as total FROM escalas WHERE data=?", [$hoje]);
        $taloesAbertos    = $this->db->fetchOne("SELECT COUNT(*) as total FROM taloes WHERE status IN ('aberto')");
        $taloesHoje       = $this->db->fetchOne("SELECT COUNT(*) as total FROM taloes WHERE DATE(data_hora_abertura)=?", [$hoje]);
        $viaturasDisp     = $this->db->fetchOne("SELECT COUNT(*) as total FROM viaturas WHERE status='disponivel'");
        $viaturasManut    = $this->db->fetchOne("SELECT COUNT(*) as total FROM viaturas WHERE status='manutencao'");
        $medidasVencidas  = $this->db->fetchOne("SELECT COUNT(*) as total FROM medidas_protetivas WHERE status='vigente' AND data_vencimento < CURDATE()");
        $trocasPendentes  = $this->db->fetchOne("SELECT COUNT(*) as total FROM trocas_plantao WHERE status='pendente'");
        $processosAbertos = $this->db->fetchOne("SELECT COUNT(*) as total FROM processos_corregedoria WHERE status IN ('aberto','em_andamento')");

        $taloesSemana = $this->db->fetchAll(
            "SELECT DATE(data_hora_abertura) as dia, COUNT(*) as total
             FROM taloes WHERE data_hora_abertura >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY dia ORDER BY dia"
        );

        return compact(
            'totalAgentes','escaladosHoje','taloesAbertos','taloesHoje',
            'viaturasDisp','viaturasManut','medidasVencidas','trocasPendentes',
            'processosAbertos','taloesSemana'
        );
    }

    private function statsCorregedor(): array {
        $processosAbertos    = $this->db->fetchOne("SELECT COUNT(*) as total FROM processos_corregedoria WHERE status='aberto'");
        $processosAndamento  = $this->db->fetchOne("SELECT COUNT(*) as total FROM processos_corregedoria WHERE status='em_andamento'");
        $prazosVencendo      = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM processos_corregedoria WHERE status IN ('aberto','em_andamento') AND prazo_legal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)"
        );
        $processosConcluidos = $this->db->fetchOne("SELECT COUNT(*) as total FROM processos_corregedoria WHERE status='concluido'");

        $processos = $this->db->fetchAll(
            "SELECT p.*, u.nome as investigado_nome FROM processos_corregedoria p
             LEFT JOIN usuarios u ON u.id = p.investigado_id
             WHERE p.status IN ('aberto','em_andamento')
             ORDER BY p.prazo_legal LIMIT 10"
        );

        return compact('processosAbertos','processosAndamento','prazosVencendo','processosConcluidos','processos');
    }

    private function statsAdmin(): array {
        $totalUsuarios  = $this->db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE ativo=1");
        $totalTaloes    = $this->db->fetchOne("SELECT COUNT(*) as total FROM taloes");
        $totalViaturas  = $this->db->fetchOne("SELECT COUNT(*) as total FROM viaturas WHERE ativa=1");
        $totalEscolas   = $this->db->fetchOne("SELECT COUNT(*) as total FROM escolas WHERE ativa=1");

        $ultimosLogins = $this->db->fetchAll(
            "SELECT ll.*, u.nome FROM login_log ll
             LEFT JOIN usuarios u ON u.id = ll.usuario_id
             WHERE ll.sucesso = 1 ORDER BY ll.tentado_em DESC LIMIT 10"
        );

        return compact('totalUsuarios','totalTaloes','totalViaturas','totalEscolas','ultimosLogins');
    }
}
