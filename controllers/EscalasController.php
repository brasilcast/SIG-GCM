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
 */class EscalasController extends Controller {

    public function index(): void {
        Auth::requireAuth();
        $userId = Auth::id();
        $semana = $_GET['semana'] ?? date('Y-W');
        [$ano,$sem] = explode('-',$semana) + [date('Y'),date('W')];
        $seg = (new DateTime())->setISODate((int)$ano,(int)$sem)->format('Y-m-d');
        $dom = (new DateTime())->setISODate((int)$ano,(int)$sem,7)->format('Y-m-d');

        // Escalas 12x36 (grade do topo) — EXCLUI turno administrativo propositalmente
        if (Auth::can('escala.ver_todos')) {
            $escalas = $this->db->fetchAll(
                "SELECT e.*,u.nome,v.prefixo FROM escalas e JOIN usuarios u ON u.id=e.agente_id LEFT JOIN viaturas v ON v.id=e.viatura_id WHERE e.data BETWEEN ? AND ? AND e.turno <> 'administrativo' ORDER BY e.data,u.nome",
                [$seg,$dom]
            );
        } else {
            $escalas = $this->db->fetchAll(
                "SELECT e.*,u.nome,v.prefixo FROM escalas e JOIN usuarios u ON u.id=e.agente_id LEFT JOIN viaturas v ON v.id=e.viatura_id WHERE e.agente_id=? AND e.data BETWEEN ? AND ? AND e.turno <> 'administrativo' ORDER BY e.data",
                [$userId,$seg,$dom]
            );
        }
        $viaturas = $this->db->fetchAll("SELECT id,prefixo FROM viaturas WHERE ativa=1 ORDER BY prefixo");
        $agentes  = Auth::can('escala.criar') ? $this->db->fetchAll("SELECT id,nome,equipe FROM usuarios WHERE ativo=1 ORDER BY nome") : [];

        // Agrupar agentes por equipe para a view (Alfa e Charlie = Diurno, Bravo e Delta = Noturno)
        $equipes = ['alfa'=>[],'bravo'=>[],'charlie'=>[],'delta'=>[],'adm'=>[],'nenhuma'=>[]];
        foreach($agentes as $a) {
            if(!empty($a['equipe']) && isset($equipes[$a['equipe']])) $equipes[$a['equipe']][] = $a;
            else $equipes['nenhuma'][] = $a;
        }

        // Configuração horários ADM — auto-cria tabela se necessário
        $configAdm = ['entrada_manha'=>'07:30','saida_manha'=>'11:30','entrada_tarde'=>'13:00','saida_tarde'=>'17:00'];
        try {
            $pdo = $this->db->getConnection();
            
            // --- INJEÇÃO DA MIGRAÇÃO (Roda Sempre p/ Garantir) ---
            try { $pdo->exec("ALTER TABLE usuarios MODIFY COLUMN equipe ENUM('alfa','bravo','charlie','delta','adm') NULL DEFAULT NULL"); } catch(\Exception $e){}
            try { $pdo->exec("ALTER TABLE escalas MODIFY COLUMN turno ENUM('diurno','noturno','folga','administrativo') NOT NULL DEFAULT 'diurno'"); } catch(\Exception $e){}
            try { $pdo->exec("ALTER TABLE escalas ADD COLUMN saida TIME NULL AFTER entrada"); } catch(\Exception $e){}
            try { $pdo->exec("ALTER TABLE escalas ADD COLUMN entrada_tarde TIME NULL AFTER saida"); } catch(\Exception $e){}
            try { $pdo->exec("ALTER TABLE escalas ADD COLUMN saida_tarde TIME NULL AFTER entrada_tarde"); } catch(\Exception $e){}
            // --------------------------------------------------------

            // DUMP DE DIAGNÓSTICO
            $dump = "USUARIOS ATIVOS:\n";
            $uu = $this->db->fetchAll("SELECT id, username, equipe FROM usuarios WHERE ativo=1");
            foreach($uu as $u) { $dump .= "ID {$u['id']} | {$u['username']} | Equipe: '{$u['equipe']}'\n"; }
            file_put_contents(ROOT_PATH . '/db_dump.txt', $dump);

            $pdo->exec("CREATE TABLE IF NOT EXISTS config_adm (chave VARCHAR(50) PRIMARY KEY, valor VARCHAR(20) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $defaults = ['entrada_manha'=>'07:30','saida_manha'=>'11:30','entrada_tarde'=>'13:00','saida_tarde'=>'17:00'];
            foreach($defaults as $k=>$v) {
                $pdo->prepare("INSERT IGNORE INTO config_adm (chave,valor) VALUES (?,?)")->execute([$k,$v]);
            }
            $rows = $this->db->fetchAll("SELECT chave,valor FROM config_adm");
            foreach($rows as $r) $configAdm[$r['chave']] = $r['valor'];
        } catch(\Exception $e) { /* usa defaults */ }

        // Escalas ADM da semana vigente (apenas seg–sex)
        $escalasAdm = [];
        try {
            $escalasAdm = $this->db->fetchAll(
                "SELECT e.*,u.nome FROM escalas e JOIN usuarios u ON u.id=e.agente_id WHERE e.turno='administrativo' AND e.data BETWEEN ? AND ? ORDER BY e.data,u.nome",
                [$seg,$dom]
            );
        } catch(\Exception $e) { $escalasAdm = []; }

        $this->view('escalas/index', compact('escalas','semana','seg','dom','viaturas','agentes','equipes','configAdm','escalasAdm'), 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('escala.criar');
        $this->verifyCsrf();
        $aId = $_POST['agente_id'] ?? Auth::id();
        $entrada = !empty($_POST['entrada']) ? $_POST['entrada'] : null;
        $saida = !empty($_POST['saida']) ? $_POST['saida'] : null;
        $data = $_POST['data'];
        $turno = $_POST['turno'];
        $setor = $_POST['setor'] ?? '';
        $obs = $_POST['observacoes'] ?? '';
        $viatura_id = $_POST['viatura_id'] ?: null;
        $criadoPor = Auth::id();

        // Se o valor de agente_id for algo como "equipe_alfa", faremos um insert em lote (Avulsa p/ todos)
        if(strpos($aId, 'equipe_') === 0) {
            $nEquipe = str_replace('equipe_', '', $aId);
            $agentesNaEquipe = $this->db->fetchAll("SELECT id FROM usuarios WHERE equipe=? AND ativo=1", [$nEquipe]);
            
            if(empty($agentesNaEquipe)) {
                $this->flash('error', "A equipe ".strtoupper($nEquipe)." não tem agentes.");
                $this->redirect('escalas');
            }
            
            $inseridos = 0;
            foreach($agentesNaEquipe as $ag) {
                // Checar se já existe para este dia
                $chk = $this->db->fetchOne("SELECT id FROM escalas WHERE agente_id=? AND data=?", [$ag['id'], $data]);
                if(!$chk) {
                    $this->db->execute(
                        "INSERT INTO escalas (agente_id,viatura_id,data,entrada,saida,turno,setor,observacoes,criado_por) VALUES (?,?,?,?,?,?,?,?,?)",
                        [$ag['id'], $viatura_id, $data, $entrada, $saida, $turno, $setor, $obs, $criadoPor]
                    );
                    $inserted++;
                }
            }
            AuditLogger::log('CREATE', 'escalas', 0, null, ['acao'=>'escala_avulsa_equipe', 'equipe'=>$nEquipe, 'inseridos'=>$inseridos, 'data'=>$data]);
            $this->flash('success', "Escala adicionada para $inseridos membro(s) da Equipe ".strtoupper($nEquipe)."!");
        
        } else {
            // Inserção normal individual
            $this->db->execute(
                "INSERT INTO escalas (agente_id,viatura_id,data,entrada,saida,turno,setor,observacoes,criado_por) VALUES (?,?,?,?,?,?,?,?,?)",
                [$aId, $viatura_id, $data, $entrada, $saida, $turno, $setor, $obs, $criadoPor]
            );
            AuditLogger::log('CREATE','escalas',(int)$this->db->lastInsertId(),null,['agente'=>$aId,'data'=>$data]);
            $this->flash('success','Escala individual adicionada!');
        }
        
        $this->redirect('escalas');
    }

    public function atualizar(string $id): void {
        Auth::requirePermission('escala.editar');
        $this->verifyCsrf();
        
        $agenteId     = !empty($_POST['agente_id']) ? $_POST['agente_id'] : null;
        $data         = !empty($_POST['data']) ? $_POST['data'] : null;
        $entrada      = !empty($_POST['entrada']) ? $_POST['entrada'] : null;
        $saida        = !empty($_POST['saida']) ? $_POST['saida'] : null;
        $entradaTarde = !empty($_POST['entrada_tarde']) ? $_POST['entrada_tarde'] : null;
        $saidaTarde   = !empty($_POST['saida_tarde']) ? $_POST['saida_tarde'] : null;
        $turno        = $_POST['turno'] ?? 'diurno';
        
        $this->db->execute("UPDATE escalas SET agente_id=?, data=?, entrada=?, saida=?, entrada_tarde=?, saida_tarde=?, turno=? WHERE id=?", 
            [$agenteId, $data, $entrada, $saida, $entradaTarde, $saidaTarde, $turno, $id]);
            
        AuditLogger::log('UPDATE','escalas',(int)$id,null,[
            'novo_agente'=>$agenteId,
            'nova_data'=>$data,
            'nova_entrada'=>$entrada,
            'nova_saida'=>$saida,
            'nova_entrada_tarde'=>$entradaTarde,
            'nova_saida_tarde'=>$saidaTarde,
            'novo_turno'=>$turno
        ]);
        
        $this->flash('success','Plantão atualizado com sucesso!');
        $this->redirect('escalas');
    }

    public function excluir(string $id): void {
        Auth::requirePermission('escala.editar');
        $this->verifyCsrf();
        
        $this->db->execute("DELETE FROM escalas WHERE id=?", [$id]);
        AuditLogger::log('DELETE','escalas',(int)$id,null,['acao'=>'excluir_plantao_individual']);
        
        $this->flash('success','Plantão excluído permanentemente!');
        $this->redirect('escalas');
    }

    public function zerar12x36(): void {
        Auth::requirePermission('escala.excluir');
        $this->verifyCsrf();
        
        $row = $this->db->fetchOne("SELECT COUNT(*) as c FROM escalas WHERE turno != 'administrativo'");
        $count = $row ? $row['c'] : 0;
        
        $this->db->execute("DELETE FROM escalas WHERE turno != 'administrativo'");
        AuditLogger::log('DELETE','escalas',0,null,['acao'=>'zerar_escala_12x36','qtde'=>$count]);
        
        $this->flash('success',"Pronto! TODAS as $count escalas 12x36 foram deletadas do sistema.");
        $this->redirect('escalas');
    }

    public function zerarAdm(): void {
        Auth::requirePermission('escala.excluir');
        $this->verifyCsrf();
        
        $row = $this->db->fetchOne("SELECT COUNT(*) as c FROM escalas WHERE turno = 'administrativo'");
        $count = $row ? $row['c'] : 0;
        
        $this->db->execute("DELETE FROM escalas WHERE turno = 'administrativo'");
        AuditLogger::log('DELETE','escalas',0,null,['acao'=>'zerar_escala_adm','qtde'=>$count]);
        
        $this->flash('success',"Pronto! TODAS as $count escalas ADM foram deletadas do sistema.");
        $this->redirect('escalas');
    }

    public function salvarEquipe(): void {
        Auth::requirePermission('escala.criar');
        $this->verifyCsrf();
        $agenteId = $_POST['agente_id'] ?? null;
        $equipe   = $_POST['equipe'] ?? '';
        
        if(!$agenteId) { $this->flash('error','Agente inválido.'); $this->redirect('escalas'); }
        
        $novoValor = in_array($equipe, ['alfa','bravo','charlie','delta','adm']) ? $equipe : null;
        
        $aff = $this->db->execute("UPDATE usuarios SET equipe=? WHERE id=?", [$novoValor, $agenteId]);
        
        // --- LOG DIAGNÓSTICO ---
        $log = "\n[" . date('Y-m-d H:i:s') . "] salvarEquipe: Agente $agenteId -> Equipe $equipe (novoValor: " . ($novoValor ?: 'NULL') . "). Affected rows: $aff";
        @file_put_contents(ROOT_PATH . '/debug_escalas.txt', $log, FILE_APPEND);
        // -----------------------

        AuditLogger::log('UPDATE','usuarios',(int)$agenteId,null,['nova_equipe'=>$novoValor]);
        
        $this->flash('success', 'Equipe do agente atualizada com sucesso!');
        $this->redirect('escalas');
    }

    public function debugEquipes() {
        echo "<pre>";
        echo "Contagem de usuarios ativos: " . $this->db->fetchOne("SELECT COUNT(*) as c FROM usuarios WHERE ativo=1")['c'] . "\n\n";
        $usuarios = $this->db->fetchAll("SELECT id, username, equipe FROM usuarios WHERE ativo=1");
        foreach($usuarios as $u) {
            echo "ID {$u['id']} | User: {$u['username']} | Equipe: '{$u['equipe']}'\n";
        }
        echo "</pre>";
        exit;
    }

    public function gerarEscala12x36(): void {
        Auth::requirePermission('escala.criar');
        $this->verifyCsrf();
        
        $equipe = $_POST['equipe'] ?? '';
        if(!in_array($equipe, ['alfa','bravo','charlie','delta'])) {
            $this->flash('error','Equipe inválida.');
            $this->redirect('escalas');
        }

        $dtIni = $_POST['data_inicial'] ?? '';
        $dtFim = $_POST['data_final'] ?? '';
        
        // Alfa e Charlie são diurnas, Bravo e Delta são noturnas
        $turno = in_array($equipe, ['alfa','charlie']) ? 'diurno' : 'noturno';
        $entrada = !empty($_POST['entrada']) ? $_POST['entrada'] : null;
        $saida = !empty($_POST['saida']) ? $_POST['saida'] : null;

        if(!$dtIni || !$dtFim || $dtIni > $dtFim) {
            $this->flash('error','Período de datas inválido.');
            $this->redirect('escalas');
        }

        $agentes = $this->db->fetchAll("SELECT id FROM usuarios WHERE equipe=? AND ativo=1", [$equipe]);
        
        // --- LOG DIAGNÓSTICO ---
        $log = "\n[" . date('Y-m-d H:i:s') . "] gerarEscala12x36: Verificando equipe $equipe. Agentes encontrados: " . count($agentes);
        @file_put_contents(ROOT_PATH . '/debug_escalas.txt', $log, FILE_APPEND);
        // -----------------------

        if(empty($agentes)) {
            $this->flash('error',"A equipe ".strtoupper($equipe)." não tem agentes associados.");
            $this->redirect('escalas');
        }

        $inicio = new DateTime($dtIni);
        $fim = new DateTime($dtFim);
        $intervalo = new DateInterval('P2D'); // +48h (trabalha 1, folga 1)
        
        $fim->modify('+1 day'); // Incluir o último dia
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        $inserted = 0;
        foreach($periodo as $dt) {
            $dataStr = $dt->format('Y-m-d');
            foreach($agentes as $a) {
                // Checar se já existe escala pra esse agente neste dia parar evitar duplicação cega
                $chk = $this->db->fetchOne("SELECT id FROM escalas WHERE agente_id=? AND data=?", [$a['id'], $dataStr]);
                if(!$chk) {
                    $this->db->execute(
                        "INSERT INTO escalas (agente_id,data,entrada,saida,turno,criado_por) VALUES (?,?,?,?,?,?)",
                        [$a['id'], $dataStr, $entrada, $saida, $turno, Auth::id()]
                    );
                    $inserted++;
                }
            }
        }

        AuditLogger::log('CREATE','escalas',0,null,['acao'=>'geracao_lote_12x36','equipe'=>$equipe,'registros'=>$inserted]);
        $this->flash('success', "Escala 12x36 gerada! $inserted plantões inseridos com sucesso.");
        $this->redirect('escalas');
    }

    public function salvarConfigAdm(): void {
        Auth::requirePermission('escala.criar');
        $this->verifyCsrf();
        $campos = ['entrada_manha','saida_manha','entrada_tarde','saida_tarde'];
        try {
            $pdo = $this->db->getConnection();
            $pdo->exec("CREATE TABLE IF NOT EXISTS config_adm (chave VARCHAR(50) PRIMARY KEY, valor VARCHAR(20) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            foreach($campos as $c) {
                if(!empty($_POST[$c])) {
                    $pdo->prepare("INSERT INTO config_adm (chave,valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor=?")->execute([$c,$_POST[$c],$_POST[$c]]);
                }
            }
            $this->flash('success','Horários do ADM atualizados com sucesso!');
        } catch(\Exception $e) {
            $this->flash('error','Erro ao salvar configuração ADM.');
        }
        $this->redirect('escalas');
    }

    public function gerarEscalaAdm(): void {
        Auth::requirePermission('escala.criar');
        $this->verifyCsrf();

        $dtIni = $_POST['data_inicial'] ?? '';
        $dtFim = $_POST['data_final'] ?? '';
        $entradaManha  = !empty($_POST['entrada_manha'])  ? $_POST['entrada_manha']  : '07:30';
        $saidaManha    = !empty($_POST['saida_manha'])    ? $_POST['saida_manha']    : '11:30';
        $entradaTarde  = !empty($_POST['entrada_tarde'])  ? $_POST['entrada_tarde']  : '13:00';
        $saidaTarde    = !empty($_POST['saida_tarde'])    ? $_POST['saida_tarde']    : '17:00';

        if(!$dtIni || !$dtFim || $dtIni > $dtFim) {
            $this->flash('error','Período de datas inválido.');
            $this->redirect('escalas'); return;
        }

        $agentes = $this->db->fetchAll("SELECT id FROM usuarios WHERE equipe='adm' AND ativo=1");
        if(empty($agentes)) {
            $this->flash('error','A equipe ADM não tem agentes associados. Vincule operadores primeiro.');
            $this->redirect('escalas'); return;
        }

        $inicio = new DateTime($dtIni);
        $fim    = new DateTime($dtFim);
        $fim->modify('+1 day');
        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        $inserted = 0;
        foreach($periodo as $dt) {
            $dow = (int)$dt->format('N'); // 1=Segunda … 7=Domingo
            if($dow >= 6) continue; // pula sábado e domingo
            $dataStr = $dt->format('Y-m-d');
            foreach($agentes as $a) {
                $chk = $this->db->fetchOne("SELECT id FROM escalas WHERE agente_id=? AND data=? AND turno='administrativo'", [$a['id'],$dataStr]);
                if(!$chk) {
                    $this->db->execute(
                        "INSERT INTO escalas (agente_id,data,entrada,saida,entrada_tarde,saida_tarde,turno,criado_por) VALUES (?,?,?,?,?,?,?,?)",
                        [$a['id'],$dataStr,$entradaManha,$saidaManha,$entradaTarde,$saidaTarde,'administrativo',Auth::id()]
                    );
                    $inserted++;
                }
            }
        }

        AuditLogger::log('CREATE','escalas',0,null,['acao'=>'geracao_lote_adm','registros'=>$inserted]);
        $this->flash('success',"Escala ADM gerada! $inserted registros inseridos (dias úteis).");
        $this->redirect('escalas');
    }

    public function trocas(): void {
        // SELF-HEALING DATABASE MIGRATION
        try {
            $this->db->fetchOne("SELECT escala_origem_id FROM trocas_plantao LIMIT 1");
        } catch (\Exception $e) {
            $pdo = $this->db->getConnection();
            $pdo->exec("DROP TABLE IF EXISTS trocas_plantao");
            $pdo->exec("CREATE TABLE trocas_plantao (
                id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                escala_origem_id  INT UNSIGNED NOT NULL,
                escala_destino_id INT UNSIGNED NOT NULL,
                solicitante_id    INT UNSIGNED NOT NULL,
                substituto_id     INT UNSIGNED NOT NULL,
                status            ENUM('pendente','aprovada','negada') DEFAULT 'pendente',
                motivo            TEXT,
                resposta          TEXT,
                aprovado_por      INT UNSIGNED,
                aprovado_em       DATETIME,
                criado_em         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (escala_origem_id)  REFERENCES escalas(id) ON DELETE CASCADE,
                FOREIGN KEY (escala_destino_id) REFERENCES escalas(id) ON DELETE CASCADE,
                FOREIGN KEY (solicitante_id)    REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (substituto_id)     REFERENCES usuarios(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        Auth::requireAuth();
        $userId = Auth::id();
        $ativas = Auth::can('troca.aprovar')
            ? $this->db->fetchAll("SELECT tp.*, eo.data as data_origem, eo.turno as turno_origem, ed.data as data_destino, ed.turno as turno_destino, us.nome as solicitante, ut.nome as substituto FROM trocas_plantao tp JOIN escalas eo ON eo.id=tp.escala_origem_id JOIN escalas ed ON ed.id=tp.escala_destino_id JOIN usuarios us ON us.id=tp.solicitante_id JOIN usuarios ut ON ut.id=tp.substituto_id ORDER BY tp.criado_em DESC LIMIT 50")
            : $this->db->fetchAll("SELECT tp.*, eo.data as data_origem, eo.turno as turno_origem, ed.data as data_destino, ed.turno as turno_destino, us.nome as solicitante, ut.nome as substituto FROM trocas_plantao tp JOIN escalas eo ON eo.id=tp.escala_origem_id JOIN escalas ed ON ed.id=tp.escala_destino_id JOIN usuarios us ON us.id=tp.solicitante_id JOIN usuarios ut ON ut.id=tp.substituto_id WHERE tp.solicitante_id=? OR tp.substituto_id=? ORDER BY tp.criado_em DESC",[$userId,$userId]);
        $minhasEscalas = $this->db->fetchAll("SELECT id,data,turno FROM escalas WHERE agente_id=? AND data >= CURDATE() ORDER BY data LIMIT 20",[$userId]);
        $agentes = $this->db->fetchAll("SELECT id,nome FROM usuarios WHERE ativo=1 AND id<>? ORDER BY nome",[$userId]);
        
        $escalasColegas = $this->db->fetchAll("SELECT id, agente_id, data, turno FROM escalas WHERE agente_id<>? AND data >= CURDATE() ORDER BY data", [$userId]);
        $escalasAgrupadas = [];
        foreach($escalasColegas as $e) { $escalasAgrupadas[$e['agente_id']][] = $e; }
        $jsonEscalas = json_encode($escalasAgrupadas);

        $this->view('escalas/trocas', compact('ativas','minhasEscalas','agentes','jsonEscalas'), 'main');
    }

    public function solicitarTroca(): void {
        Auth::requirePermission('troca.solicitar');
        $this->verifyCsrf();
        $origemId = $_POST['escala_origem_id'] ?? 0;
        $destinoId = $_POST['escala_destino_id'] ?? 0;
        $substitutoId = $_POST['substituto_id'] ?? 0;
        $motivo = $_POST['motivo'] ?? '';

        $origem = $this->db->fetchOne("SELECT turno, agente_id FROM escalas WHERE id=?", [$origemId]);
        $destino = $this->db->fetchOne("SELECT turno, agente_id FROM escalas WHERE id=?", [$destinoId]);

        if (!$origem || !$destino) {
            $this->flash('error', 'Uma das escalas selecionadas é inválida.');
            $this->redirect('trocas'); return;
        }
        if ($origem['agente_id'] != Auth::id()) {
            $this->flash('error', 'A escala de origem informada não pertence a você.');
            $this->redirect('trocas'); return;
        }
        if ($destino['agente_id'] != $substitutoId) {
            $this->flash('error', 'A escala de destino não pertence ao substituto selecionado.');
            $this->redirect('trocas'); return;
        }
        if ($origem['turno'] !== $destino['turno']) {
            $this->flash('error', 'Apenas é permitido trocar Diurno por Diurno, ou Noturno por Noturno.');
            $this->redirect('trocas'); return;
        }

        $this->db->execute(
            "INSERT INTO trocas_plantao (escala_origem_id,escala_destino_id,solicitante_id,substituto_id,motivo) VALUES (?,?,?,?,?)",
            [$origemId, $destinoId, Auth::id(), $substitutoId, $motivo]
        );
        $this->flash('success','Solicitação de troca enviada para aprovação!');
        $this->redirect('trocas');
    }

    public function aprovarTroca(string $id): void {
        Auth::requirePermission('troca.aprovar');
        $this->verifyCsrf();
        $decisao = $_POST['decisao'] ?? 'aprovada';
        // Coluna 'resposta' não existe — usando 'motivo' como campo de resposta
        $this->db->execute(
            "UPDATE trocas_plantao SET status=?,aprovado_por=?,aprovado_em=NOW() WHERE id=?",
            [$decisao,Auth::id(),$id]
        );
        if ($decisao === 'aprovada') {
            $troca = $this->db->fetchOne("SELECT * FROM trocas_plantao WHERE id=?",[$id]);
            if ($troca) {
                $this->db->execute("UPDATE escalas SET agente_id=? WHERE id=?",[$troca['substituto_id'],$troca['escala_origem_id']]);
                $this->db->execute("UPDATE escalas SET agente_id=? WHERE id=?",[$troca['solicitante_id'],$troca['escala_destino_id']]);
            }
        }
        $this->flash('success','Troca '.($decisao==='aprovada'?'aprovada':'negada').'!');
        $this->redirect('trocas');
    }

    public function pdf(): void {
        Auth::requireAuth();
        $semana = $_GET['semana'] ?? date('Y-W');
        [$ano,$sem] = explode('-',$semana) + [date('Y'),date('W')];
        $seg = (new DateTime())->setISODate((int)$ano,(int)$sem)->format('Y-m-d');
        $dom = (new DateTime())->setISODate((int)$ano,(int)$sem,7)->format('Y-m-d');

        $escalas = $this->db->fetchAll(
            "SELECT e.*,u.nome,v.prefixo FROM escalas e JOIN usuarios u ON u.id=e.agente_id LEFT JOIN viaturas v ON v.id=e.viatura_id WHERE e.data BETWEEN ? AND ? AND e.turno <> 'administrativo' ORDER BY e.data,u.nome",
            [$seg,$dom]
        );

        $escalasAdm = [];
        try {
            $escalasAdm = $this->db->fetchAll(
                "SELECT e.*,u.nome FROM escalas e JOIN usuarios u ON u.id=e.agente_id WHERE e.turno='administrativo' AND e.data BETWEEN ? AND ? ORDER BY e.data,u.nome",
                [$seg,$dom]
            );
        } catch(\Exception $e) { $escalasAdm = []; }

        $this->view('escalas/pdf', compact('escalas','semana','seg','dom','escalasAdm'), 'pdf_layout');
    }

}
