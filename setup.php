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
// SIG-GCM — Instalador do Banco de Dados
// Acesse: https://sig-teste.fun/setup.php
// ATENÇÃO: Apague este arquivo após executar!
// =============================================

// Segurança básica
define('SETUP_TOKEN', 'gcm2025setup');
if (!isset($_GET['token']) || $_GET['token'] !== SETUP_TOKEN) {
    die('<h2 style="font-family:monospace;color:red">Acesso negado. Use: /setup.php?token=gcm2025setup</h2>');
}

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/core/Database.php';

$db = Database::getInstance()->getConnection();
$erros = [];
$ok = [];

function run(PDO $db, string $sql, string $label, array &$ok, array &$erros): void {
    try {
        $db->exec($sql);
        $ok[] = "<i class='fa-solid fa-check'></i> $label";
    } catch (PDOException $e) {
        $erros[] = "<i class='fa-solid fa-xmark'></i> $label — " . $e->getMessage();
    }
}

$db->exec("SET NAMES utf8mb4");
$db->exec("SET foreign_key_checks = 0");

// ======= TABELAS =======

run($db, "CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120) NOT NULL,
    matricula     VARCHAR(30)  UNIQUE,
    username      VARCHAR(60)  NOT NULL UNIQUE,
    email         VARCHAR(120),
    cpf           VARCHAR(14),
    rg            VARCHAR(20),
    data_nascimento DATE,
    telefone      VARCHAR(20),
    endereco      VARCHAR(200),
    bairro        VARCHAR(100),
    cidade        VARCHAR(100),
    estado        VARCHAR(2),
    senha         VARCHAR(255) NOT NULL,
    perfil        ENUM('operador','inspetor','subcomandante','comandante','corregedor','admin') NOT NULL DEFAULT 'operador',
    equipe        ENUM('alfa','bravo','charlie','delta') NULL DEFAULT NULL,
    foto          VARCHAR(255),
    ativo         TINYINT(1)   NOT NULL DEFAULT 1,
    primeiro_acesso TINYINT(1) NOT NULL DEFAULT 1,
    ultimo_login  DATETIME     NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela usuarios", $ok, $erros);

// Ajustar tabela escalas para novos turnos e horário de entrada
try { 
    $db->exec("ALTER TABLE escalas MODIFY COLUMN turno ENUM('diurno','noturno','folga') NOT NULL DEFAULT 'diurno'");
    $db->exec("ALTER TABLE escalas ADD COLUMN entrada TIME NULL AFTER data");
    $ok[] = "<i class='fa-solid fa-check'></i> Tabela escalas atualizada (turnos e entrada)";
} catch(Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) {
        $erros[] = "<i class='fa-solid fa-xmark'></i> Erro ao atualizar tabela escalas: " . $e->getMessage();
    }
}

run($db, "CREATE TABLE IF NOT EXISTS permissoes_usuario (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NOT NULL,
    recurso     VARCHAR(100) NOT NULL,
    permitido   TINYINT(1) DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_recurso (usuario_id, recurso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela permissoes_usuario", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS login_log (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id       INT UNSIGNED NULL,
    username_tentado VARCHAR(100),
    ip               VARCHAR(45),
    user_agent       VARCHAR(255),
    sucesso          TINYINT(1) DEFAULT 0,
    tentado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela login_log", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NULL,
    acao        VARCHAR(30) NOT NULL,
    tabela      VARCHAR(80),
    registro_id INT UNSIGNED NULL,
    dados_antes JSON,
    dados_depois JSON,
    ip          VARCHAR(45),
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela audit_log", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS naturezas_ocorrencia (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grupo    VARCHAR(60) NOT NULL,
    nome     VARCHAR(100) NOT NULL,
    codigo   VARCHAR(20),
    ativa    TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela naturezas_ocorrencia", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS ocorrencias (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero               VARCHAR(20)  NOT NULL UNIQUE,
    natureza_id          INT UNSIGNED,
    status               ENUM('aberto','em_atendimento','fechado') NOT NULL DEFAULT 'aberto',
    data_hora_abertura   DATETIME NOT NULL,
    data_hora_fechamento DATETIME NULL,
    agente_id            INT UNSIGNED NOT NULL,
    viatura_id           INT UNSIGNED NULL,
    km_inicial           DECIMAL(10,1),
    km_final             DECIMAL(10,1),
    latitude             DECIMAL(10,8),
    longitude            DECIMAL(11,8),
    endereco             VARCHAR(200),
    bairro               VARCHAR(100),
    referencia           VARCHAR(200),
    narrativa            TEXT,
    encaminhamento       TEXT,
    medidas_tomadas      TEXT,
    observacoes_internas TEXT,
    criado_por           INT UNSIGNED NOT NULL,
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela ocorrencias", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS ocorrencias_agentes (
    ocorrencia_id INT UNSIGNED NOT NULL,
    usuario_id    INT UNSIGNED NOT NULL,
    PRIMARY KEY (ocorrencia_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela ocorrencias_agentes", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS ocorrencias_envolvidos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT UNSIGNED NOT NULL,
    tipo          ENUM('autor','vitima','testemunha','outro') NOT NULL DEFAULT 'outro',
    nome          VARCHAR(120),
    cpf           VARCHAR(14),
    rg            VARCHAR(20),
    data_nascimento DATE,
    telefone      VARCHAR(20),
    endereco      VARCHAR(200),
    observacoes   TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela ocorrencias_envolvidos", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS ocorrencias_atualizacoes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT UNSIGNED NOT NULL,
    autor_id      INT UNSIGNED NOT NULL,
    texto         TEXT NOT NULL,
    criado_em     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela ocorrencias_atualizacoes", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS ocorrencias_fotos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT UNSIGNED NOT NULL,
    caminho       VARCHAR(255) NOT NULL,
    descricao     VARCHAR(200),
    criado_em     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela ocorrencias_fotos", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS viaturas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prefixo     VARCHAR(20) NOT NULL UNIQUE,
    placa       VARCHAR(10),
    modelo      VARCHAR(80),
    marca       VARCHAR(60),
    ano         YEAR,
    cor         VARCHAR(40),
    tipo        ENUM('viatura','moto','bicicleta','van','outros') DEFAULT 'viatura',
    combustivel ENUM('gasolina','etanol','diesel','flex','gnv','eletrico') DEFAULT 'flex',
    km_atual    DECIMAL(10,1) DEFAULT 0,
    status      ENUM('disponivel','em_uso','manutencao','inativa') DEFAULT 'disponivel',
    ativa       TINYINT(1) DEFAULT 1,
    observacoes TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela viaturas", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS checklists_viatura (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    viatura_id      INT NOT NULL,
    agente_id       INT NOT NULL,
    tipo            ENUM('saida','retorno') NOT NULL,
    km              DECIMAL(10,2),
    status_geral    ENUM('ok','atencao','problema') DEFAULT 'ok',
    pneus           ENUM('ok','atencao','problema') DEFAULT 'ok',
    luzes           ENUM('ok','atencao','problema') DEFAULT 'ok',
    lataria         ENUM('ok','atencao','problema') DEFAULT 'ok',
    vidros          ENUM('ok','atencao','problema') DEFAULT 'ok',
    giroflex        ENUM('ok','atencao','problema') DEFAULT 'ok',
    sirene          ENUM('ok','atencao','problema') DEFAULT 'ok',
    estepe          ENUM('ok','atencao','problema') DEFAULT 'ok',
    extintor        ENUM('ok','atencao','problema') DEFAULT 'ok',
    documentos      ENUM('ok','atencao','problema') DEFAULT 'ok',
    triangulo       ENUM('ok','atencao','problema') DEFAULT 'ok',
    oleo            ENUM('ok','atencao','problema') DEFAULT 'ok',
    agua            ENUM('ok','atencao','problema') DEFAULT 'ok',
    combustivel     ENUM('ok','atencao','problema') DEFAULT 'ok',
    colete          ENUM('ok','atencao','problema') DEFAULT 'ok',
    algemas         ENUM('ok','atencao','problema') DEFAULT 'ok',
    radio           ENUM('ok','atencao','problema') DEFAULT 'ok',
    observacoes     TEXT,
    realizado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela checklists_viatura", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS abastecimentos (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    viatura_id          INT NOT NULL,
    agente_id           INT NOT NULL,
    data_abastecimento  DATE NOT NULL,
    litros              DECIMAL(8,2),
    valor_total         DECIMAL(10,2),
    valor_litro         DECIMAL(8,4),
    km_atual            DECIMAL(10,2),
    km_anterior         DECIMAL(10,2),
    consumo_calculado   DECIMAL(6,2),
    posto               VARCHAR(200),
    tipo_combustivel    VARCHAR(50),
    foto_cupom          VARCHAR(255),
    observacoes         TEXT,
    criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela abastecimentos", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS escalas (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agente_id     INT UNSIGNED NOT NULL,
    viatura_id    INT UNSIGNED NULL,
    data          DATE NOT NULL,
    entrada       TIME NULL,
    saida         TIME NULL,
    entrada_tarde TIME NULL,
    saida_tarde   TIME NULL,
    turno         ENUM('diurno','noturno','folga','administrativo') NOT NULL DEFAULT 'diurno',
    setor         VARCHAR(80),
    observacoes   TEXT,
    criado_por    INT UNSIGNED,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela escalas", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS trocas_plantao (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    escala_origem_id  INT UNSIGNED NOT NULL,
    escala_destino_id INT UNSIGNED NOT NULL,
    solicitante_id    INT UNSIGNED NOT NULL,
    substituto_id     INT UNSIGNED NOT NULL,
    status            ENUM('pendente','aprovada','negada') DEFAULT 'pendente',
    motivo            TEXT,
    resposta          TEXT,
    aprovado_por      INT UNSIGNED NULL,
    aprovado_em       DATETIME NULL,
    criado_em         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (escala_origem_id)  REFERENCES escalas(id) ON DELETE CASCADE,
    FOREIGN KEY (escala_destino_id) REFERENCES escalas(id) ON DELETE CASCADE,
    FOREIGN KEY (solicitante_id)    REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (substituto_id)     REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela trocas_plantao", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS banco_horas (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agente_id      INT UNSIGNED NOT NULL,
    tipo           ENUM('credito','debito') NOT NULL,
    horas          DECIMAL(5,2) NOT NULL,
    motivo         VARCHAR(255),
    referencia_data DATE,
    lancado_por    INT UNSIGNED,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela banco_horas", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS escolas (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(120) NOT NULL,
    endereco  VARCHAR(200),
    bairro    VARCHAR(100),
    diretor   VARCHAR(120),
    telefone  VARCHAR(20),
    turno     VARCHAR(40),
    latitude  DECIMAL(10,8),
    longitude DECIMAL(11,8),
    ativa     TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela escolas", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS rondas_escolares (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    escola_id    INT UNSIGNED NOT NULL,
    agente_id    INT UNSIGNED NOT NULL,
    viatura_id   INT UNSIGNED NULL,
    latitude     DECIMAL(10,8),
    longitude    DECIMAL(11,8),
    foto         VARCHAR(255),
    observacoes  TEXT,
    realizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela rondas_escolares", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS ocorrencias_escolares (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    escola_id   INT UNSIGNED NOT NULL,
    agente_id   INT UNSIGNED NOT NULL,
    tipo        VARCHAR(80),
    descricao   TEXT,
    acao_tomada TEXT,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela ocorrencias_escolares", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS vitimas_penha (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome                VARCHAR(120) NOT NULL,
    cpf                 VARCHAR(14),
    data_nascimento     DATE,
    telefone            VARCHAR(20),
    telefone_emergencia VARCHAR(20),
    nome_emergencia     VARCHAR(120),
    prioridade          TINYINT(1) NOT NULL DEFAULT 3,
    endereco            VARCHAR(200),
    bairro              VARCHAR(100),
    latitude            DECIMAL(10,8),
    longitude           DECIMAL(11,8),
    nome_agressor       VARCHAR(120),
    cpf_agressor        VARCHAR(14),
    vinculo_agressor    VARCHAR(100),
    historico_violencia TEXT,
    observacoes         TEXT,
    ativa               TINYINT(1) DEFAULT 1,
    cadastrado_por      INT UNSIGNED,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela vitimas_penha", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS medidas_protetivas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vitima_id       INT UNSIGNED NOT NULL,
    tipo            VARCHAR(120) NOT NULL,
    numero_processo VARCHAR(60),
    data_inicio     DATE,
    data_vencimento DATE NOT NULL,
    status          ENUM('vigente','vencida','renovada','arquivada') DEFAULT 'vigente',
    observacoes     TEXT,
    criado_por      INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela medidas_protetivas", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS visitas_penha (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vitima_id    INT UNSIGNED NOT NULL,
    agente_id    INT UNSIGNED NOT NULL,
    situacao     ENUM('normal','necessita_atencao','urgente') DEFAULT 'normal',
    relato       TEXT,
    latitude     DECIMAL(10,8),
    longitude    DECIMAL(11,8),
    foto         VARCHAR(255),
    realizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela visitas_penha", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS processos_corregedoria (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero         VARCHAR(60) NOT NULL UNIQUE,
    tipo           ENUM('sindicancia','pad','investigacao_preliminar') NOT NULL DEFAULT 'sindicancia',
    investigado_id INT UNSIGNED NULL,
    fato_descricao TEXT NOT NULL,
    data_abertura  DATE NOT NULL,
    prazo_legal    DATE NULL,
    status         ENUM('aberto','em_andamento','concluido','arquivado') DEFAULT 'aberto',
    conclusao      TEXT,
    criado_por     INT UNSIGNED NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela processos_corregedoria", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS processos_movimentacoes (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    processo_id    INT UNSIGNED NOT NULL,
    descricao      TEXT NOT NULL,
    registrado_por INT UNSIGNED,
    criado_em      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela processos_movimentacoes", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS comunicados (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo             VARCHAR(200) NOT NULL,
    conteudo           TEXT NOT NULL,
    tipo               ENUM('informacao','atencao','urgente') DEFAULT 'informacao',
    destino            ENUM('todos','perfil','usuario') DEFAULT 'todos',
    perfil_destino     VARCHAR(40) NULL,
    usuario_destino_id INT UNSIGNED NULL,
    requer_confirmacao TINYINT(1) DEFAULT 0,
    valido_ate         DATE NULL,
    criado_por         INT UNSIGNED NOT NULL,
    criado_em          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela comunicados", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS comunicados_leitura (
    comunicado_id INT UNSIGNED NOT NULL,
    usuario_id    INT UNSIGNED NOT NULL,
    confirmado    TINYINT(1) DEFAULT 0,
    confirmado_em TIMESTAMP NULL,
    PRIMARY KEY (comunicado_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela comunicados_leitura", $ok, $erros);

// ======= TABELAS TALÕES (módulo operacional principal) =======

run($db, "CREATE TABLE IF NOT EXISTS taloes (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero               VARCHAR(20)  NOT NULL,
    natureza_id          INT UNSIGNED NULL,
    status               ENUM('aberto','em_atendimento','fechado') NOT NULL DEFAULT 'aberto',
    tipo_equipe          ENUM('viatura','pop') NOT NULL DEFAULT 'viatura',
    bogm                 VARCHAR(50) NULL,
    bopc                 VARCHAR(50) NULL,
    data_hora_abertura   DATETIME NOT NULL,
    data_hora_fechamento DATETIME NULL,
    agente_id            INT UNSIGNED NOT NULL,
    viatura_id           INT UNSIGNED NULL,
    km_inicial           DECIMAL(10,1),
    km_final             DECIMAL(10,1),
    latitude             DECIMAL(10,8),
    longitude            DECIMAL(11,8),
    endereco             VARCHAR(200),
    bairro               VARCHAR(100),
    referencia           VARCHAR(200),
    narrativa            TEXT,
    encaminhamento       TEXT,
    medidas_tomadas      TEXT,
    observacoes_internas TEXT,
    criado_por           INT UNSIGNED NOT NULL,
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_agente (agente_id),
    INDEX idx_data (data_hora_abertura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela taloes", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS taloes_agentes (
    talao_id   INT UNSIGNED NOT NULL,
    agente_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (talao_id, agente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela taloes_agentes", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS taloes_envolvidos (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    talao_id  INT UNSIGNED NOT NULL,
    tipo      ENUM('autor','vitima','testemunha','outro') NOT NULL DEFAULT 'outro',
    nome      VARCHAR(120),
    cpf       VARCHAR(14),
    rg        VARCHAR(20),
    data_nascimento DATE,
    telefone  VARCHAR(20),
    endereco  VARCHAR(200),
    observacoes TEXT,
    INDEX idx_talao (talao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela taloes_envolvidos", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS taloes_fotos (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    talao_id  INT UNSIGNED NOT NULL,
    caminho   VARCHAR(255) NOT NULL,
    descricao VARCHAR(200),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_talao (talao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela taloes_fotos", $ok, $erros);

run($db, "CREATE TABLE IF NOT EXISTS taloes_atualizacoes (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    talao_id  INT UNSIGNED NOT NULL,
    agente_id INT UNSIGNED NOT NULL,
    texto     TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_talao (talao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", "Tabela taloes_atualizacoes", $ok, $erros);

// ======= DADOS INICIAIS =======

// Naturezas
$checkNat = $db->query("SELECT COUNT(*) FROM naturezas_ocorrencia")->fetchColumn();
if ($checkNat == 0) {
    $db->exec("INSERT INTO naturezas_ocorrencia (grupo, nome, codigo) VALUES
        ('Crimes Contra Pessoa','Lesão Corporal','CP-01'),
        ('Crimes Contra Pessoa','Ameaça','CP-02'),
        ('Crimes Contra Pessoa','Vias de Fato','CP-03'),
        ('Crimes Contra Patrimônio','Furto','CPA-01'),
        ('Crimes Contra Patrimônio','Roubo','CPA-02'),
        ('Crimes Contra Patrimônio','Dano ao Patrimônio','CPA-03'),
        ('Tráfico e Drogas','Uso de Drogas','TR-01'),
        ('Tráfico e Drogas','Tráfico de Drogas','TR-02'),
        ('Perturbação','Perturbação do Sossego','PT-01'),
        ('Perturbação','Embriaguez em Via Pública','PT-02'),
        ('Trânsito','Acidente de Trânsito','TT-01'),
        ('Trânsito','Veículo Abandonado','TT-02'),
        ('Outros','Abordagem Preventiva','OT-01'),
        ('Outros','Apoio a Órgãos Públicos','OT-02'),
        ('Outros','Encontro de Material Ilícito','OT-03')");
    $ok[] = "<i class='fa-solid fa-check'></i> Naturezas de ocorrência inseridas";
}

// Usuários (senhas geradas com password_hash)
$checkUser = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
if ($checkUser == 0) {
    $senhaAdmin = password_hash('Admin@2025', PASSWORD_BCRYPT, ['cost'=>12]);
    $senhaTeste = password_hash('Teste@2025', PASSWORD_BCRYPT, ['cost'=>12]);

    $stmt = $db->prepare("INSERT INTO usuarios (nome, matricula, username, perfil, senha, ativo, primeiro_acesso) VALUES (?,?,?,?,?,1,?)");
    $stmt->execute(['Administrador do Sistema','0000','admin','admin',$senhaAdmin,0]);
    $stmt->execute(['Carlos Operador','001','operador.teste','operador',$senhaTeste,1]);
    $stmt->execute(['Ana Inspetora','002','inspetor.teste','inspetor',$senhaTeste,1]);
    $stmt->execute(['Roberto Subcomandante','003','subcmd.teste','subcomandante',$senhaTeste,1]);
    $stmt->execute(['Maria Comandante','004','comandante.teste','comandante',$senhaTeste,1]);
    $stmt->execute(['José Corregedor','005','corregedor.teste','corregedor',$senhaTeste,1]);

    $ok[] = "<i class='fa-solid fa-check'></i> 6 usuários de teste criados";
}

// ======= VIATURAS DE TESTE =======
$checkViaturas = $db->query("SELECT COUNT(*) FROM viaturas")->fetchColumn();
if ($checkViaturas == 0) {
    $db->exec("INSERT INTO viaturas (prefixo,placa,modelo,marca,ano,cor,tipo,combustivel,km_atual,status,ativa) VALUES
        ('GCM-01','ABC-1234','Sandero','Renault',2021,'Branco','viatura','flex',45230,'disponivel',1),
        ('GCM-02','DEF-5678','Voyage','Volkswagen',2020,'Prata','viatura','flex',62100,'disponivel',1),
        ('GCM-03','GHI-9012','Fan 160','Honda',2022,'Azul','moto','gasolina',18700,'disponivel',1),
        ('GCM-04','JKL-3456','Master','Renault',2019,'Branco','van','diesel',88400,'manutencao',1)");
    $ok[] = "<i class='fa-solid fa-check'></i> 4 viaturas de teste criadas";
}

// ======= ESCOLAS DE TESTE =======
$checkEscolas = $db->query("SELECT COUNT(*) FROM escolas")->fetchColumn();
if ($checkEscolas == 0) {
    $db->exec("INSERT INTO escolas (nome,endereco,bairro,diretor,telefone,turno,ativa) VALUES
        ('EMEF Carlos Drummond de Andrade','Rua das Flores, 100','Centro','Márcia Souza','(17) 3622-1111','Manhã e Tarde',1),
        ('EMEF Santos Dumont','Av. Brasil, 520','Jardim América','Paulo Lima','(17) 3622-2222','Tarde',1),
        ('EE Dr. Plácido Rocha','Rua São Paulo, 300','Vila Nova','Sandra Ferreira','(17) 3622-3333','Manhã',1),
        ('CEMEI Criança Feliz','Rua Mato Grosso, 80','Jardim Paraíso','Cláudia Moura','(17) 3622-4444','Manhã',1)");
    $ok[] = "<i class='fa-solid fa-check'></i> 4 escolas de teste criadas";
}

// ======= PERMISSÕES COMPLETAS PARA TODOS OS USUÁRIOS =======
$checkPerms = $db->query("SELECT COUNT(*) FROM permissoes_usuario")->fetchColumn();
if ($checkPerms == 0) {
    // Buscar IDs
    $users = $db->query("SELECT id, perfil FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

    $permisPorPerfil = [
        'admin' => [
            'dashboard','taloes.criar','taloes.ver_proprios','taloes.ver_todos','taloes.editar_parcial','taloes.editar_todos',
            'frota.checklists','frota.abastecimentos','frota.cadastrar_viaturas','frota.ver',
            'escala.ver_propria','escala.criar','escala.editar','escala.ver_todos',
            'troca.solicitar','troca.aprovar','banco_horas.ver_proprio','banco_horas.lancar',
            'ronda_escolar.registrar','ronda_escolar.ver_todos',
            'maria_penha.visitar','maria_penha.cadastrar_vitima','maria_penha.ver',
            'comunicados.ver','comunicados.criar',
            'corregedoria.ver','corregedoria.criar','corregedoria.editar',
            'auditoria.ver','usuarios.criar','usuarios.alterar_permissoes',
        ],
        'operador' => [
            'dashboard','taloes.criar','taloes.ver_proprios',
            'frota.checklists','frota.abastecimentos','frota.ver',
            'escala.ver_propria','troca.solicitar','banco_horas.ver_proprio',
            'ronda_escolar.registrar',
            'maria_penha.visitar',
            'comunicados.ver',
        ],
        'inspetor' => [
            'dashboard','taloes.criar','taloes.ver_todos','taloes.editar_parcial',
            'frota.checklists','frota.abastecimentos','frota.cadastrar_viaturas','frota.ver',
            'escala.ver_todos','escala.criar','escala.editar',
            'troca.aprovar','troca.solicitar','banco_horas.ver_proprio','banco_horas.lancar',
            'ronda_escolar.registrar','ronda_escolar.ver_todos',
            'maria_penha.visitar','maria_penha.cadastrar_vitima','maria_penha.ver',
            'comunicados.ver','comunicados.criar',
        ],
        'subcomandante' => [
            'dashboard','taloes.ver_todos','taloes.editar_todos',
            'frota.ver','frota.cadastrar_viaturas',
            'escala.ver_todos','escala.criar','escala.editar',
            'troca.aprovar','banco_horas.lancar',
            'ronda_escolar.ver_todos',
            'maria_penha.ver','maria_penha.cadastrar_vitima',
            'comunicados.ver','comunicados.criar',
            'usuarios.criar',
        ],
        'comandante' => [
            'dashboard','taloes.ver_todos','taloes.editar_todos',
            'frota.ver','frota.cadastrar_viaturas',
            'escala.ver_todos','escala.criar','escala.editar',
            'troca.aprovar','banco_horas.lancar',
            'ronda_escolar.ver_todos',
            'maria_penha.ver','maria_penha.cadastrar_vitima',
            'comunicados.ver','comunicados.criar',
            'corregedoria.ver',
            'auditoria.ver','usuarios.criar','usuarios.alterar_permissoes',
        ],
        'corregedor' => [
            'dashboard','taloes.ver_todos',
            'ronda_escolar.ver_todos',
            'maria_penha.ver',
            'comunicados.ver',
            'corregedoria.ver','corregedoria.criar','corregedoria.editar',
            'auditoria.ver',
        ],
    ];

    $stmtPerm = $db->prepare("INSERT IGNORE INTO permissoes_usuario (usuario_id, recurso, permitido) VALUES (?,?,1)");
    foreach ($users as $u) {
        $perms = $permisPorPerfil[$u['perfil']] ?? ['dashboard'];
        foreach ($perms as $p) {
            $stmtPerm->execute([$u['id'], $p]);
        }
    }
    $ok[] = "<i class='fa-solid fa-check'></i> Permissões completas atribuídas a todos os usuários";
}

// ======= COMUNICADO DE BOAS-VINDAS =======
$checkCom = $db->query("SELECT COUNT(*) FROM comunicados")->fetchColumn();
if ($checkCom == 0) {
    $adminId = $db->query("SELECT id FROM usuarios WHERE perfil='admin' LIMIT 1")->fetchColumn();
    if ($adminId) {
        $db->exec("INSERT INTO comunicados (titulo,conteudo,tipo,destino,requer_confirmacao,criado_por) VALUES (
            'Bem-vindos ao SIG-GCM!',
            'O Sistema Integrado de Gestão da Guarda Civil Municipal está operacional. Este sistema foi desenvolvido para facilitar o trabalho de todos os servidores. Em caso de dúvidas, entre em contato com a administração.',
            'informacao','todos',0,$adminId)");
        $ok[] = "<i class='fa-solid fa-check'></i> Comunicado de boas-vindas criado";
    }
}

// ======= ESCALAS DE TESTE =======
$checkEscalas = $db->query("SELECT COUNT(*) FROM escalas")->fetchColumn();
if ($checkEscalas == 0) {
    $operador   = $db->query("SELECT id FROM usuarios WHERE username='operador.teste' LIMIT 1")->fetchColumn();
    $inspetor   = $db->query("SELECT id FROM usuarios WHERE username='inspetor.teste' LIMIT 1")->fetchColumn();
    $admin      = $db->query("SELECT id FROM usuarios WHERE perfil='admin' LIMIT 1")->fetchColumn();
    $viatura1   = $db->query("SELECT id FROM viaturas WHERE prefixo='GCM-01' LIMIT 1")->fetchColumn();
    $viatura2   = $db->query("SELECT id FROM viaturas WHERE prefixo='GCM-02' LIMIT 1")->fetchColumn();

    if ($operador && $inspetor) {
        $hoje   = date('Y-m-d');
        $amanha = date('Y-m-d', strtotime('+1 day'));
        $ontem  = date('Y-m-d', strtotime('-1 day'));
        $stmtE  = $db->prepare("INSERT INTO escalas (agente_id,viatura_id,data,turno,setor,criado_por) VALUES (?,?,?,?,?,?)");
        $stmtE->execute([$operador, $viatura1, $ontem,  'manha', 'Centro',    $admin]);
        $stmtE->execute([$operador, $viatura1, $hoje,   'manha', 'Centro',    $admin]);
        $stmtE->execute([$operador, $viatura2, $amanha, 'tarde', 'Zona Norte',$admin]);
        $stmtE->execute([$inspetor, $viatura2, $hoje,   'tarde', 'Zona Sul',  $admin]);
        $stmtE->execute([$inspetor, null,      $amanha, 'manha', 'Administrativo', $admin]);
        $ok[] = "<i class='fa-solid fa-check'></i> 5 escalas de teste criadas";
    }
}

// ======= VÍTIMAS MARIA DA PENHA =======
$checkVitimas = $db->query("SELECT COUNT(*) FROM vitimas_penha")->fetchColumn();
if ($checkVitimas == 0) {
    $admin = $admin ?? $db->query("SELECT id FROM usuarios WHERE perfil='admin' LIMIT 1")->fetchColumn();
    $db->exec("INSERT INTO vitimas_penha (nome,cpf,data_nascimento,telefone,telefone_emergencia,nome_emergencia,prioridade,endereco,bairro,nome_agressor,vinculo_agressor,ativa,cadastrado_por) VALUES
        ('Maria Aparecida Santos','111.222.333-44','1985-03-15','(17)99111-2233','(17)99444-5566','João Santos',1,'Rua das Rosas, 45','Centro','Carlos Pereira','Ex-companheiro',1,$admin),
        ('Ana Paula Ferreira','222.333.444-55','1990-07-22','(17)99222-3344','(17)99555-6677','Rita Ferreira',2,'Av. Brasil, 200','Jardim América','Marcos Oliveira','Marido',1,$admin),
        ('Luciana Costa','333.444.555-66','1978-11-08','(17)99333-4455',NULL,NULL,3,'Rua São Paulo, 78','Vila Nova','Pedro Costa','Ex-namorado',1,$admin)");
    $ok[] = "<i class='fa-solid fa-check'></i> 3 vítimas Maria da Penha de teste criadas";

    // Medidas protetivas
    $vitima1 = $db->query("SELECT id FROM vitimas_penha WHERE cpf='111.222.333-44' LIMIT 1")->fetchColumn();
    $vitima2 = $db->query("SELECT id FROM vitimas_penha WHERE cpf='222.333.444-55' LIMIT 1")->fetchColumn();
    if ($vitima1 && $vitima2) {
        $db->exec("INSERT INTO medidas_protetivas (vitima_id,tipo,numero_processo,data_inicio,data_vencimento,status,criado_por) VALUES
            ($vitima1,'Proibição de aproximação','0001234-56.2025.8.26.0000','2025-06-01','2026-06-01','vigente',$admin),
            ($vitima1,'Afastamento do lar','0001234-56.2025.8.26.0000','2025-06-01','2026-06-01','vigente',$admin),
            ($vitima2,'Proibição de contato','0002345-67.2025.8.26.0000','2025-09-15','2026-03-15','vencida',$admin)");
        $ok[] = "<i class='fa-solid fa-check'></i> Medidas protetivas de teste criadas";
    }
}

// ======= PROCESSOS CORREGEDORIA =======
$checkProc = $db->query("SELECT COUNT(*) FROM processos_corregedoria")->fetchColumn();
if ($checkProc == 0) {
    $admin    = $admin ?? $db->query("SELECT id FROM usuarios WHERE perfil='admin' LIMIT 1")->fetchColumn();
    $operador = $operador ?? $db->query("SELECT id FROM usuarios WHERE username='operador.teste' LIMIT 1")->fetchColumn();
    if ($admin) {
        $db->exec("INSERT INTO processos_corregedoria (numero,tipo,investigado_id,fato_descricao,data_abertura,prazo_legal,status,criado_por) VALUES
            ('CORR-2025-001','sindicancia',$operador,'Apurar suposto desrespeito a superior hierárquico durante operação no centro da cidade.','2025-10-01','2026-01-01','em_andamento',$admin),
            ('CORR-2025-002','investigacao_preliminar',NULL,'Verificar denúncia anônima de uso irregular de viatura fora de expediente.','2025-11-15','2026-02-15','aberto',$admin)");
        $ok[] = "<i class='fa-solid fa-check'></i> 2 processos de corregedoria de teste criados";
    }
}

$db->exec("SET foreign_key_checks = 1");

// ======= RELATÓRIO =======
$total = count($ok) + count($erros);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>SIG-GCM — Setup</title>
  <style>
    body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
    h1 { color: #3b82f6; margin-bottom: 1.5rem; }
    .ok  { color: #22c55e; margin: .25rem 0; }
    .err { color: #ef4444; margin: .25rem 0; }
    .box { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; }
    .summary { font-size: 1.2rem; font-weight: bold; margin-top: 1.5rem; }
    .warn { background: #78350f; border: 1px solid #f59e0b; color: #fde68a; padding: 1rem; border-radius: 8px; margin-top: 1rem; }
    a.btn { display: inline-block; background: #1d4ed8; color: white; padding: .75rem 1.5rem; border-radius: 6px; text-decoration: none; margin-top: 1rem; }
  </style>
</head>
<body>
<h1>SIG-GCM — Instalação do Banco de Dados</h1>
<div class="box">
  <?php foreach ($ok as $m): ?><div class="ok"><?= htmlspecialchars($m) ?></div><?php endforeach; ?>
  <?php foreach ($erros as $m): ?><div class="err"><?= htmlspecialchars($m) ?></div><?php endforeach; ?>
</div>
<div class="summary">
  <?php if (empty($erros)): ?>
    <i class='fa-solid fa-check'></i> Instalação concluída com sucesso! <?= count($ok) ?> operações realizadas.
    <br><br><a href="/" class="btn">Ir para o Sistema</a>
  <?php else: ?>
    <i class='fa-solid fa-triangle-exclamation'></i> Concluído com <?= count($erros) ?> erro(s). Verifique acima.
  <?php endif; ?>
</div>
<div class="warn" style="margin-top:1.5rem">
  <i class='fa-solid fa-triangle-exclamation'></i> <strong>IMPORTANTE:</strong> Apague ou renomeie este arquivo <code>setup.php</code> após a instalação por segurança!
</div>
</body>
</html>
