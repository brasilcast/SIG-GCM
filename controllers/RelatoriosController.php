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
// SIG-GCM — RelatoriosController
// =============================================

class RelatoriosController extends Controller {

    // ── Helpers de filtro ──────────────────────────────────────────
    private function getPeriodo(): array {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');          // 1º dia do mês atual
        $fim    = $_GET['fim']    ?? date('Y-m-t');           // último dia do mês atual
        return [$inicio, $fim];
    }

    private function requerRelatorio(): void {
        Auth::requireAuth();
        if (!Auth::can('relatorios.basicos') && !Auth::can('relatorios.gerenciais')) {
            http_response_code(403);
            include ROOT_PATH . '/views/errors/403.php';
            exit;
        }
    }

    // ── Dashboard Principal ────────────────────────────────────────
    public function index(): void {
        $this->requerRelatorio();

        // KPI Cards
        $kpis = [];
        try {
            $mesAtual  = date('Y-m');
            $hoje      = date('Y-m-d');
            $semIni    = date('Y-m-d', strtotime('monday this week'));
            $semFim    = date('Y-m-d', strtotime('sunday this week'));

            $kpis['taloes_mes']     = $this->db->fetchOne("SELECT COUNT(*) as n FROM taloes WHERE DATE_FORMAT(data_hora_abertura,'%Y-%m')=?", [$mesAtual])['n'] ?? 0;
            $kpis['taloes_abertos'] = $this->db->fetchOne("SELECT COUNT(*) as n FROM taloes WHERE status='aberto'")['n'] ?? 0;
            $kpis['agentes_ativos'] = $this->db->fetchOne("SELECT COUNT(*) as n FROM usuarios WHERE ativo=1")['n'] ?? 0;
            $kpis['viaturas_disp']  = $this->db->fetchOne("SELECT COUNT(*) as n FROM viaturas WHERE status='disponivel' AND ativa=1")['n'] ?? 0;
            $kpis['viaturas_manut'] = $this->db->fetchOne("SELECT COUNT(*) as n FROM viaturas WHERE status='manutencao'")['n'] ?? 0;
            $kpis['visitas_sem']    = $this->db->fetchOne("SELECT COUNT(*) as n FROM visitas_penha WHERE DATE(realizado_em) BETWEEN ? AND ?", [$semIni, $semFim])['n'] ?? 0;
            $kpis['rondas_sem']     = $this->db->fetchOne("SELECT COUNT(*) as n FROM rondas_escolares WHERE DATE(realizado_em) BETWEEN ? AND ?", [$semIni, $semFim])['n'] ?? 0;
            $kpis['processos_abertos'] = $this->db->fetchOne("SELECT COUNT(*) as n FROM processos_corregedoria WHERE status IN ('aberto','em_andamento')")['n'] ?? 0;
            $kpis['medidas_vencendo']  = $this->db->fetchOne("SELECT COUNT(*) as n FROM medidas_protetivas WHERE status='vigente' AND data_vencimento BETWEEN ? AND DATE_ADD(?,INTERVAL 30 DAY)", [$hoje, $hoje])['n'] ?? 0;
            $kpis['trocas_pendentes']  = $this->db->fetchOne("SELECT COUNT(*) as n FROM trocas_plantao WHERE status='pendente'")['n'] ?? 0;
        } catch (\Exception $e) {
            $kpis = array_fill_keys(['taloes_mes','taloes_abertos','agentes_ativos','viaturas_disp','viaturas_manut','visitas_sem','rondas_sem','processos_abertos','medidas_vencendo','trocas_pendentes'], 0);
        }

        // Gráfico 1: Talões por mês (12 meses)
        $taloesMes = [];
        try {
            $rows = $this->db->fetchAll(
                "SELECT DATE_FORMAT(data_hora_abertura,'%Y-%m') as mes, COUNT(*) as total
                 FROM taloes WHERE data_hora_abertura >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY mes ORDER BY mes"
            );
            foreach ($rows as $r) $taloesMes[$r['mes']] = (int)$r['total'];
        } catch (\Exception $e) {}

        // Gráfico 2: Talões por natureza (Top 8)
        $taloesPorNatureza = [];
        try {
            $rows = $this->db->fetchAll(
                "SELECT COALESCE(n.nome,'Sem Natureza') as nome, COUNT(*) as total
                 FROM taloes t LEFT JOIN naturezas_ocorrencia n ON n.id=t.natureza_id
                 GROUP BY n.nome ORDER BY total DESC LIMIT 8"
            );
            foreach ($rows as $r) $taloesPorNatureza[$r['nome']] = (int)$r['total'];
        } catch (\Exception $e) {}

        // Gráfico 3: Talões por dia da semana
        $taloesDiaSemana = [0,0,0,0,0,0,0];
        try {
            $rows = $this->db->fetchAll(
                "SELECT DAYOFWEEK(data_hora_abertura) as dow, COUNT(*) as total
                 FROM taloes WHERE data_hora_abertura >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                 GROUP BY dow"
            );
            foreach ($rows as $r) $taloesDiaSemana[(int)$r['dow']-1] = (int)$r['total'];
        } catch (\Exception $e) {}

        // Gráfico 4: Visitas Maria Penha por mês
        $visitasMes = [];
        try {
            $rows = $this->db->fetchAll(
                "SELECT DATE_FORMAT(realizado_em,'%Y-%m') as mes, COUNT(*) as total
                 FROM visitas_penha WHERE realizado_em >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY mes ORDER BY mes"
            );
            foreach ($rows as $r) $visitasMes[$r['mes']] = (int)$r['total'];
        } catch (\Exception $e) {}

        // Gráfico 5: Consumo médio frota por mês
        $consumoMes = [];
        try {
            $rows = $this->db->fetchAll(
                "SELECT DATE_FORMAT(data_abastecimento,'%Y-%m') as mes, ROUND(AVG(consumo_calculado),2) as media
                 FROM abastecimentos WHERE consumo_calculado IS NOT NULL AND data_abastecimento >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY mes ORDER BY mes"
            );
            foreach ($rows as $r) $consumoMes[$r['mes']] = (float)$r['media'];
        } catch (\Exception $e) {}

        $this->view('relatorios/index', compact(
            'kpis','taloesMes','taloesPorNatureza','taloesDiaSemana','visitasMes','consumoMes'
        ), 'main');
    }

    // ── Relatório: Talões ──────────────────────────────────────────
    public function taloes(): void {
        $this->requerRelatorio();
        [$inicio, $fim] = $this->getPeriodo();

        $where  = ['DATE(t.data_hora_abertura) BETWEEN ? AND ?'];
        $params = [$inicio, $fim];

        $agente  = (int)($_GET['agente'] ?? 0);
        $natId   = (int)($_GET['natureza'] ?? 0);
        $status  = $_GET['status'] ?? '';
        $bairro  = trim($_GET['bairro'] ?? '');

        if ($agente)  { $where[] = 't.agente_id = ?';     $params[] = $agente; }
        if ($natId)   { $where[] = 't.natureza_id = ?';   $params[] = $natId; }
        if ($status)  { $where[] = 't.status = ?';         $params[] = $status; }
        if ($bairro)  { $where[] = 't.bairro LIKE ?';      $params[] = "%$bairro%"; }

        $w = implode(' AND ', $where);

        try {
            $taloes = $this->db->fetchAll(
                "SELECT t.*, u.nome as agente_nome, n.nome as natureza_nome, v.prefixo as viatura
                 FROM taloes t
                 LEFT JOIN usuarios u ON u.id=t.agente_id
                 LEFT JOIN naturezas_ocorrencia n ON n.id=t.natureza_id
                 LEFT JOIN viaturas v ON v.id=t.viatura_id
                 WHERE $w ORDER BY t.data_hora_abertura DESC LIMIT 500",
                $params
            );

            $totais = $this->db->fetchOne(
                "SELECT COUNT(*) as total,
                        SUM(status='fechado') as fechados,
                        SUM(status='aberto')  as abertos,
                        ROUND(AVG(TIMESTAMPDIFF(HOUR,data_hora_abertura,data_hora_fechamento)),1) as media_horas
                 FROM taloes t WHERE $w", $params
            );

            $porNatureza = $this->db->fetchAll(
                "SELECT COALESCE(n.nome,'Sem Natureza') as nome, COUNT(*) as total
                 FROM taloes t LEFT JOIN naturezas_ocorrencia n ON n.id=t.natureza_id
                 WHERE $w GROUP BY n.nome ORDER BY total DESC LIMIT 10", $params
            );

            $porBairro = $this->db->fetchAll(
                "SELECT bairro, COUNT(*) as total FROM taloes t WHERE $w AND bairro!=''
                 GROUP BY bairro ORDER BY total DESC LIMIT 10", $params
            );

            $porDia = $this->db->fetchAll(
                "SELECT DATE(data_hora_abertura) as dia, COUNT(*) as total
                 FROM taloes t WHERE $w GROUP BY dia ORDER BY dia", $params
            );

            $topAgentes = $this->db->fetchAll(
                "SELECT u.nome, COUNT(*) as total FROM taloes t JOIN usuarios u ON u.id=t.agente_id
                 WHERE $w GROUP BY u.nome ORDER BY total DESC LIMIT 5", $params
            );

            $naturezas = $this->db->fetchAll("SELECT id, nome FROM naturezas_ocorrencia ORDER BY nome");
            $agentes   = $this->db->fetchAll("SELECT id, nome FROM usuarios WHERE ativo=1 ORDER BY nome");

        } catch (\Exception $e) {
            $taloes = $totais = $porNatureza = $porBairro = $porDia = $topAgentes = [];
            $naturezas = $agentes = [];
        }

        if (isset($_GET['export'])) {
            $this->exportarCsv($taloes, ['numero','data_hora_abertura','natureza_nome','bairro','status','agente_nome','viatura'], 'taloes');
        }

        $this->view('relatorios/taloes', compact(
            'taloes','totais','porNatureza','porBairro','porDia','topAgentes',
            'naturezas','agentes','inicio','fim','agente','natId','status','bairro'
        ), 'main');
    }

    // ── Relatório: Escalas ────────────────────────────────────────
    public function escalas(): void {
        $this->requerRelatorio();
        [$inicio, $fim] = $this->getPeriodo();

        $where  = ['e.data BETWEEN ? AND ?'];
        $params = [$inicio, $fim];
        $equipe = $_GET['equipe'] ?? '';
        $turno  = $_GET['turno']  ?? '';
        if ($equipe) { $where[] = 'e.turno = ?'; $params[] = $equipe; }
        if ($turno)  { $where[] = 'e.turno = ?'; $params[] = $turno; }
        $w = implode(' AND ', $where);

        try {
            $porEquipe = $this->db->fetchAll(
                "SELECT e.turno, COUNT(*) as total FROM escalas e WHERE $w GROUP BY e.turno ORDER BY total DESC",
                $params
            );

            $topAgentes = $this->db->fetchAll(
                "SELECT u.nome, COUNT(*) as total FROM escalas e JOIN usuarios u ON u.id=e.agente_id
                 WHERE $w GROUP BY u.nome ORDER BY total DESC LIMIT 10",
                $params
            );

            $porDia = $this->db->fetchAll(
                "SELECT e.data, COUNT(*) as total FROM escalas e WHERE $w GROUP BY e.data ORDER BY e.data",
                $params
            );

            $trocas = $this->db->fetchOne(
                "SELECT SUM(status='pendente') as pendentes, SUM(status='aprovada') as aprovadas, SUM(status='negada') as negadas
                 FROM trocas_plantao WHERE DATE(criado_em) BETWEEN ? AND ?",
                [$inicio, $fim]
            );

            $escalas = $this->db->fetchAll(
                "SELECT e.*, u.nome as agente_nome FROM escalas e
                 JOIN usuarios u ON u.id=e.agente_id WHERE $w ORDER BY e.data DESC LIMIT 500",
                $params
            );

        } catch (\Exception $e) {
            $porEquipe = $topAgentes = $porDia = $escalas = [];
            $trocas = [];
        }

        if (isset($_GET['export'])) {
            $this->exportarCsv($escalas, ['data','agente_nome','turno','entrada','saida'], 'escalas');
        }

        $this->view('relatorios/escalas', compact(
            'porEquipe','topAgentes','porDia','trocas','escalas','inicio','fim','equipe','turno'
        ), 'main');
    }

    // ── Relatório: Maria da Penha ─────────────────────────────────
    public function mariaPenha(): void {
        $this->requerRelatorio();
        [$inicio, $fim] = $this->getPeriodo();
        $hoje = date('Y-m-d');

        try {
            $visistasMes = $this->db->fetchAll(
                "SELECT DATE_FORMAT(realizado_em,'%Y-%m') as mes, COUNT(*) as total
                 FROM visitas_penha WHERE realizado_em BETWEEN ? AND ?
                 GROUP BY mes ORDER BY mes",
                [$inicio.' 00:00:00', $fim.' 23:59:59']
            );

            $vitimas = $this->db->fetchAll(
                "SELECT v.*,
                   (SELECT COUNT(*) FROM visitas_penha vi WHERE vi.vitima_id=v.id AND DATE(vi.realizado_em) BETWEEN ? AND ?) as visitas_periodo,
                   (SELECT MAX(realizado_em) FROM visitas_penha WHERE vitima_id=v.id) as ultima_visita,
                   (SELECT COUNT(*) FROM medidas_protetivas mp WHERE mp.vitima_id=v.id AND mp.status='vigente') as medidas_ativas
                 FROM vitimas_penha v WHERE v.ativa=1",
                [$inicio, $fim]
            );

            $totalVitimas    = count($vitimas);
            $seVisita7Dias   = $this->db->fetchOne(
                "SELECT COUNT(*) as n FROM vitimas_penha v WHERE v.ativa=1 AND NOT EXISTS (
                    SELECT 1 FROM visitas_penha vi WHERE vi.vitima_id=v.id AND vi.realizado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 )"
            )['n'] ?? 0;
            $medidasVencidas = $this->db->fetchOne("SELECT COUNT(*) as n FROM medidas_protetivas WHERE status='vigente' AND data_vencimento < ?", [$hoje])['n'] ?? 0;
            $medidasVencendo = $this->db->fetchOne("SELECT COUNT(*) as n FROM medidas_protetivas WHERE status='vigente' AND data_vencimento BETWEEN ? AND DATE_ADD(?,INTERVAL 30 DAY)", [$hoje,$hoje])['n'] ?? 0;

            $porBairro = $this->db->fetchAll(
                "SELECT bairro, COUNT(*) as total FROM vitimas_penha WHERE ativa=1 AND bairro!='' GROUP BY bairro ORDER BY total DESC LIMIT 8"
            );

            $topAgentes = $this->db->fetchAll(
                "SELECT u.nome, COUNT(*) as total FROM visitas_penha vi JOIN usuarios u ON u.id=vi.agente_id
                 WHERE DATE(vi.realizado_em) BETWEEN ? AND ? GROUP BY u.nome ORDER BY total DESC LIMIT 5",
                [$inicio, $fim]
            );

            $visitas = $this->db->fetchAll(
                "SELECT vi.*, v.nome as vitima, u.nome as agente FROM visitas_penha vi
                 JOIN vitimas_penha v ON v.id=vi.vitima_id JOIN usuarios u ON u.id=vi.agente_id
                 WHERE DATE(vi.realizado_em) BETWEEN ? AND ? ORDER BY vi.realizado_em DESC LIMIT 300",
                [$inicio, $fim]
            );

        } catch (\Exception $e) {
            $visistasMes = $vitimas = $porBairro = $topAgentes = $visitas = [];
            $totalVitimas = $seVisita7Dias = $medidasVencidas = $medidasVencendo = 0;
        }

        if (isset($_GET['export'])) {
            $exp = array_map(fn($v) => ['nome'=>$v['nome'],'bairro'=>$v['bairro'],'prioridade'=>$v['prioridade'],'visitas_periodo'=>$v['visitas_periodo'],'ultima_visita'=>$v['ultima_visita'],'medidas_ativas'=>$v['medidas_ativas']], $vitimas);
            $this->exportarCsv($exp, ['nome','bairro','prioridade','visitas_periodo','ultima_visita','medidas_ativas'], 'maria_penha');
        }

        $this->view('relatorios/maria_penha', compact(
            'visistasMes','vitimas','porBairro','topAgentes','visitas',
            'totalVitimas','seVisita7Dias','medidasVencidas','medidasVencendo',
            'inicio','fim'
        ), 'main');
    }

    // ── Relatório: Ronda Escolar ──────────────────────────────────
    public function rondaEscolar(): void {
        $this->requerRelatorio();
        [$inicio, $fim] = $this->getPeriodo();

        try {
            $rondas = $this->db->fetchAll(
                "SELECT r.*, e.nome as escola_nome, e.bairro, u.nome as agente_nome, v.prefixo as viatura
                 FROM rondas_escolares r JOIN escolas e ON e.id=r.escola_id
                 JOIN usuarios u ON u.id=r.agente_id LEFT JOIN viaturas v ON v.id=r.viatura_id
                 WHERE DATE(r.realizado_em) BETWEEN ? AND ? ORDER BY r.realizado_em DESC LIMIT 500",
                [$inicio, $fim]
            );

            $totalRondas = count($rondas);

            $porEscola = $this->db->fetchAll(
                "SELECT e.nome, COUNT(*) as total FROM rondas_escolares r JOIN escolas e ON e.id=r.escola_id
                 WHERE DATE(r.realizado_em) BETWEEN ? AND ? GROUP BY e.nome ORDER BY total DESC LIMIT 10",
                [$inicio, $fim]
            );

            $topAgentes = $this->db->fetchAll(
                "SELECT u.nome, COUNT(*) as total FROM rondas_escolares r JOIN usuarios u ON u.id=r.agente_id
                 WHERE DATE(r.realizado_em) BETWEEN ? AND ? GROUP BY u.nome ORDER BY total DESC LIMIT 5",
                [$inicio, $fim]
            );

            $semVisita = $this->db->fetchAll(
                "SELECT e.nome, e.bairro,
                   (SELECT MAX(realizado_em) FROM rondas_escolares WHERE escola_id=e.id) as ultima
                 FROM escolas e WHERE e.ativa=1
                 HAVING ultima IS NULL OR ultima < DATE_SUB(NOW(), INTERVAL 7 DAY)
                 ORDER BY ultima ASC"
            );

            $porSemana = $this->db->fetchAll(
                "SELECT DATE_FORMAT(realizado_em,'%Y-%V') as semana, COUNT(*) as total
                 FROM rondas_escolares WHERE DATE(realizado_em) BETWEEN ? AND ?
                 GROUP BY semana ORDER BY semana",
                [$inicio, $fim]
            );

        } catch (\Exception $e) {
            $rondas = $porEscola = $topAgentes = $semVisita = $porSemana = [];
            $totalRondas = 0;
        }

        if (isset($_GET['export'])) {
            $this->exportarCsv($rondas, ['realizado_em','escola_nome','bairro','agente_nome','viatura','observacoes'], 'ronda_escolar');
        }

        $this->view('relatorios/ronda_escolar', compact(
            'rondas','totalRondas','porEscola','topAgentes','semVisita','porSemana','inicio','fim'
        ), 'main');
    }

    // ── Relatório: Frota ──────────────────────────────────────────
    public function frota(): void {
        $this->requerRelatorio();
        [$inicio, $fim] = $this->getPeriodo();

        try {
            $abastecimentos = $this->db->fetchAll(
                "SELECT a.*, v.prefixo, u.nome as agente FROM abastecimentos a
                 JOIN viaturas v ON v.id=a.viatura_id JOIN usuarios u ON u.id=a.agente_id
                 WHERE DATE(a.data_abastecimento) BETWEEN ? AND ? ORDER BY a.data_abastecimento DESC LIMIT 500",
                [$inicio, $fim]
            );

            $porViatura = $this->db->fetchAll(
                "SELECT v.prefixo,
                   COUNT(*) as abastecimentos,
                   ROUND(SUM(a.litros),1) as total_litros,
                   ROUND(SUM(a.valor_total),2) as custo_total,
                   ROUND(AVG(a.consumo_calculado),2) as consumo_medio,
                   MAX(a.km_atual) - MIN(a.km_anterior) as km_percorrido
                 FROM abastecimentos a JOIN viaturas v ON v.id=a.viatura_id
                 WHERE DATE(a.data_abastecimento) BETWEEN ? AND ?
                 GROUP BY v.prefixo ORDER BY custo_total DESC",
                [$inicio, $fim]
            );

            $custoMes = $this->db->fetchAll(
                "SELECT DATE_FORMAT(data_abastecimento,'%Y-%m') as mes, ROUND(SUM(valor_total),2) as total
                 FROM abastecimentos WHERE DATE(data_abastecimento) BETWEEN ? AND ?
                 GROUP BY mes ORDER BY mes",
                [$inicio, $fim]
            );

            $checklistsAlerta = $this->db->fetchAll(
                "SELECT c.*, v.prefixo, u.nome as agente FROM checklists_viatura c
                 JOIN viaturas v ON v.id=c.viatura_id JOIN usuarios u ON u.id=c.agente_id
                 WHERE c.status_geral != 'ok' AND DATE(c.realizado_em) BETWEEN ? AND ?
                 ORDER BY c.realizado_em DESC LIMIT 100",
                [$inicio, $fim]
            );

            $totalCusto  = array_sum(array_column($porViatura, 'custo_total'));
            $totalLitros = array_sum(array_column($porViatura, 'total_litros'));

        } catch (\Exception $e) {
            $abastecimentos = $porViatura = $custoMes = $checklistsAlerta = [];
            $totalCusto = $totalLitros = 0;
        }

        if (isset($_GET['export'])) {
            $this->exportarCsv($abastecimentos, ['prefixo','data_abastecimento','litros','valor_total','km_atual','consumo_calculado','posto','tipo_combustivel'], 'frota');
        }

        $this->view('relatorios/frota', compact(
            'abastecimentos','porViatura','custoMes','checklistsAlerta',
            'totalCusto','totalLitros','inicio','fim'
        ), 'main');
    }

    // ── Relatório: Corregedoria ────────────────────────────────────
    public function corregedoria(): void {
        $this->requerRelatorio();
        [$inicio, $fim] = $this->getPeriodo();

        try {
            $processos = $this->db->fetchAll(
                "SELECT p.*, u.nome as investigado_nome FROM processos_corregedoria p
                 LEFT JOIN usuarios u ON u.id=p.investigado_id
                 WHERE p.data_abertura BETWEEN ? AND ? ORDER BY p.data_abertura DESC",
                [$inicio, $fim]
            );

            $porStatus = $this->db->fetchAll(
                "SELECT status, COUNT(*) as total FROM processos_corregedoria
                 WHERE data_abertura BETWEEN ? AND ? GROUP BY status",
                [$inicio, $fim]
            );

            $porTipo = $this->db->fetchAll(
                "SELECT tipo, COUNT(*) as total FROM processos_corregedoria
                 WHERE data_abertura BETWEEN ? AND ? GROUP BY tipo ORDER BY total DESC",
                [$inicio, $fim]
            );

            $porMes = $this->db->fetchAll(
                "SELECT DATE_FORMAT(data_abertura,'%Y-%m') as mes, COUNT(*) as total
                 FROM processos_corregedoria WHERE data_abertura BETWEEN ? AND ?
                 GROUP BY mes ORDER BY mes",
                [$inicio, $fim]
            );

            $tempMedio = $this->db->fetchOne(
                "SELECT ROUND(AVG(DATEDIFF(updated_at,data_abertura)),1) as media_dias
                 FROM processos_corregedoria WHERE status='concluido' AND data_abertura BETWEEN ? AND ?",
                [$inicio, $fim]
            )['media_dias'] ?? null;

            $prazosVencidos = $this->db->fetchOne(
                "SELECT COUNT(*) as n FROM processos_corregedoria WHERE status IN ('aberto','em_andamento') AND prazo_legal < CURDATE()"
            )['n'] ?? 0;

        } catch (\Exception $e) {
            $processos = $porStatus = $porTipo = $porMes = [];
            $tempMedio = null; $prazosVencidos = 0;
        }

        if (isset($_GET['export'])) {
            $this->exportarCsv($processos, ['numero','tipo','investigado_nome','data_abertura','prazo_legal','status'], 'corregedoria');
        }

        $this->view('relatorios/corregedoria', compact(
            'processos','porStatus','porTipo','porMes','tempMedio','prazosVencidos','inicio','fim'
        ), 'main');
    }

    // ── Relatório: Auditoria ───────────────────────────────────────
    public function auditoria(): void {
        Auth::requirePermission('auditoria.ver');
        [$inicio, $fim] = $this->getPeriodo();

        try {
            $loginsPorDia = $this->db->fetchAll(
                "SELECT DATE(tentado_em) as dia, SUM(sucesso) as bem_sucedidos, SUM(1-sucesso) as falhos
                 FROM login_log WHERE DATE(tentado_em) BETWEEN ? AND ?
                 GROUP BY dia ORDER BY dia",
                [$inicio, $fim]
            );

            $topUsuarios = $this->db->fetchAll(
                "SELECT u.nome, COUNT(*) as total FROM login_log ll JOIN usuarios u ON u.id=ll.usuario_id
                 WHERE ll.sucesso=1 AND DATE(ll.tentado_em) BETWEEN ? AND ?
                 GROUP BY u.nome ORDER BY total DESC LIMIT 10",
                [$inicio, $fim]
            );

            $totalLogins   = $this->db->fetchOne("SELECT COUNT(*) as n FROM login_log WHERE DATE(tentado_em) BETWEEN ? AND ?", [$inicio,$fim])['n'] ?? 0;
            $loginsBemSuced= $this->db->fetchOne("SELECT COUNT(*) as n FROM login_log WHERE sucesso=1 AND DATE(tentado_em) BETWEEN ? AND ?", [$inicio,$fim])['n'] ?? 0;
            $loginsFalhos  = $totalLogins - $loginsBemSuced;

            $ultimasAcoes = $this->db->fetchAll(
                "SELECT ll.*, u.nome FROM login_log ll LEFT JOIN usuarios u ON u.id=ll.usuario_id
                 WHERE DATE(ll.tentado_em) BETWEEN ? AND ? ORDER BY ll.tentado_em DESC LIMIT 200",
                [$inicio, $fim]
            );

        } catch (\Exception $e) {
            $loginsPorDia = $topUsuarios = $ultimasAcoes = [];
            $totalLogins = $loginsBemSuced = $loginsFalhos = 0;
        }

        if (isset($_GET['export'])) {
            $this->exportarCsv($ultimasAcoes, ['tentado_em','nome','username_tentado','sucesso','ip'], 'auditoria');
        }

        $this->view('relatorios/auditoria', compact(
            'loginsPorDia','topUsuarios','totalLogins','loginsBemSuced','loginsFalhos','ultimasAcoes','inicio','fim'
        ), 'main');
    }

    // ── Exportação CSV genérica ────────────────────────────────────
    private function exportarCsv(array $rows, array $colunas, string $nome): void {
        $filename = "relatorio_{$nome}_" . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        $out = fopen('php://output', 'w');
        // BOM para compatibilidade com Excel
        fputs($out, "\xEF\xBB\xBF");
        fputcsv($out, $colunas, ';');
        foreach ($rows as $row) {
            $line = [];
            foreach ($colunas as $col) {
                $line[] = $row[$col] ?? '';
            }
            fputcsv($out, $line, ';');
        }
        fclose($out);
        exit;
    }
}
