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
 */class CorregedoriaController extends Controller {

    public function index(): void {
        Auth::requirePermission('corregedoria.ver');
        $status = $_GET['status'] ?? '';
        $where = $status ? ['status = ?'] : ['1=1'];
        $params = $status ? [$status] : [];
        $processos = $this->db->fetchAll(
            "SELECT p.*,u.nome as investigado_nome FROM processos_corregedoria p
             LEFT JOIN usuarios u ON u.id=p.investigado_id
             WHERE ".implode(' AND ',$where)."
             ORDER BY p.id DESC",
            $params
        );
        $this->view('corregedoria/index', compact('processos','status'), 'main');
    }

    public function novo(): void {
        Auth::requirePermission('corregedoria.criar');
        $agentes = $this->db->fetchAll("SELECT id,nome,matricula FROM usuarios WHERE ativo=1 ORDER BY nome");
        $numero  = 'CORR-'.date('Y').'-'.str_pad(rand(1,999),3,'0',STR_PAD_LEFT);
        $this->view('corregedoria/form', compact('agentes','numero') + ['processo'=>null], 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('corregedoria.criar');
        $this->verifyCsrf();
        $this->db->execute(
            "INSERT INTO processos_corregedoria (numero,tipo,investigado_id,fato_descricao,data_abertura,prazo_legal,status,criado_por)
             VALUES (?,?,?,?,?,?,?,?)",
            [$_POST['numero'],$_POST['tipo'],$_POST['investigado_id']?:null,$_POST['fato_descricao']??'',$_POST['data_abertura']??date('Y-m-d'),$_POST['prazo_legal']?:null,'aberto',Auth::id()]
        );
        $id = $this->db->lastInsertId();
        AuditLogger::create('processos_corregedoria',(int)$id,['numero'=>$_POST['numero']]);
        $this->flash('success','Processo aberto!');
        $this->redirect("corregedoria/$id");
    }

    public function ver(string $id): void {
        Auth::requirePermission('corregedoria.ver');
        $p = $this->db->fetchOne("SELECT pr.*,u.nome as investigado_nome FROM processos_corregedoria pr LEFT JOIN usuarios u ON u.id=pr.investigado_id WHERE pr.id=?",[$id]);
        if (!$p) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        $movs = $this->db->fetchAll("SELECT m.*,u.nome as registrado FROM processos_movimentacoes m LEFT JOIN usuarios u ON u.id=m.registrado_por WHERE m.processo_id=? ORDER BY m.criado_em DESC",[$id]);
        AuditLogger::view('processos_corregedoria',(int)$id);
        $this->view('corregedoria/ver', compact('p','movs'), 'main');
    }

    public function addMovimentacao(string $id): void {
        Auth::requirePermission('corregedoria.editar');
        $this->verifyCsrf();
        $this->db->execute(
            "INSERT INTO processos_movimentacoes (processo_id,descricao,registrado_por) VALUES (?,?,?)",
            [$id,$_POST['descricao']??'',Auth::id()]
        );
        if (!empty($_POST['novo_status'])) {
            $this->db->execute("UPDATE processos_corregedoria SET status=? WHERE id=?",[$_POST['novo_status'],$id]);
        }
        $this->flash('success','Movimentação registrada!');
        $this->redirect("corregedoria/$id");
    }
}
