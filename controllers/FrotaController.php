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
 */class FrotaController extends Controller {

    public function viaturas(): void {
        Auth::requireAuth();
        $viaturas = $this->db->fetchAll("SELECT * FROM viaturas ORDER BY prefixo");
        $this->view('frota/viaturas', compact('viaturas'), 'main');
    }

    public function novaViatura(): void {
        Auth::requirePermission('frota.cadastrar_viaturas');
        $this->view('frota/viatura_form', ['viatura'=>null], 'main');
    }

    public function salvarViatura(): void {
        Auth::requirePermission('frota.cadastrar_viaturas');
        $this->verifyCsrf();
        $prefixo = trim($_POST['prefixo'] ?? '');
        if (!$prefixo) { $this->flash('error','Prefixo é obrigatório.'); $this->redirect('frota/viaturas/nova'); }
        $this->db->execute(
            "INSERT INTO viaturas (prefixo,placa,modelo,marca,ano,cor,km_atual,status,observacoes)
             VALUES (?,?,?,?,?,?,?,?,?)",
            [$prefixo,$_POST['placa']??'',$_POST['modelo']??'',$_POST['marca']??'',$_POST['ano']?:null,$_POST['cor']??'',$_POST['km_atual']??0,$_POST['status']??'disponivel',$_POST['observacoes']??'']
        );
        $id = $this->db->lastInsertId();
        AuditLogger::create('viaturas',(int)$id,['prefixo'=>$prefixo]);
        $this->flash('success',"Viatura $prefixo cadastrada!");
        $this->redirect("frota/viaturas/$id");
    }

    public function editarViatura(string $id): void {
        Auth::requirePermission('frota.cadastrar_viaturas');
        $viatura = $this->db->fetchOne("SELECT * FROM viaturas WHERE id=?",[$id]);
        if (!$viatura) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        $this->view('frota/viatura_form', compact('viatura'), 'main');
    }

    public function atualizarViatura(string $id): void {
        Auth::requirePermission('frota.cadastrar_viaturas');
        $this->verifyCsrf();
        $viatura = $this->db->fetchOne("SELECT * FROM viaturas WHERE id=?",[$id]);
        if (!$viatura) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }

        $prefixo = trim($_POST['prefixo'] ?? '');
        if (!$prefixo) { $this->flash('error','Prefixo é obrigatório.'); $this->redirect("frota/viaturas/$id/editar"); }

        $this->db->execute(
            "UPDATE viaturas SET prefixo=?, placa=?, modelo=?, marca=?, ano=?, cor=?, km_atual=?, status=?, observacoes=?, ativa=? WHERE id=?",
            [$prefixo, $_POST['placa']??'', $_POST['modelo']??'', $_POST['marca']??'', $_POST['ano']?:null, $_POST['cor']??'', $_POST['km_atual']??0, $_POST['status']??'disponivel', $_POST['observacoes']??'', ($_POST['status']??'')==='inativa'?0:1, $id]
        );
        AuditLogger::update('viaturas',(int)$id,$viatura,['prefixo'=>$prefixo,'status'=>$_POST['status']]);
        $this->flash('success',"Viatura $prefixo atualizada!");
        $this->redirect("frota/viaturas/$id");
    }

    public function excluirViatura(string $id): void {
        Auth::requirePermission('frota.cadastrar_viaturas');
        $this->verifyCsrf();
        $viatura = $this->db->fetchOne("SELECT * FROM viaturas WHERE id=?",[$id]);
        if (!$viatura) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }

        // Mudar status para inativa e ativa = 0 para simular exclusão e não afetar histórico
        $this->db->execute("UPDATE viaturas SET ativa=0, status='inativa' WHERE id=?", [$id]);
        AuditLogger::update('viaturas',(int)$id,$viatura,['ativa'=>0,'status'=>'inativa']);
        $this->flash('success',"Viatura ".$viatura['prefixo']." removida com sucesso!");
        $this->redirect("frota/viaturas");
    }

    public function verViatura(string $id): void {
        Auth::requireAuth();
        $v = $this->db->fetchOne("SELECT * FROM viaturas WHERE id=?",[$id]);
        if (!$v) { http_response_code(404); include ROOT_PATH.'/views/errors/404.php'; exit; }
        // Tabelas corretas de acordo com o setup.php
        $checklists = $this->db->fetchAll(
            "SELECT c.*,u.nome as agente FROM checklists_viatura c
             JOIN usuarios u ON u.id=c.agente_id WHERE c.viatura_id=? ORDER BY c.realizado_em DESC LIMIT 10",[$id]
        );
        $abastec = $this->db->fetchAll(
            "SELECT a.*,u.nome as agente FROM abastecimentos a
             JOIN usuarios u ON u.id=a.agente_id WHERE a.viatura_id=? ORDER BY a.data_abastecimento DESC LIMIT 10",[$id]
        );
        $this->view('frota/viatura_ver', compact('v','checklists','abastec'), 'main');
    }

    public function checklists(): void {
        Auth::requirePermission('frota.checklists');
        $checklists = $this->db->fetchAll(
            "SELECT c.*,u.nome as agente,v.prefixo FROM checklists_viatura c
             JOIN usuarios u ON u.id=c.agente_id JOIN viaturas v ON v.id=c.viatura_id
             ORDER BY c.realizado_em DESC LIMIT 50"
        );
        $viaturas = $this->db->fetchAll("SELECT id,prefixo,placa FROM viaturas WHERE ativa=1 AND status != 'inativa' ORDER BY prefixo");
        $this->view('frota/checklists', compact('checklists','viaturas'), 'main');
    }

    public function novoChecklist(): void {
        Auth::requirePermission('frota.checklists');
        $viaturas = $this->db->fetchAll("SELECT id,prefixo,placa,km_atual,modelo FROM viaturas WHERE ativa=1 AND status != 'inativa' ORDER BY prefixo");
        $this->view('frota/checklist_form', compact('viaturas'), 'main');
    }

    public function salvarChecklist(): void {
        Auth::requirePermission('frota.checklists');
        $this->verifyCsrf();
        $vId = $_POST['viatura_id'] ?? null;
        if (!$vId) { $this->flash('error','Selecione a viatura.'); $this->redirect('frota/checklists/novo'); }

        // Montar json dos itens
        $itens = ['pneus','luzes','lataria','vidros','giroflex','sirene','estepe','extintor','documentos','triangulo','oleo','agua','combustivel','colete','algemas','radio'];
        $itensJson = [];
        foreach ($itens as $item) {
            $itensJson[$item] = $_POST[$item] ?? 'ok';
        }
        // Determinar status_geral
        $statusGeral = 'ok';
        foreach ($itensJson as $v) {
            if ($v === 'problema') { $statusGeral = 'problema'; break; }
            if ($v === 'atencao')  { $statusGeral = 'atencao'; }
        }

        $this->db->execute(
            "INSERT INTO checklists_viatura (viatura_id,agente_id,tipo,km,status_geral,observacoes,
             pneus,luzes,lataria,vidros,giroflex,sirene,estepe,extintor,documentos,triangulo,oleo,agua,combustivel,colete,algemas,radio)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $vId, Auth::id(), $_POST['tipo']??'saida', $_POST['km']?:null, $statusGeral, $_POST['observacoes']??'',
                $itensJson['pneus'], $itensJson['luzes'], $itensJson['lataria'], $itensJson['vidros'],
                $itensJson['giroflex'], $itensJson['sirene'], $itensJson['estepe'], $itensJson['extintor'],
                $itensJson['documentos'], $itensJson['triangulo'], $itensJson['oleo'], $itensJson['agua'],
                $itensJson['combustivel'], $itensJson['colete'], $itensJson['algemas'], $itensJson['radio']
            ]
        );
        $id = $this->db->lastInsertId();

        if (!empty($_POST['km'])) {
            $this->db->execute("UPDATE viaturas SET km_atual=? WHERE id=?",[$_POST['km'],$vId]);
        }

        $this->flash('success','Checklist registrado com sucesso!');
        $this->redirect('frota/checklists');
    }

    public function abastecimentos(): void {
        Auth::requirePermission('frota.abastecimentos');
        $abastec = $this->db->fetchAll(
            "SELECT a.*,u.nome as agente,v.prefixo FROM abastecimentos a
             JOIN usuarios u ON u.id=a.agente_id JOIN viaturas v ON v.id=a.viatura_id
             ORDER BY a.data_abastecimento DESC LIMIT 50"
        );
        $this->view('frota/abastecimentos', compact('abastec'), 'main');
    }

    public function novoAbastecimento(): void {
        Auth::requirePermission('frota.abastecimentos');
        $viaturas = $this->db->fetchAll("SELECT id,prefixo,placa,km_atual FROM viaturas WHERE ativa=1 AND status != 'inativa' ORDER BY prefixo");
        $this->view('frota/abastecimento_form', compact('viaturas'), 'main');
    }

    public function salvarAbastecimento(): void {
        Auth::requirePermission('frota.abastecimentos');
        $this->verifyCsrf();
        $vId = $_POST['viatura_id'] ?? null;
        if (!$vId) { $this->flash('error','Selecione a viatura.'); $this->redirect('frota/abastecimentos/novo'); }

        $km    = $_POST['km_atual'] ?? null;
        $kmAnt = $this->db->fetchOne("SELECT km_atual FROM viaturas WHERE id=?",[$vId])['km_atual'] ?? 0;
        $consumo = ($km && $kmAnt && $_POST['litros']) ? round(($km - $kmAnt) / $_POST['litros'],2) : null;

        $this->db->execute(
            "INSERT INTO abastecimentos (viatura_id,agente_id,data_abastecimento,litros,valor_total,km_atual,km_anterior,consumo_calculado,posto,tipo_combustivel,observacoes)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            [$vId,Auth::id(),$_POST['data_abastecimento'],$_POST['litros']?:null,$_POST['valor_total']?:null,$km,$kmAnt,$consumo,$_POST['posto']??'',$_POST['tipo_combustivel']??'gasolina',$_POST['observacoes']??'']
        );

        if ($km) {
            $this->db->execute("UPDATE viaturas SET km_atual=? WHERE id=?",[$km,$vId]);
        }

        $this->flash('success','Abastecimento registrado! Consumo: '.($consumo?$consumo.' km/L':'não calculado'));
        $this->redirect('frota/abastecimentos');
    }
}
