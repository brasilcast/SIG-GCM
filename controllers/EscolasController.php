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
 */class EscolasController extends Controller {

    public function index(): void {
        Auth::requireAuth();
        // Apenas para listar caso precisem (geralmente gerenciam na Ronda Escolar msm, 
        // mas aqui fica sendo o painel administrativo das escolas)
        if (!Auth::can('ronda_escolar.ver_todos')) {
            $this->redirect('ronda-escolar');
        }
        $escolas = $this->db->fetchAll("SELECT * FROM escolas WHERE ativa=1 ORDER BY nome");
        $this->view('escolas/index', compact('escolas'), 'main');
    }

    public function nova(): void {
        Auth::requirePermission('ronda_escolar.registrar'); // ou uma permissão específica
        $escola = ['id'=>'','nome'=>'','endereco'=>'','bairro'=>'','diretor'=>'','telefone'=>''];
        $this->view('escolas/form', compact('escola'), 'main');
    }

    public function editar(string $id): void {
        Auth::requirePermission('ronda_escolar.registrar');
        $escola = $this->db->fetchOne("SELECT * FROM escolas WHERE id=? AND ativa=1", [$id]);
        if (!$escola) {
            $this->flash('error', 'Escola não encontrada.');
            $this->redirect('ronda-escolar');
        }
        $this->view('escolas/form', compact('escola'), 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('ronda_escolar.registrar');
        $this->verifyCsrf();
        $id = $_POST['id'] ?? '';
        
        if ($id) {
            $this->db->execute(
                "UPDATE escolas SET nome=?, endereco=?, bairro=?, diretor=?, telefone=? WHERE id=?",
                [$_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['diretor'], $_POST['telefone'], $id]
            );
            AuditLogger::log('UPDATE','escolas',(int)$id,null,['nome'=>$_POST['nome']]);
            $this->flash('success', 'Escola atualizada com sucesso!');
        } else {
            $this->db->execute(
                "INSERT INTO escolas (nome, endereco, bairro, diretor, telefone, turno, ativa) VALUES (?, ?, ?, ?, ?, '', 1)",
                [$_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['diretor'], $_POST['telefone']]
            );
            $newId = $this->db->lastInsertId();
            AuditLogger::log('CREATE','escolas',(int)$newId,null,['nome'=>$_POST['nome']]);
            $this->flash('success', 'Escola cadastrada com sucesso!');
        }
        
        $this->redirect('ronda-escolar');
    }

    public function excluir(string $id): void {
        Auth::requirePermission('ronda_escolar.registrar');
        $this->verifyCsrf();
        $this->db->execute("UPDATE escolas SET ativa=0 WHERE id=?", [$id]);
        AuditLogger::log('DELETE','escolas',(int)$id,null,['acao'=>'desativacao']);
        $this->flash('success', 'Escola excluída com sucesso!');
        $this->redirect('ronda-escolar');
    }
}
