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
 */class UsuariosController extends Controller {

    public function index(): void {
        Auth::requirePermission('usuarios.criar');
        $busca  = $_GET['q'] ?? '';
        $perfil = $_GET['perfil'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = ['1=1'];
        $params = [];
        if ($busca)  { $where[] = "(nome LIKE ? OR username LIKE ? OR matricula LIKE ?)"; $params = array_merge($params, ["%$busca%","%$busca%","%$busca%"]); }
        if ($perfil) { $where[] = "perfil = ?"; $params[] = $perfil; }

        $total = $this->db->fetchOne("SELECT COUNT(*) as n FROM usuarios WHERE " . implode(' AND ', $where), $params)['n'];
        $pag   = $this->paginate($total, $page);
        $params[] = $pag['per_page'];
        $params[] = $pag['offset'];
        $usuarios = $this->db->fetchAll("SELECT id,nome,username,perfil,matricula,ativo,ultimo_login FROM usuarios WHERE " . implode(' AND ', $where) . " ORDER BY nome LIMIT ? OFFSET ?", $params);

        $this->view('usuarios/index', compact('usuarios','busca','perfil','pag'), 'main');
    }

    public function novo(): void {
        Auth::requirePermission('usuarios.criar');
        $this->view('usuarios/form', ['usuario'=>null,'titulo'=>'Novo Usuário'], 'main');
    }

    public function salvar(): void {
        Auth::requirePermission('usuarios.criar');
        $this->verifyCsrf();
        $data = $this->validar($_POST);
        if (!$data['ok']) { $this->flash('error', $data['msg']); $this->redirect('usuarios/novo'); }
        
        $senha = $_POST['senha'] ?? '';
        if (empty($senha)) {
            $this->flash('error', 'A Senha Inicial é obrigatória.');
            $this->redirect('usuarios/novo');
        }

        $hash = Auth::hashSenha($senha);

        // Apenas colunas que existem na tabela usuarios
        $this->db->execute(
            "INSERT INTO usuarios (nome,username,senha,perfil,equipe,matricula,email,cpf,rg,data_nascimento,telefone,endereco,bairro,cidade,estado,ativo,primeiro_acesso)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,1)",
            [$data['nome'],$data['username'],$hash,$data['perfil'],$data['equipe']?:null,$data['matricula']?:null,$data['email']?:null,
             $data['cpf']?:null, $data['rg']?:null, !empty($data['data_nascimento'])?$data['data_nascimento']:null, $data['telefone']?:null,
             $data['endereco']?:null, $data['bairro']?:null, $data['cidade']?:null, $data['estado']?:null]
        );
        $id = $this->db->lastInsertId();
        AuditLogger::create('usuarios', (int)$id, ['username'=>$data['username'],'perfil'=>$data['perfil']]);
        $this->flash('success', "Usuário <b>{$data['username']}</b> criado com sucesso! A senha inicial definida foi salva.");
        $this->redirect("usuarios/$id");
    }

    public function ver(string $id): void {
        Auth::requirePermission('usuarios.criar');
        $u      = $this->buscarOu404((int)$id);
        $logins = $this->db->fetchAll("SELECT * FROM login_log WHERE usuario_id=? ORDER BY tentado_em DESC LIMIT 20", [$id]);
        $this->view('usuarios/ver', compact('u','logins'), 'main');
    }

    public function editar(string $id): void {
        Auth::requirePermission('usuarios.criar');
        $u = $this->buscarOu404((int)$id);
        $this->view('usuarios/form', ['usuario'=>$u,'titulo'=>'Editar Usuário'], 'main');
    }

    public function atualizar(string $id): void {
        Auth::requirePermission('usuarios.criar');
        $this->verifyCsrf();
        $u    = $this->buscarOu404((int)$id);
        $data = $this->validar($_POST, (int)$id);
        if (!$data['ok']) { $this->flash('error', $data['msg']); $this->redirect("usuarios/$id/editar"); }

        $antes = $u;
        $this->db->execute(
            "UPDATE usuarios SET nome=?,username=?,perfil=?,equipe=?,matricula=?,email=?,cpf=?,rg=?,data_nascimento=?,telefone=?,endereco=?,bairro=?,cidade=?,estado=?,updated_at=NOW() WHERE id=?",
            [$data['nome'],$data['username'],$data['perfil'],$data['equipe']?:null,$data['matricula']?:null,$data['email']?:null,
             $data['cpf']?:null, $data['rg']?:null, !empty($data['data_nascimento'])?$data['data_nascimento']:null, $data['telefone']?:null,
             $data['endereco']?:null, $data['bairro']?:null, $data['cidade']?:null, $data['estado']?:null,$id]
        );
        AuditLogger::update('usuarios',(int)$id,$antes,$data);
        $this->flash('success','Usuário atualizado com sucesso!');
        $this->redirect("usuarios/$id");
    }

    public function toggle(string $id): void {
        Auth::requirePermission('usuarios.criar');
        $this->verifyCsrf();
        $u = $this->buscarOu404((int)$id);
        if ((int)$id === Auth::id()) { $this->flash('error','Você não pode desativar sua própria conta.'); $this->redirect("usuarios/$id"); }
        $novo = $u['ativo'] ? 0 : 1;
        $this->db->execute("UPDATE usuarios SET ativo=? WHERE id=?",[$novo,$id]);
        $this->flash('success', $novo ? 'Usuário ativado.' : 'Usuário desativado.');
        $this->redirect("usuarios/$id");
    }

    public function permissoes(string $id): void {
        Auth::requirePermission('usuarios.alterar_permissoes');
        $u = $this->buscarOu404((int)$id);
        $perms    = $this->db->fetchAll("SELECT recurso,permitido FROM permissoes_usuario WHERE usuario_id=?",[$id]);
        $permsMap = array_column($perms,'permitido','recurso');
        $this->view('usuarios/permissoes', compact('u','permsMap'), 'main');
    }

    public function gerarSenhaTemporaria(string $id): void {
        Auth::requirePermission('usuarios.criar');
        $this->verifyCsrf();
        $u = $this->buscarOu404((int)$id);

        $senhaTemp = Auth::gerarSenhaTemporaria();
        $hash      = Auth::hashSenha($senhaTemp);

        $this->db->execute(
            "UPDATE usuarios SET senha=?, primeiro_acesso=1, updated_at=NOW() WHERE id=?",
            [$hash, $id]
        );

        AuditLogger::log('UPDATE','usuarios',(int)$id,null,['acao'=>'gerar_senha_temporaria']);
        $this->flash('success', "Nova senha temporária gerada para <b>{$u['username']}</b>: <b style='font-size:1.1rem;letter-spacing:1px;background:#eee;padding:2px 6px;border-radius:4px'>{$senhaTemp}</b> — anote antes de fechar.");
        $this->redirect("usuarios/$id");
    }

    public function salvarPermissoes(string $id): void {
        Auth::requirePermission('usuarios.alterar_permissoes');
        $this->verifyCsrf();
        $this->buscarOu404((int)$id);

        $todos    = $this->listarTodosRecursos();
        $marcados = $_POST['perms'] ?? [];

        foreach ($todos as $recurso) {
            $permitido = in_array($recurso, $marcados) ? 1 : 0;
            $this->db->execute(
                "INSERT INTO permissoes_usuario (usuario_id,recurso,permitido) VALUES (?,?,?)
                 ON DUPLICATE KEY UPDATE permitido=VALUES(permitido)",
                [$id,$recurso,$permitido]
            );
        }
        AuditLogger::log('UPDATE','permissoes_usuario',(int)$id,null,['perms_concedidas'=>count($marcados)]);
        $this->flash('success','Permissões salvas com sucesso!');
        $this->redirect("usuarios/$id/permissoes");
    }

    // ---- helpers ----
    private function buscarOu404(int $id): array {
        $u = $this->db->fetchOne("SELECT * FROM usuarios WHERE id=?",[$id]);
        if (!$u) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        return $u;
    }

    private function validar(array $post, ?int $ignorarId=null): array {
        $nome   = trim($post['nome'] ?? '');
        $user   = trim($post['username'] ?? '');
        $perf   = $post['perfil'] ?? '';
        $perfis = ['operador','inspetor','subcomandante','comandante','corregedor','admin'];
        if (!$nome) return ['ok'=>false,'msg'=>'Nome é obrigatório.'];
        if (!$user) return ['ok'=>false,'msg'=>'Username é obrigatório.'];
        if (!in_array($perf,$perfis)) return ['ok'=>false,'msg'=>'Perfil inválido.'];
        $equi   = $post['equipe'] ?? '';
        $equipes = ['alfa','bravo','charlie','delta'];
        if ($equi && !in_array($equi, $equipes)) return ['ok'=>false,'msg'=>'Equipe inválida.'];

        $exists = $this->db->fetchOne("SELECT id FROM usuarios WHERE username=?".($ignorarId?" AND id<>$ignorarId":''),[$user]);
        if ($exists) return ['ok'=>false,'msg'=>"Username '$user' já está em uso."];
        return array_merge(['ok'=>true,'msg'=>''],$post,['nome'=>$nome,'username'=>$user,'perfil'=>$perf,'equipe'=>$equi]);
    }

    private function listarTodosRecursos(): array {
        return [
            'dashboard','taloes.criar','taloes.ver_proprios','taloes.ver_todos','taloes.editar_parcial','taloes.editar_todos',
            'frota.checklists','frota.abastecimentos','frota.cadastrar_viaturas','frota.ver',
            'escala.ver_propria','escala.criar','escala.editar','escala.ver_todos',
            'troca.solicitar','troca.aprovar','banco_horas.ver_proprio','banco_horas.lancar',
            'ronda_escolar.registrar','ronda_escolar.ver_todos',
            'maria_penha.visitar','maria_penha.cadastrar_vitima','maria_penha.ver',
            'comunicados.ver','comunicados.criar',
            'corregedoria.ver','corregedoria.criar','corregedoria.editar',
            'auditoria.ver','usuarios.criar','usuarios.alterar_permissoes',
        ];
    }
}
