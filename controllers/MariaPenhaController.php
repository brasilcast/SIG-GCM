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
 */class MariaPenhaController extends Controller {

    public function index(): void {
        Auth::requirePermission('maria_penha.ver');
        $busca = $_GET['q'] ?? '';
        $prio  = $_GET['prioridade'] ?? '';
        $where = ['1=1']; $params = [];
        if ($busca) { $where[]="(v.nome LIKE ? OR v.bairro LIKE ?)"; $params=array_merge($params,["%$busca%","%$busca%"]); }
        if ($prio)  { $where[]='v.prioridade = ?'; $params[]=$prio; }
        $vitimas = $this->db->fetchAll(
            "SELECT v.*,
              (SELECT COUNT(*) FROM visitas_penha WHERE vitima_id=v.id) as total_visitas,
              (SELECT MAX(realizado_em) FROM visitas_penha WHERE vitima_id=v.id) as ultima_visita,
              (SELECT COUNT(*) FROM medidas_protetivas WHERE vitima_id=v.id AND status='vigente') as medidas_ativas
             FROM vitimas_penha v WHERE v.ativa=1 AND ".implode(' AND ',$where)."
             ORDER BY v.prioridade,v.nome",
            $params
        );
        $this->view('maria_penha/index', compact('vitimas','busca','prio'), 'main');
    }

    public function nova(): void {
        Auth::requirePermission('maria_penha.cadastrar_vitima');
        $this->view('maria_penha/form', ['vitima'=>null], 'main');
    }

    public function editar(string $id): void {
        Auth::requirePermission('maria_penha.cadastrar_vitima');
        $vitima = $this->db->fetchOne("SELECT * FROM vitimas_penha WHERE id=? AND ativa=1", [$id]);
        if (!$vitima) {
            $this->flash('error', 'Vítima não encontrada.');
            $this->redirect('maria-penha');
        }
        $this->view('maria_penha/form', compact('vitima'), 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('maria_penha.cadastrar_vitima');
        $this->verifyCsrf();
        $id = $_POST['id'] ?? '';
        $nome = trim($_POST['nome'] ?? '');
        if (!$nome) { $this->flash('error','Nome da vítima é obrigatório.'); $this->redirect('maria-penha/nova'); }
        
        if ($id) {
            $this->db->execute(
                "UPDATE vitimas_penha SET nome=?, cpf=?, data_nascimento=?, telefone=?, telefone_emergencia=?, nome_emergencia=?, endereco=?, bairro=?, latitude=?, longitude=?, prioridade=?, nome_agressor=?, cpf_agressor=?, vinculo_agressor=?, historico_violencia=?, observacoes=? WHERE id=?",
                [$nome,$_POST['cpf']??null,$_POST['data_nascimento']?:null,$_POST['telefone']??null,$_POST['telefone_emergencia']??null,$_POST['nome_emergencia']??null,$_POST['endereco']??null,$_POST['bairro']??null,$_POST['latitude']?:null,$_POST['longitude']?:null,$_POST['prioridade']??3,$_POST['nome_agressor']??null,$_POST['cpf_agressor']??null,$_POST['vinculo_agressor']??null,$_POST['historico_violencia']??null,$_POST['observacoes']??null,$id]
            );
            AuditLogger::log('UPDATE','vitimas_penha',(int)$id,null,['nome'=>$nome]);
            $this->flash('success','Dados da vítima atualizados com sucesso!');
        } else {
            $this->db->execute(
                "INSERT INTO vitimas_penha (nome,cpf,data_nascimento,telefone,telefone_emergencia,nome_emergencia,endereco,bairro,latitude,longitude,prioridade,nome_agressor,cpf_agressor,vinculo_agressor,historico_violencia,observacoes,cadastrado_por)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                [$nome,$_POST['cpf']??null,$_POST['data_nascimento']?:null,$_POST['telefone']??null,$_POST['telefone_emergencia']??null,$_POST['nome_emergencia']??null,$_POST['endereco']??null,$_POST['bairro']??null,$_POST['latitude']?:null,$_POST['longitude']?:null,$_POST['prioridade']??3,$_POST['nome_agressor']??null,$_POST['cpf_agressor']??null,$_POST['vinculo_agressor']??null,$_POST['historico_violencia']??null,$_POST['observacoes']??null,Auth::id()]
            );
            $id = $this->db->lastInsertId();
            AuditLogger::create('vitimas_penha',(int)$id,['nome'=>$nome]);
            $this->flash('success','Vítima cadastrada com sucesso!');
        }
        $this->redirect("maria-penha/$id");
    }

    public function excluir(string $id): void {
        Auth::requirePermission('maria_penha.cadastrar_vitima');
        $this->verifyCsrf();
        $this->db->execute("UPDATE vitimas_penha SET ativa=0 WHERE id=?", [$id]);
        AuditLogger::log('DELETE','vitimas_penha',(int)$id,null,['acao'=>'desativacao']);
        $this->flash('success', 'Vítima removida do sistema.');
        $this->redirect('maria-penha');
    }

    public function ver(string $id): void {
        Auth::requirePermission('maria_penha.ver');
        $v = $this->db->fetchOne("SELECT * FROM vitimas_penha WHERE id=?",[$id]);
        if (!$v) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        $visitas = $this->db->fetchAll("SELECT vi.*,u.nome as agente FROM visitas_penha vi JOIN usuarios u ON u.id=vi.agente_id WHERE vi.vitima_id=? ORDER BY vi.realizado_em DESC",[$id]);
        $medidas = $this->db->fetchAll("SELECT * FROM medidas_protetivas WHERE vitima_id=? ORDER BY data_vencimento",[$id]);
        AuditLogger::view('vitimas_penha',(int)$id);
        $this->view('maria_penha/ver', compact('v','visitas','medidas'), 'main');
    }

    public function registrarVisita(string $id): void {
        Auth::requirePermission('maria_penha.visitar');
        $this->verifyCsrf();
        $agora = date('Y-m-d H:i:s');

        $foto = null;
        if (!empty($_FILES['foto']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext,['jpg','jpeg','png','webp'])) {
                $dir = UPLOAD_PATH . "/visitas_penha/";
                if (!is_dir($dir)) mkdir($dir,0755,true);
                $novo = uniqid().".$ext";
                if (move_uploaded_file($_FILES['foto']['tmp_name'],$dir.$novo)) $foto = "visitas_penha/$novo";
            }
        }

        $this->db->execute(
            "INSERT INTO visitas_penha (vitima_id,agente_id,latitude,longitude,situacao,relato,foto,realizado_em) VALUES (?,?,?,?,?,?,?,?)",
            [$id,Auth::id(),$_POST['latitude']?:null,$_POST['longitude']?:null,$_POST['situacao']??'normal',$_POST['relato']??'',$foto,$_POST['realizado_em']??$agora]
        );
        AuditLogger::create('visitas_penha',(int)$this->db->lastInsertId(),['vitima_id'=>$id,'agente_id'=>Auth::id()]);
        $this->flash('success','Visita registrada!');
        $this->redirect("maria-penha/$id");
    }

    public function novaMedida(string $id): void {
        Auth::requirePermission('maria_penha.cadastrar_vitima');
        $this->verifyCsrf();
        if (empty($_POST['data_vencimento'])) { $this->flash('error','Data de vencimento obrigatória.'); $this->redirect("maria-penha/$id"); }
        // Apenas colunas que existem na tabela medidas_protetivas
        $this->db->execute(
            "INSERT INTO medidas_protetivas (vitima_id,tipo,numero_processo,data_inicio,data_vencimento,status,observacoes,criado_por)
             VALUES (?,?,?,?,?,?,?,?)",
            [$id,$_POST['tipo']??'Medida Protetiva',$_POST['numero_processo']??null,$_POST['data_inicio']?:null,$_POST['data_vencimento'],'vigente',$_POST['observacoes']??null,Auth::id()]
        );
        $this->flash('success','Medida protetiva cadastrada!');
        $this->redirect("maria-penha/$id");
    }

    public function mapa(): void {
        Auth::requirePermission('maria_penha.ver');
        if (isset($_GET['json'])) {
            $pontos = $this->db->fetchAll("SELECT id,nome,latitude,longitude,prioridade,bairro FROM vitimas_penha WHERE ativa=1 AND latitude IS NOT NULL AND longitude IS NOT NULL");
            $this->json($pontos);
        }
        $this->view('maria_penha/mapa', [], 'main');
    }
}
