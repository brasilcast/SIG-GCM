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
 */class TaloesController extends Controller {

    public function index(): void {
        Auth::requireAuth();
        $busca   = $_GET['q'] ?? '';
        $status  = $_GET['status'] ?? '';
        $nat     = $_GET['natureza'] ?? '';
        $data    = $_GET['data'] ?? '';
        $page    = max(1, (int)($_GET['page'] ?? 1));

        // Filtro base de exibição padrão: Mostrar talões abertos ou fechados nas últimas 24h para TODOS os usuários
        $where  = ['(t.status = "aberto" OR (t.status = "fechado" AND t.data_hora_fechamento >= DATE_SUB(NOW(), INTERVAL 24 HOUR)))'];
        $params = [];

        // Ignorando restrição `!Auth::can('taloes.ver_todos')` pois o usuário quer que TODOS vejam os recentes

        if ($busca)  { $where[] = '(t.numero LIKE ? OR t.narrativa LIKE ? OR t.bairro LIKE ?)'; $params = array_merge($params, ["%$busca%","%$busca%","%$busca%"]); }
        if ($status) { $where[] = 't.status = ?'; $params[] = $status; }
        if ($nat)    { $where[] = 't.natureza_id = ?'; $params[] = $nat; }
        if ($data)   { $where[] = 'DATE(t.data_hora_abertura) = ?'; $params[] = $data; }

        $whereStr = implode(' AND ', $where);
        $total = $this->db->fetchOne("SELECT COUNT(DISTINCT t.id) as n FROM taloes t LEFT JOIN taloes_agentes ta ON ta.talao_id = t.id WHERE $whereStr", $params)['n'];
        $pag   = $this->paginate($total, $page);

        $params2   = $params;
        $params2[] = $pag['per_page'];
        $params2[] = $pag['offset'];

        $taloes = $this->db->fetchAll(
            "SELECT DISTINCT t.*, u.nome as agente_nome, n.nome as natureza_nome, n.codigo as natureza_codigo, v.prefixo as viatura_prefixo
             FROM taloes t
             LEFT JOIN usuarios u ON u.id = t.agente_id
             LEFT JOIN naturezas_ocorrencia n ON n.id = t.natureza_id
             LEFT JOIN viaturas v ON v.id = t.viatura_id
             LEFT JOIN taloes_agentes ta ON ta.talao_id = t.id
             WHERE $whereStr
             ORDER BY t.data_hora_abertura DESC
             LIMIT ? OFFSET ?",
            $params2
        );

        // Buscar todos os talões abertos para os cards fixos no topo, visíveis para TODOS
        $whereAbertos = ['t.status = "aberto"'];
        $paramsAbertos = [];
        // Ignorando restrição `!Auth::can('taloes.ver_todos')` pois o usuário quer que TODOS vejam
        $whereStrAbertos = implode(' AND ', $whereAbertos);
        $taloesAbertos = $this->db->fetchAll(
            "SELECT DISTINCT t.*, u.nome as agente_nome, n.nome as natureza_nome, n.codigo as natureza_codigo, v.prefixo as viatura_prefixo
             FROM taloes t
             LEFT JOIN usuarios u ON u.id = t.agente_id
             LEFT JOIN naturezas_ocorrencia n ON n.id = t.natureza_id
             LEFT JOIN viaturas v ON v.id = t.viatura_id
             LEFT JOIN taloes_agentes ta ON ta.talao_id = t.id
             WHERE $whereStrAbertos
             ORDER BY t.data_hora_abertura DESC",
            $paramsAbertos
        );

        // naturezas_ocorrencia usa coluna 'ativo' ou 'ativa' dependendo da versão, vamos ignorar a flag para garantir funcionamento e só ordenar
        $naturezas = $this->db->fetchAll("SELECT id, nome, codigo FROM naturezas_ocorrencia ORDER BY grupo, codigo");
        $this->view('taloes/index', compact('taloes','taloesAbertos','busca','status','nat','data','naturezas','pag'), 'main');
    }

    public function novo(): void {
        Auth::requirePermission('taloes.criar');
        $naturezas   = $this->db->fetchAll("SELECT id, grupo, nome, codigo FROM naturezas_ocorrencia ORDER BY grupo, codigo");
        $viaturas    = $this->db->fetchAll("SELECT id,prefixo,placa FROM viaturas WHERE status != 'inativa' AND ativa=1 ORDER BY prefixo");
        $agentes     = $this->db->fetchAll("SELECT id,nome,matricula FROM usuarios WHERE ativo=1 ORDER BY nome");
        
        // Calcular o número sugerido do dia
        $hoje = date('Y-m-d');
        // Usar LIKE '___' garantindo que só estamos pegando os formatos numéricos puros (tipo 01)
        // Se a gente quiser suportar outros formatos, a lógica seria um pouco mais complexa
        // Vou pegar o maior número registrado na data de hoje
        $ultimo_numero_do_dia = $this->db->fetchOne("SELECT numero FROM taloes WHERE DATE(data_hora_abertura) = ? ORDER BY id DESC LIMIT 1", [$hoje]);
        
        if ($ultimo_numero_do_dia && preg_match('/^(\d+)$/', $ultimo_numero_do_dia['numero'], $matches)) {
            $numInteiro = (int)$matches[1];
            $proxNumInteiro = $numInteiro + 1;
            $numSugerido = str_pad($proxNumInteiro, 2, '0', STR_PAD_LEFT);
        } else {
            // Primeiro talão do dia (ou o último tinha um formato de texto imprevisível)
            $numSugerido = '01';
        }
        
        $this->view('taloes/form', compact('naturezas','viaturas','agentes','numSugerido') + ['talao'=>null,'titulo'=>'Novo Talão','agsVinc'=>[]], 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('taloes.criar');
        $this->verifyCsrf();

        $numero = trim($_POST['numero'] ?? '');
        if (!$numero) { $this->flash('error','Número do talão é obrigatório.'); $this->redirect('taloes/novo'); }

        // Removemos a verificação do UNIQUE:
        // $duplo = $this->db->fetchOne("SELECT id FROM taloes WHERE numero=?",[$numero]);
        // Se quiser evitar duplicações estritas no MESMO dia, poderíamos verificar aqui:
        // $duploNoMesmoDia = $this->db->fetchOne("SELECT id FROM taloes WHERE numero=? AND DATE(data_hora_abertura) = DATE(?)", [$numero, $_POST['data_hora_abertura'] ?? date('Y-m-d H:i:s')]);
        // if ($duploNoMesmoDia) { $this->flash('error',"Número '$numero' já existe neste dia."); $this->redirect('taloes/novo'); }

        $this->db->execute(
            "INSERT INTO taloes (numero,bogm,bopc,tipo_equipe,status,natureza_id,viatura_id,agente_id,data_hora_abertura,km_inicial,latitude,longitude,endereco,bairro,referencia,narrativa,encaminhamento,medidas_tomadas,observacoes_internas,criado_por)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $numero,
                $_POST['bogm'] ?? '',
                $_POST['bopc'] ?? '',
                $_POST['tipo_equipe'] ?? 'viatura',
                $_POST['status'] ?? 'aberto',
                !empty($_POST['natureza_id']) ? $_POST['natureza_id'] : null,
                !empty($_POST['viatura_id'])  ? $_POST['viatura_id']  : null,
                !empty($_POST['agente_id'])   ? $_POST['agente_id']   : Auth::id(),
                $_POST['data_hora_abertura'] ?? date('Y-m-d H:i:s'),
                $_POST['km_inicial'] ?: null,
                $_POST['latitude']   ?: null,
                $_POST['longitude']  ?: null,
                $_POST['endereco']   ?? '',
                $_POST['bairro']     ?? '',
                $_POST['referencia'] ?? '',
                $_POST['narrativa']  ?? '',
                $_POST['encaminhamento'] ?? '',
                $_POST['medidas_tomadas'] ?? '',
                $_POST['observacoes_internas'] ?? '',
                Auth::id(),
            ]
        );
        $id = $this->db->lastInsertId();

        if (!empty($_POST['agentes_adicionais'])) {
            foreach ($_POST['agentes_adicionais'] as $aid) {
                if ((int)$aid !== (int)($_POST['agente_id'] ?? 0)) {
                    $this->db->execute("INSERT IGNORE INTO taloes_agentes (talao_id,agente_id) VALUES (?,?)",[$id,$aid]);
                }
            }
        }

        if (!empty($_FILES['fotos']['name'][0])) {
            $this->processarFotos((int)$id, $_FILES['fotos']);
        }

        AuditLogger::create('taloes', (int)$id, ['numero'=>$numero,'criado_por'=>Auth::id()]);

        // Auto-ronda escolar se natureza for Z06 (EM ESCOLA)
        $nat = $this->db->fetchOne("SELECT codigo FROM naturezas_ocorrencia WHERE id=?", [$_POST['natureza_id'] ?: null]);
        if ($nat && $nat['codigo'] === 'Z06') {
            $this->registrarRondaAutomatica(
                (int)$id,
                trim($_POST['endereco'] ?? ''),
                trim($_POST['bairro'] ?? ''),
                $_POST['latitude'] ?: null,
                $_POST['longitude'] ?: null,
                $_POST['viatura_id'] ?: null,
                (int)($_POST['agente_id'] ?: Auth::id()),
                $_POST['data_hora_abertura'] ?? date('Y-m-d H:i:s')
            );
        }

        $this->flash('success',"Talão #$numero registrado com sucesso!");
        $this->redirect('taloes');
    }

    public function ver(string $id): void {
        Auth::requireAuth();
        $talao        = $this->buscarOu404((int)$id);
        $agentes      = $this->db->fetchAll("SELECT u.nome FROM taloes_agentes ta JOIN usuarios u ON u.id=ta.agente_id WHERE ta.talao_id=?",[$id]);
        $envolvidos   = $this->db->fetchAll("SELECT * FROM taloes_envolvidos WHERE talao_id=? ORDER BY tipo",[$id]);
        $fotos        = $this->db->fetchAll("SELECT * FROM taloes_fotos WHERE talao_id=? ORDER BY criado_em",[$id]);
        $atualizacoes = $this->db->fetchAll("SELECT ta.*,u.nome as autor FROM taloes_atualizacoes ta LEFT JOIN usuarios u ON u.id=ta.agente_id WHERE ta.talao_id=? ORDER BY ta.criado_em DESC",[$id]);
        AuditLogger::view('taloes',(int)$id);
        $this->view('taloes/ver', compact('talao','agentes','envolvidos','fotos','atualizacoes'), 'main');
    }

    public function editar(string $id): void {
        Auth::requireAuth();
        $talao = $this->buscarOu404((int)$id);
        if (!Auth::can('taloes.editar_todos') && (int)$talao['criado_por'] !== Auth::id()) {
            $this->flash('error','Sem permissão para editar este talão.');
            $this->redirect("taloes/$id");
        }
        $naturezas = $this->db->fetchAll("SELECT id, grupo, nome, codigo FROM naturezas_ocorrencia ORDER BY grupo, codigo");
        $viaturas  = $this->db->fetchAll("SELECT id,prefixo,placa FROM viaturas WHERE ativa=1 ORDER BY prefixo");
        $agentes   = $this->db->fetchAll("SELECT id,nome,matricula FROM usuarios WHERE ativo=1 ORDER BY nome");
        $agsVinc   = array_column($this->db->fetchAll("SELECT agente_id FROM taloes_agentes WHERE talao_id=?",[$id]),'agente_id');
        
        // Em caso de edição, passamos a natureza_grupo baseada no natureza_id
        if (!empty($talao['natureza_id'])) {
            $grp = $this->db->fetchOne("SELECT grupo FROM naturezas_ocorrencia WHERE id=?", [$talao['natureza_id']]);
            if ($grp) {
                $talao['natureza_grupo'] = $grp['grupo'];
            }
        }
        
        $this->view('taloes/form', compact('talao','naturezas','viaturas','agentes','agsVinc') + ['titulo'=>'Editar Talão','numSugerido'=>$talao['numero']], 'main');
    }

    public function atualizar(string $id): void {
        Auth::requireAuth();
        $this->verifyCsrf();
        $talao = $this->buscarOu404((int)$id);
        if (!Auth::can('taloes.editar_todos') && (int)$talao['criado_por'] !== Auth::id()) {
            $this->flash('error','Sem permissão.');
            $this->redirect("taloes/$id");
        }
        $antes = $talao;
        $this->db->execute(
            "UPDATE taloes SET numero=?, bogm=?, bopc=?, tipo_equipe=?, natureza_id=?,viatura_id=?,agente_id=?,data_hora_abertura=?,data_hora_fechamento=?,km_inicial=?,km_final=?,latitude=?,longitude=?,endereco=?,bairro=?,referencia=?,narrativa=?,encaminhamento=?,medidas_tomadas=?,observacoes_internas=?,updated_at=NOW() WHERE id=?",
            [
                $_POST['numero'] ?: $talao['numero'], $_POST['bogm']??'', $_POST['bopc']??'', $_POST['tipo_equipe']??'viatura',
                !empty($_POST['natureza_id']) ? $_POST['natureza_id'] : null, 
                !empty($_POST['viatura_id'])  ? $_POST['viatura_id']  : null, 
                !empty($_POST['agente_id'])   ? $_POST['agente_id']   : null,
                $_POST['data_hora_abertura'] ?? $talao['data_hora_abertura'], $_POST['data_hora_fechamento'] ?: null,
                $_POST['km_inicial'] ?: null, $_POST['km_final'] ?: null,
                $_POST['latitude'] ?: null, $_POST['longitude'] ?: null,
                $_POST['endereco']??'', $_POST['bairro']??'', $_POST['referencia']??'',
                $_POST['narrativa']??'', $_POST['encaminhamento']??'', $_POST['medidas_tomadas']??'',
                $_POST['observacoes_internas']??'', $id
            ]
        );

        if (!empty($_FILES['fotos']['name'][0])) {
            $this->processarFotos((int)$id, $_FILES['fotos']);
        }

        AuditLogger::update('taloes',(int)$id,$antes,$_POST);

        // Auto-ronda escolar se natureza for Z06 (EM ESCOLA)
        $nat = $this->db->fetchOne("SELECT codigo FROM naturezas_ocorrencia WHERE id=?", [$_POST['natureza_id'] ?? null]);
        if ($nat && $nat['codigo'] === 'Z06') {
            $this->registrarRondaAutomatica(
                (int)$id,
                trim($_POST['endereco'] ?? ''),
                trim($_POST['bairro'] ?? ''),
                $_POST['latitude'] ?? null,
                $_POST['longitude'] ?? null,
                $_POST['viatura_id'] ?? null,
                (int)($_POST['agente_id'] ?? Auth::id()),
                $_POST['data_hora_abertura'] ?? date('Y-m-d H:i:s')
            );
        }

        $this->flash('success','Talão atualizado!');
        $this->redirect("taloes/$id");
    }

    public function atualizarStatus(string $id): void {
        Auth::requireAuth();
        $this->verifyCsrf();
        $talao = $this->buscarOu404((int)$id);
        
        $novoStatus = $_POST['status'] ?? '';
        if (!in_array($novoStatus, ['aberto', 'fechado'])) { 
            $this->back(); 
        }

        $params = [$novoStatus];
        $sqlExtra = "";

        if ($novoStatus === 'fechado') {
            // Captura os dados vindos do Modal (Data, Hora e KM)
            $dtFec = $_POST['data_fechamento'] ?? date('Y-m-d');
            $hrFec = $_POST['hora_fechamento'] ?? date('H:i');
            $dhFechamento = $dtFec . ' ' . $hrFec . ':00';
            
            $sqlExtra .= ", data_hora_fechamento=?";
            $params[] = $dhFechamento;

            // Só tenta salvar km_final se ele vier preenchido e se o talão não for 'pop' (Equipe Viatura)
            if (isset($_POST['km_final']) && $_POST['km_final'] !== '' && ($talao['tipo_equipe'] ?? 'viatura') !== 'pop') {
                $sqlExtra .= ", km_final=?";
                $params[] = (float)$_POST['km_final'];
            }
        } else {
            // Se reabrir, limpa os dados de fechamento
            $sqlExtra .= ", data_hora_fechamento=NULL, km_final=NULL";
        }

        $params[] = $id;
        $this->db->execute("UPDATE taloes SET status=? {$sqlExtra} WHERE id=?", $params);
        
        AuditLogger::update('taloes', (int)$id, $talao, ['status' => $novoStatus]);
        $this->flash('success', 'Status atualizado para: ' . ucfirst($novoStatus));
        $this->redirect('taloes');
    }

    public function addAtualizacao(string $id): void {
        Auth::requireAuth();
        $this->verifyCsrf();
        $this->buscarOu404((int)$id);
        $texto = trim($_POST['texto'] ?? '');
        if (!$texto) { $this->back(); }
        $this->db->execute("INSERT INTO taloes_atualizacoes (talao_id,agente_id,texto) VALUES (?,?,?)",[$id,Auth::id(),$texto]);
        $this->flash('success','Atualização registrada.');
        $this->redirect("taloes/$id");
    }

    public function pdf(string $id): void {
        Auth::requireAuth();
        $talao      = $this->buscarOu404((int)$id);
        $agentes    = $this->db->fetchAll("SELECT u.nome FROM taloes_agentes ta JOIN usuarios u ON u.id=ta.agente_id WHERE ta.talao_id=?",[$id]);
        $envolvidos = $this->db->fetchAll("SELECT * FROM taloes_envolvidos WHERE talao_id=? ORDER BY tipo",[$id]);
        $this->view('taloes/pdf', compact('talao','agentes','envolvidos'), 'pdf_layout');
    }

    public function mapaCalor(): void {
        Auth::requireAuth();
        if (isset($_GET['json'])) {
            $pontos = $this->db->fetchAll(
                "SELECT t.id,t.numero,t.latitude,t.longitude,t.bairro,t.status,t.data_hora_abertura,
                        n.nome as natureza
                 FROM taloes t
                 LEFT JOIN naturezas_ocorrencia n ON n.id = t.natureza_id
                 WHERE t.latitude IS NOT NULL AND t.longitude IS NOT NULL
                 AND t.data_hora_abertura >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                 LIMIT 2000"
            );
            $this->json($pontos);
        }
        $this->view('taloes/mapa_calor', [], 'main');
    }

    // ---- Helpers ----
    private function buscarOu404(int $id): array {
        $t = $this->db->fetchOne(
            "SELECT t.*, u.nome as agente_nome, n.nome as natureza_nome, n.grupo as natureza_grupo, n.codigo as natureza_codigo,
                    v.prefixo as viatura_prefixo, c.nome as criado_por_nome
             FROM taloes t
             LEFT JOIN usuarios u ON u.id=t.agente_id
             LEFT JOIN naturezas_ocorrencia n ON n.id=t.natureza_id
             LEFT JOIN viaturas v ON v.id=t.viatura_id
             LEFT JOIN usuarios c ON c.id=t.criado_por
             WHERE t.id=?",[$id]
        );
        if (!$t) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        return $t;
    }

    /**
     * Registra automaticamente uma ronda escolar quando um talão Z06 é salvo.
     * Faz correspondência fuzzy entre o endereço/bairro do talão e:
     *   (a) o NOME da escola (útil quando operador digita o nome)
     *   (b) o ENDEREÇO da escola (útil quando GPS captura a rua e número)
     */
    private function registrarRondaAutomatica(
        int $talaoId,
        string $endereco,
        string $bairro,
        $lat, $lon, $viaturaId, int $agenteId, string $dataHora
    ): void {
        if (!$endereco) return;

        // Busca todas as escolas ativas — inclui o campo endereco
        $escolas = $this->db->fetchAll("SELECT id, nome, endereco, bairro FROM escolas WHERE ativa=1 ORDER BY nome");
        if (empty($escolas)) return;

        // Normaliza uma string para comparação
        $normalizar = function(string $s): string {
            $s = strtolower($s);
            $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            // Remove prefixos de logradouro
            $s = preg_replace('/\b(rua|av\.?|avenida|rod\.?|rodovia|alameda|al\.?|travessa|tv\.?)\s*/i', '', $s);
            // Remove números de casa/lote
            $s = preg_replace('/\b\d+\b/', ' ', $s);
            $s = preg_replace('/[^a-z\s]/', ' ', $s);
            $s = preg_replace('/\s+/', ' ', trim($s));
            return $s;
        };

        // Normaliza nome de escola removendo prefixos institucionais
        $normalizarNomeEscola = function(string $s) use ($normalizar): string {
            $s = $normalizar($s);
            $s = preg_replace('/\b(escola|municipal|estadual|em|ee|colegio|prof\.?|professor|professora|centro|educacional|de|da|do|dos|das)\b/i', '', $s);
            $s = preg_replace('/\s+/', ' ', trim($s));
            return $s;
        };

        // Calcula score por palavras em comum
        $scoreWordMatch = function(string $haystack, string $needle): float {
            $palavras = array_filter(explode(' ', $needle), fn($p) => strlen($p) >= 3);
            if (empty($palavras)) return 0.0;
            $matches = 0;
            foreach ($palavras as $palavra) {
                if (str_contains($haystack, $palavra)) $matches++;
            }
            return ($matches / count($palavras)) * 100;
        };

        // Texto do talão para comparar
        $enderecoNorm = $normalizar($endereco . ' ' . $bairro);

        $melhorEscola = null;
        $melhorScore  = 0.0;

        foreach ($escolas as $escola) {
            // --- Modo 1: comparar endereço do talão × NOME da escola ---
            $nomeNorm  = $normalizarNomeEscola($escola['nome']);
            $scoreNome = $scoreWordMatch($enderecoNorm, $nomeNorm);

            // Fallback similar_text se nenhuma palavra bateu
            if ($scoreNome === 0.0) {
                similar_text($enderecoNorm, $nomeNorm, $pctNome);
                $scoreNome = (float)$pctNome;
            }

            // --- Modo 2: comparar endereço do talão × ENDEREÇO da escola ---
            $scoreEndereco = 0.0;
            if (!empty($escola['endereco'])) {
                $endEscolaNorm = $normalizar($escola['endereco'] . ' ' . ($escola['bairro'] ?? ''));
                $scoreEndereco = $scoreWordMatch($enderecoNorm, $endEscolaNorm);

                // Se não bateu por palavras, tenta similar_text
                if ($scoreEndereco === 0.0) {
                    similar_text($enderecoNorm, $endEscolaNorm, $pctEnd);
                    $scoreEndereco = (float)$pctEnd;
                }
            }

            // Usa o maior score entre nome e endereço
            $score = max($scoreNome, $scoreEndereco);

            if ($score > $melhorScore) {
                $melhorScore  = $score;
                $melhorEscola = $escola;
            }
        }

        // Só registra se similaridade >= 40%
        if ($melhorEscola && $melhorScore >= 40.0) {
            // Evita duplicata: verifica se já existe ronda desse talão nessa escola
            $existe = $this->db->fetchOne(
                "SELECT id FROM rondas_escolares WHERE escola_id=? AND talao_id=?",
                [$melhorEscola['id'], $talaoId]
            );
            if (!$existe) {
                $modoDeteccao = $melhorScore >= 40 ? 'por endereço' : 'por nome';
                $this->db->execute(
                    "INSERT INTO rondas_escolares (escola_id, agente_id, viatura_id, latitude, longitude, observacoes, realizado_em, talao_id)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $melhorEscola['id'],
                        $agenteId,
                        $viaturaId,
                        $lat,
                        $lon,
                        'Ronda registrada automaticamente via Talão #' . $talaoId . '. Escola identificada: ' . $melhorEscola['nome'] . ' (correspondência: ' . round($melhorScore) . '%)',
                        $dataHora,
                        $talaoId,
                    ]
                );
            }
        }
    }

    private function processarFotos(int $talaoId, array $files): void {
        $uploadDir = UPLOAD_PATH . '/taloes/' . $talaoId . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) continue;
            $novo = uniqid() . '.' . $ext;
            if (move_uploaded_file($files['tmp_name'][$i], $uploadDir . $novo)) {
                $this->db->execute("INSERT INTO taloes_fotos (talao_id,caminho) VALUES (?,?)",[$talaoId,"taloes/$talaoId/$novo"]);
            }
        }
    }
}
