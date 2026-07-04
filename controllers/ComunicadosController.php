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
 */class ComunicadosController extends Controller {

    public function index(): void {
        Auth::requirePermission('comunicados.ver');
        $userId = Auth::id();
        $perfil = Auth::perfil();
        $comunicados = $this->db->fetchAll(
            "SELECT c.*,u.nome as criado_por_nome,
              (SELECT confirmado FROM comunicados_leitura WHERE comunicado_id=c.id AND usuario_id=?) as lido
             FROM comunicados c LEFT JOIN usuarios u ON u.id=c.criado_por
             WHERE (c.valido_ate IS NULL OR c.valido_ate >= CURDATE())
               AND (c.destino='todos' OR (c.destino='perfil' AND c.perfil_destino=?) OR (c.destino='usuario' AND c.usuario_destino_id=?))
             ORDER BY c.criado_em DESC",
            [$userId,$perfil,$userId]
        );
        $this->view('comunicados/index', compact('comunicados'), 'main');
    }

    public function novo(): void {
        Auth::requirePermission('comunicados.criar');
        $usuarios = $this->db->fetchAll("SELECT id,nome,perfil FROM usuarios WHERE ativo=1 ORDER BY nome");
        $this->view('comunicados/form', compact('usuarios'), 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('comunicados.criar');
        $this->verifyCsrf();
        $this->db->execute(
            "INSERT INTO comunicados (titulo,conteudo,tipo,destino,perfil_destino,usuario_destino_id,requer_confirmacao,valido_ate,criado_por)
             VALUES (?,?,?,?,?,?,?,?,?)",
            [$_POST['titulo']??'',$_POST['conteudo']??'',$_POST['tipo']??'informacao',$_POST['destino']??'todos',$_POST['perfil_destino']??null,$_POST['usuario_destino_id']?:null,(int)($_POST['requer_confirmacao']??0),$_POST['valido_ate']?:null,Auth::id()]
        );
        $this->flash('success','Comunicado publicado!');
        $this->redirect('comunicados');
    }

    public function confirmar(string $id): void {
        Auth::requireAuth();
        $this->verifyCsrf();
        $this->db->execute(
            "INSERT INTO comunicados_leitura (comunicado_id,usuario_id,confirmado,confirmado_em) VALUES (?,?,1,NOW())
             ON DUPLICATE KEY UPDATE confirmado=1,confirmado_em=NOW()",
            [$id,Auth::id()]
        );
        $this->flash('success','Leitura confirmada!');
        $this->back();
    }
}
