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
 */class RondaEscolarController extends Controller {

    public function index(): void {
        Auth::requireAuth();
        // Operadores só podem registrar — redireciona direto ao formulário
        if (!Auth::can('ronda_escolar.ver_todos')) {
            $this->redirect('ronda-escolar/registrar');
        }
        $escolas = $this->db->fetchAll(
            "SELECT e.*,
              (SELECT COUNT(*) FROM rondas_escolares WHERE escola_id=e.id) as total_rondas,
              (SELECT MAX(realizado_em) FROM rondas_escolares WHERE escola_id=e.id) as ultima_ronda
             FROM escolas e WHERE e.ativa=1 ORDER BY e.nome"
        );
        $this->view('ronda_escolar/index', compact('escolas'), 'main');
    }

    public function registrar(): void {
        Auth::requirePermission('ronda_escolar.registrar');
        $escolas  = $this->db->fetchAll("SELECT id,nome,bairro FROM escolas WHERE ativa=1 ORDER BY nome");
        $viaturas = $this->db->fetchAll("SELECT id,prefixo FROM viaturas WHERE ativa=1 ORDER BY prefixo");
        $this->view('ronda_escolar/form', compact('escolas','viaturas'), 'main');
    }

    public function salvarRegistro(): void {
        Auth::requirePermission('ronda_escolar.registrar');
        $this->verifyCsrf();
        $escolaId = $_POST['escola_id'] ?? null;
        if (!$escolaId) { $this->flash('error','Selecione a escola.'); $this->redirect('ronda-escolar/registrar'); }

        $foto = null;
        if (!empty($_FILES['foto']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext,['jpg','jpeg','png','webp'])) {
                $dir = UPLOAD_PATH . "/rondas/";
                if (!is_dir($dir)) mkdir($dir,0755,true);
                $novo = uniqid().".$ext";
                if (move_uploaded_file($_FILES['foto']['tmp_name'],$dir.$novo)) $foto = "rondas/$novo";
            }
        }

        $this->db->execute(
            "INSERT INTO rondas_escolares (escola_id,agente_id,viatura_id,latitude,longitude,foto,observacoes,realizado_em) VALUES (?,?,?,?,?,?,?,?)",
            [$escolaId,Auth::id(),$_POST['viatura_id']?:null,$_POST['latitude']?:null,$_POST['longitude']?:null,$foto,$_POST['observacoes']??'',date('Y-m-d H:i:s')]
        );
        AuditLogger::log('CREATE','rondas_escolares',null,null,['escola_id'=>$escolaId,'agente_id'=>Auth::id()]);
        $this->flash('success','Ronda registrada com sucesso!');
        $this->redirect('ronda-escolar');
    }

    public function escola(string $id): void {
        Auth::requirePermission('ronda_escolar.ver_todos');
        $escola = $this->db->fetchOne("SELECT * FROM escolas WHERE id=?",[$id]);
        if (!$escola) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        $rondas = $this->db->fetchAll("SELECT r.*,u.nome as agente,v.prefixo FROM rondas_escolares r JOIN usuarios u ON u.id=r.agente_id LEFT JOIN viaturas v ON v.id=r.viatura_id WHERE r.escola_id=? ORDER BY r.realizado_em DESC",[$id]);
        $ocorrencias = $this->db->fetchAll("SELECT o.*,u.nome as agente FROM ocorrencias_escolares o JOIN usuarios u ON u.id=o.agente_id WHERE o.escola_id=? ORDER BY o.criado_em DESC",[$id]);
        $this->view('ronda_escolar/escola', compact('escola','rondas','ocorrencias'), 'main');
    }
}
