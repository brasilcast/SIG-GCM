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
// SIG-GCM — Migrations (Auto-criação de Tabelas)
// Acesse: /migrations/setup.php para criar o banco
// =============================================

require_once dirname(__DIR__) . '/config/app.php';

// ---- Conexão direta para criar o banco se não existir ----
function getSetupPDO(): PDO {
    require_once dirname(__DIR__) . '/config/database.php';
    // Conectar sem especificar banco para poder criá-lo
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
    ]);
    // Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    return $pdo;
}

$errors   = [];
$success  = [];

try {
    $pdo = getSetupPDO();

    // ====================================================
    // ARRAY DE MIGRATIONS — ORDEM RESPEITA FOREIGN KEYS
    // ====================================================
    $migrations = [];

    // ----------------------------
    // 1. USUÁRIOS E ACESSO
    // ----------------------------
    $migrations['usuarios'] = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            nome            VARCHAR(200)    NOT NULL,
            username        VARCHAR(100)    UNIQUE NOT NULL,
            senha           VARCHAR(255)    NOT NULL,
            perfil          ENUM('operador','inspetor','subcomandante','comandante','corregedor','admin') NOT NULL DEFAULT 'operador',
            matricula       VARCHAR(50),
            cpf             VARCHAR(20),
            rg              VARCHAR(30),
            data_nascimento DATE,
            estado_civil    ENUM('solteiro','casado','divorciado','viuvo','uniao_estavel'),
            telefone        VARCHAR(20),
            telefone_emergencia VARCHAR(20),
            nome_emergencia VARCHAR(200),
            email           VARCHAR(200),
            foto            VARCHAR(255),
            cargo           VARCHAR(100),
            classe          VARCHAR(100),
            data_admissao   DATE,
            data_posse      DATE,
            turno_padrao    ENUM('manha','tarde','noite','plantao_24h'),
            endereco        TEXT,
            senha_temporaria TINYINT(1)     DEFAULT 1,
            ativo           TINYINT(1)      DEFAULT 1,
            ultimo_login    DATETIME,
            created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['permissoes_usuario'] = "
        CREATE TABLE IF NOT EXISTS permissoes_usuario (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id  INT NOT NULL,
            recurso     VARCHAR(150) NOT NULL,
            permitido   TINYINT(1) DEFAULT 1,
            definido_por INT,
            definido_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_usuario_recurso (usuario_id, recurso),
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['login_log'] = "
        CREATE TABLE IF NOT EXISTS login_log (
            id              BIGINT AUTO_INCREMENT PRIMARY KEY,
            usuario_id      INT,
            username_tentado VARCHAR(100),
            sucesso         TINYINT(1),
            ip              VARCHAR(50),
            latitude        DECIMAL(10,8),
            longitude       DECIMAL(11,8),
            cidade          VARCHAR(100),
            pais            VARCHAR(50),
            user_agent      TEXT,
            tentado_em      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_usuario_id  (usuario_id),
            INDEX idx_tentado_em  (tentado_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['audit_log'] = "
        CREATE TABLE IF NOT EXISTS audit_log (
            id          BIGINT AUTO_INCREMENT PRIMARY KEY,
            usuario_id  INT,
            acao        VARCHAR(50),
            tabela      VARCHAR(100),
            registro_id INT,
            dados_antes JSON,
            dados_depois JSON,
            ip          VARCHAR(50),
            user_agent  TEXT,
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_usuario    (usuario_id),
            INDEX idx_tabela     (tabela, registro_id),
            INDEX idx_criado_em  (criado_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 2. VIATURAS E FROTA
    // ----------------------------
    $migrations['viaturas'] = "
        CREATE TABLE IF NOT EXISTS viaturas (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            prefixo     VARCHAR(20) NOT NULL,
            placa       VARCHAR(20),
            modelo      VARCHAR(100),
            marca       VARCHAR(100),
            ano         INT,
            cor         VARCHAR(50),
            km_atual    DECIMAL(10,2) DEFAULT 0,
            status      ENUM('disponivel','em_servico','manutencao','inativa') DEFAULT 'disponivel',
            observacoes TEXT,
            ativa       TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['checklists_viatura'] = "
        CREATE TABLE IF NOT EXISTS checklists_viatura (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            viatura_id      INT NOT NULL,
            agente_id       INT NOT NULL,
            tipo            ENUM('saida','retorno') NOT NULL,
            km              DECIMAL(10,2),
            -- Exterior
            pneus           ENUM('ok','atencao','problema') DEFAULT 'ok',
            luzes           ENUM('ok','atencao','problema') DEFAULT 'ok',
            lataria         ENUM('ok','atencao','problema') DEFAULT 'ok',
            vidros          ENUM('ok','atencao','problema') DEFAULT 'ok',
            -- Sinalização
            giroflex        ENUM('ok','atencao','problema') DEFAULT 'ok',
            sirene          ENUM('ok','atencao','problema') DEFAULT 'ok',
            -- Itens Obrigatórios
            estepe          ENUM('ok','atencao','problema') DEFAULT 'ok',
            extintor        ENUM('ok','atencao','problema') DEFAULT 'ok',
            documentos      ENUM('ok','atencao','problema') DEFAULT 'ok',
            triangulo       ENUM('ok','atencao','problema') DEFAULT 'ok',
            -- Níveis
            oleo            ENUM('ok','atencao','problema') DEFAULT 'ok',
            agua            ENUM('ok','atencao','problema') DEFAULT 'ok',
            combustivel     ENUM('ok','atencao','problema') DEFAULT 'ok',
            -- EPI
            colete          ENUM('ok','atencao','problema') DEFAULT 'ok',
            algemas         ENUM('ok','atencao','problema') DEFAULT 'ok',
            radio           ENUM('ok','atencao','problema') DEFAULT 'ok',
            -- Geral
            observacoes     TEXT,
            realizado_em    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (viatura_id) REFERENCES viaturas(id),
            FOREIGN KEY (agente_id)  REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['checklist_fotos'] = "
        CREATE TABLE IF NOT EXISTS checklist_fotos (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            checklist_id    INT NOT NULL,
            caminho         VARCHAR(255) NOT NULL,
            descricao       VARCHAR(200),
            criado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (checklist_id) REFERENCES checklists_viatura(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['abastecimentos'] = "
        CREATE TABLE IF NOT EXISTS abastecimentos (
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
            criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (viatura_id) REFERENCES viaturas(id),
            FOREIGN KEY (agente_id)  REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['manutencoes_viatura'] = "
        CREATE TABLE IF NOT EXISTS manutencoes_viatura (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            viatura_id      INT NOT NULL,
            tipo            ENUM('preventiva','corretiva') NOT NULL,
            descricao       TEXT,
            fornecedor      VARCHAR(200),
            custo           DECIMAL(10,2),
            data_entrada    DATE,
            data_prevista   DATE,
            data_saida      DATE,
            km_entrada      DECIMAL(10,2),
            status          ENUM('aguardando','em_andamento','concluida') DEFAULT 'aguardando',
            observacoes     TEXT,
            registrado_por  INT,
            FOREIGN KEY (viatura_id) REFERENCES viaturas(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 3. OPERACIONAL — TALÕES
    // ----------------------------
    $migrations['naturezas_ocorrencia'] = "
        CREATE TABLE IF NOT EXISTS naturezas_ocorrencia (
            id      INT AUTO_INCREMENT PRIMARY KEY,
            grupo   VARCHAR(100),
            nome    VARCHAR(200) NOT NULL,
            codigo  VARCHAR(20),
            ativo   TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['taloes'] = "
        CREATE TABLE IF NOT EXISTS taloes (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            numero          VARCHAR(30) UNIQUE NOT NULL,
            status          ENUM('aberto','em_atendimento','fechado') DEFAULT 'aberto',
            natureza_id     INT,
            viatura_id      INT,
            agente_id       INT,
            -- Data/hora EDITÁVEL (não apenas NOW())
            data_hora_abertura  DATETIME NOT NULL,
            data_hora_fechamento DATETIME,
            km_inicial      DECIMAL(10,2),
            km_final        DECIMAL(10,2),
            latitude        DECIMAL(10,8),
            longitude       DECIMAL(11,8),
            endereco        TEXT,
            bairro          VARCHAR(100),
            referencia      VARCHAR(200),
            narrativa       TEXT,
            encaminhamento  TEXT,
            medidas_tomadas TEXT,
            observacoes_internas TEXT,
            criado_por      INT,
            updated_by      INT,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (natureza_id) REFERENCES naturezas_ocorrencia(id),
            FOREIGN KEY (viatura_id)  REFERENCES viaturas(id),
            FOREIGN KEY (agente_id)   REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['taloes_agentes'] = "
        CREATE TABLE IF NOT EXISTS taloes_agentes (
            talao_id    INT,
            agente_id   INT,
            PRIMARY KEY (talao_id, agente_id),
            FOREIGN KEY (talao_id)  REFERENCES taloes(id) ON DELETE CASCADE,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['taloes_envolvidos'] = "
        CREATE TABLE IF NOT EXISTS taloes_envolvidos (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            talao_id    INT NOT NULL,
            tipo        ENUM('vitima','autor','testemunha','outro') NOT NULL,
            nome        VARCHAR(200),
            cpf         VARCHAR(20),
            data_nascimento DATE,
            telefone    VARCHAR(20),
            endereco    TEXT,
            observacoes TEXT,
            FOREIGN KEY (talao_id) REFERENCES taloes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['taloes_fotos'] = "
        CREATE TABLE IF NOT EXISTS taloes_fotos (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            talao_id    INT NOT NULL,
            caminho     VARCHAR(255) NOT NULL,
            descricao   VARCHAR(200),
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (talao_id) REFERENCES taloes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['taloes_atualizacoes'] = "
        CREATE TABLE IF NOT EXISTS taloes_atualizacoes (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            talao_id    INT NOT NULL,
            agente_id   INT,
            texto       TEXT NOT NULL,
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (talao_id)  REFERENCES taloes(id) ON DELETE CASCADE,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['templates_talao'] = "
        CREATE TABLE IF NOT EXISTS templates_talao (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            nome        VARCHAR(200) NOT NULL,
            conteudo    TEXT,
            grupo       VARCHAR(100),
            criado_por  INT,
            ativo       TINYINT(1) DEFAULT 1,
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (criado_por) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 4. ESCALAS E RH
    // ----------------------------
    $migrations['escalas'] = "
        CREATE TABLE IF NOT EXISTS escalas (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            agente_id   INT NOT NULL,
            viatura_id  INT,
            data        DATE NOT NULL,
            turno       ENUM('manha','tarde','noite','plantao_24h') NOT NULL,
            setor       VARCHAR(100),
            funcao      VARCHAR(100),
            observacoes TEXT,
            criado_por  INT,
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (agente_id)  REFERENCES usuarios(id),
            FOREIGN KEY (viatura_id) REFERENCES viaturas(id),
            INDEX idx_agente_data (agente_id, data)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['trocas_plantao'] = "
        CREATE TABLE IF NOT EXISTS trocas_plantao (
            id                INT AUTO_INCREMENT PRIMARY KEY,
            escala_origem_id  INT NOT NULL,
            escala_destino_id INT NOT NULL,
            solicitante_id    INT NOT NULL,
            substituto_id     INT NOT NULL,
            status            ENUM('pendente','aprovada','negada') DEFAULT 'pendente',
            motivo            TEXT,
            resposta          TEXT,
            aprovado_por      INT,
            aprovado_em       DATETIME,
            criado_em         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (escala_origem_id)  REFERENCES escalas(id),
            FOREIGN KEY (escala_destino_id) REFERENCES escalas(id),
            FOREIGN KEY (solicitante_id)    REFERENCES usuarios(id),
            FOREIGN KEY (substituto_id)     REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['banco_horas'] = "
        CREATE TABLE IF NOT EXISTS banco_horas (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            agente_id       INT NOT NULL,
            tipo            ENUM('credito','debito') NOT NULL,
            horas           DECIMAL(6,2) NOT NULL,
            motivo          VARCHAR(255),
            referencia_data DATE,
            registrado_por  INT,
            criado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['afastamentos'] = "
        CREATE TABLE IF NOT EXISTS afastamentos (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            agente_id       INT NOT NULL,
            tipo            ENUM('ferias','licenca_medica','licenca_especial','maternidade','paternidade','suspensao','outros') NOT NULL,
            data_inicio     DATE NOT NULL,
            data_fim        DATE NOT NULL,
            motivo          TEXT,
            documento       VARCHAR(255),
            status          ENUM('pendente','aprovado','negado') DEFAULT 'pendente',
            aprovado_por    INT,
            aprovado_em     DATETIME,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['advertencias_elogios'] = "
        CREATE TABLE IF NOT EXISTS advertencias_elogios (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            agente_id   INT NOT NULL,
            tipo        ENUM('advertencia_leve','advertencia_media','advertencia_grave','elogio','comenda') NOT NULL,
            descricao   TEXT NOT NULL,
            data_ocorrencia DATE,
            registrado_por INT,
            documento   VARCHAR(255),
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['treinamentos'] = "
        CREATE TABLE IF NOT EXISTS treinamentos (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            agente_id       INT NOT NULL,
            tipo            VARCHAR(100),
            descricao       VARCHAR(200) NOT NULL,
            instituicao     VARCHAR(200),
            data_inicio     DATE,
            data_fim        DATE,
            carga_horaria   INT,
            nota            DECIMAL(5,2),
            aprovado        TINYINT(1),
            certificado     VARCHAR(255),
            validade_ate    DATE,
            observacoes     TEXT,
            registrado_por  INT,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 5. MATERIAIS E ARMAMENTO
    // ----------------------------
    $migrations['armamentos'] = "
        CREATE TABLE IF NOT EXISTS armamentos (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            tipo            VARCHAR(100),
            modelo          VARCHAR(200),
            calibre         VARCHAR(50),
            numero_serie    VARCHAR(100) UNIQUE,
            tombamento      VARCHAR(50),
            status          ENUM('disponivel','acautelado','manutencao','inservivel') DEFAULT 'disponivel',
            observacoes     TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['acautelamentos'] = "
        CREATE TABLE IF NOT EXISTS acautelamentos (
            id                  INT AUTO_INCREMENT PRIMARY KEY,
            armamento_id        INT NOT NULL,
            agente_id           INT NOT NULL,
            data_entrega        DATETIME NOT NULL,
            data_devolucao      DATETIME,
            quantidade_municao  INT DEFAULT 0,
            entregue_por        INT,
            devolvido_a         INT,
            status              ENUM('ativo','devolvido') DEFAULT 'ativo',
            observacoes         TEXT,
            FOREIGN KEY (armamento_id) REFERENCES armamentos(id),
            FOREIGN KEY (agente_id)    REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['equipamentos'] = "
        CREATE TABLE IF NOT EXISTS equipamentos (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            tipo        VARCHAR(100) NOT NULL,
            descricao   VARCHAR(200),
            patrimonio  VARCHAR(50),
            estado      ENUM('bom','deteriorado','inservivel') DEFAULT 'bom',
            agente_id   INT,
            data_acautelamento DATE,
            observacoes TEXT,
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 6. RONDA ESCOLAR
    // ----------------------------
    $migrations['escolas'] = "
        CREATE TABLE IF NOT EXISTS escolas (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            nome        VARCHAR(200) NOT NULL,
            endereco    TEXT,
            bairro      VARCHAR(100),
            latitude    DECIMAL(10,8),
            longitude   DECIMAL(11,8),
            diretor     VARCHAR(200),
            telefone    VARCHAR(20),
            email       VARCHAR(200),
            turno       VARCHAR(100),
            observacoes TEXT,
            ativa       TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['rondas_escolares'] = "
        CREATE TABLE IF NOT EXISTS rondas_escolares (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            escola_id   INT NOT NULL,
            agente_id   INT NOT NULL,
            viatura_id  INT,
            latitude    DECIMAL(10,8),
            longitude   DECIMAL(11,8),
            foto        VARCHAR(255),
            observacoes TEXT,
            realizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (escola_id)  REFERENCES escolas(id),
            FOREIGN KEY (agente_id)  REFERENCES usuarios(id),
            FOREIGN KEY (viatura_id) REFERENCES viaturas(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['ocorrencias_escolares'] = "
        CREATE TABLE IF NOT EXISTS ocorrencias_escolares (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            escola_id   INT NOT NULL,
            agente_id   INT NOT NULL,
            tipo        VARCHAR(100),
            descricao   TEXT NOT NULL,
            encaminhamento TEXT,
            status      ENUM('aberto','em_andamento','encerrado') DEFAULT 'aberto',
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (escola_id) REFERENCES escolas(id),
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 7. MARIA DA PENHA
    // ----------------------------
    $migrations['vitimas_penha'] = "
        CREATE TABLE IF NOT EXISTS vitimas_penha (
            id                  INT AUTO_INCREMENT PRIMARY KEY,
            nome                VARCHAR(200) NOT NULL,
            cpf                 VARCHAR(20),
            data_nascimento     DATE,
            telefone            VARCHAR(20),
            telefone_emergencia VARCHAR(20),
            nome_emergencia     VARCHAR(200),
            parentesco_emergencia VARCHAR(100),
            endereco            TEXT,
            bairro              VARCHAR(100),
            latitude            DECIMAL(10,8),
            longitude           DECIMAL(11,8),
            prioridade          TINYINT(1) DEFAULT 3 COMMENT '1=Máxima, 5=Baixa',
            nome_agressor       VARCHAR(200),
            cpf_agressor        VARCHAR(20),
            vinculo_agressor    VARCHAR(100),
            historico_violencia TEXT,
            observacoes         TEXT,
            foto                VARCHAR(255),
            ativa               TINYINT(1) DEFAULT 1,
            cadastrado_por      INT,
            criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['medidas_protetivas'] = "
        CREATE TABLE IF NOT EXISTS medidas_protetivas (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            vitima_id       INT NOT NULL,
            numero_processo VARCHAR(100),
            vara_judicial   VARCHAR(200),
            tipo            VARCHAR(200),
            restricoes      TEXT,
            data_inicio     DATE,
            data_vencimento DATE NOT NULL,
            status          ENUM('vigente','vencida','renovada','arquivada') DEFAULT 'vigente',
            observacoes     TEXT,
            documento       VARCHAR(255),
            cadastrado_por  INT,
            FOREIGN KEY (vitima_id) REFERENCES vitimas_penha(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['visitas_penha'] = "
        CREATE TABLE IF NOT EXISTS visitas_penha (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            vitima_id   INT NOT NULL,
            agente_id   INT NOT NULL,
            viatura_id  INT,
            latitude    DECIMAL(10,8),
            longitude   DECIMAL(11,8),
            situacao    ENUM('normal','necessita_atencao','urgente') DEFAULT 'normal',
            relato      TEXT,
            foto        VARCHAR(255),
            realizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (vitima_id)  REFERENCES vitimas_penha(id),
            FOREIGN KEY (agente_id)  REFERENCES usuarios(id),
            FOREIGN KEY (viatura_id) REFERENCES viaturas(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['ocorrencias_penha'] = "
        CREATE TABLE IF NOT EXISTS ocorrencias_penha (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            vitima_id   INT NOT NULL,
            agente_id   INT NOT NULL,
            descricao   TEXT NOT NULL,
            tipo        VARCHAR(100),
            bo_numero   VARCHAR(50),
            encaminhamento TEXT,
            criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (vitima_id) REFERENCES vitimas_penha(id),
            FOREIGN KEY (agente_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 8. CORREGEDORIA
    // ----------------------------
    $migrations['processos_corregedoria'] = "
        CREATE TABLE IF NOT EXISTS processos_corregedoria (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            numero          VARCHAR(50) UNIQUE NOT NULL,
            tipo            ENUM('sindicancia','pad','investigacao_preliminar') NOT NULL,
            investigado_id  INT,
            fato_descricao  TEXT,
            data_abertura   DATE NOT NULL,
            prazo_legal     DATE,
            status          ENUM('aberto','em_andamento','concluido','arquivado') DEFAULT 'aberto',
            penalidade      VARCHAR(200),
            conclusao       TEXT,
            documento_final VARCHAR(255),
            criado_por      INT,
            criado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (investigado_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['processos_movimentacoes'] = "
        CREATE TABLE IF NOT EXISTS processos_movimentacoes (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            processo_id     INT NOT NULL,
            descricao       TEXT NOT NULL,
            documento       VARCHAR(255),
            registrado_por  INT,
            criado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (processo_id)   REFERENCES processos_corregedoria(id),
            FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ----------------------------
    // 9. COMUNICADOS E ALERTAS
    // ----------------------------
    $migrations['comunicados'] = "
        CREATE TABLE IF NOT EXISTS comunicados (
            id                  INT AUTO_INCREMENT PRIMARY KEY,
            titulo              VARCHAR(200) NOT NULL,
            conteudo            TEXT,
            tipo                ENUM('informacao','atencao','urgente') DEFAULT 'informacao',
            destino             ENUM('todos','perfil','usuario') DEFAULT 'todos',
            perfil_destino      VARCHAR(50),
            usuario_destino_id  INT,
            requer_confirmacao  TINYINT(1) DEFAULT 0,
            valido_ate          DATE,
            anexo               VARCHAR(255),
            criado_por          INT,
            criado_em           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (criado_por) REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $migrations['comunicados_leitura'] = "
        CREATE TABLE IF NOT EXISTS comunicados_leitura (
            id              INT AUTO_INCREMENT PRIMARY KEY,
            comunicado_id   INT NOT NULL,
            usuario_id      INT NOT NULL,
            lido_em         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            confirmado      TINYINT(1) DEFAULT 0,
            confirmado_em   DATETIME,
            UNIQUE KEY uniq_comunicado_usuario (comunicado_id, usuario_id),
            FOREIGN KEY (comunicado_id) REFERENCES comunicados(id) ON DELETE CASCADE,
            FOREIGN KEY (usuario_id)    REFERENCES usuarios(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    // ====================================================
    // EXECUTAR MIGRATIONS
    // ====================================================
    foreach ($migrations as $tabela => $sql) {
        try {
            $pdo->exec($sql);
            $success[] = $tabela;
        } catch (PDOException $e) {
            $errors[] = "ERRO em [{$tabela}]: " . $e->getMessage();
        }
    }

    // ====================================================
    // SEED: Criar Usuário Admin Padrão (se não existir)
    // ====================================================
    $adminExists = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE username = 'admin'")->fetchColumn();
    if (!$adminExists) {
        $hash = password_hash('Admin@2025', PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->exec("INSERT INTO usuarios (nome, username, senha, perfil, senha_temporaria, ativo)
                    VALUES ('Administrador do Sistema', 'admin', '$hash', 'admin', 0, 1)");
        $success[] = '[SEED] Usuário admin criado (senha: Admin@2025 — altere imediatamente!)';
    }

    // ====================================================
    // SEED: Usuários de Teste (um por perfil)
    // ====================================================
    $testUsers = [
        ['Operador Teste',      'operador.teste',      'operador'],
        ['Inspetor Teste',      'inspetor.teste',      'inspetor'],
        ['Subcomandante Teste', 'subcmd.teste',        'subcomandante'],
        ['Comandante Teste',    'comandante.teste',    'comandante'],
        ['Corregedor Teste',    'corregedor.teste',    'corregedor'],
    ];
    $senhaTesteHash = password_hash('Teste@2025', PASSWORD_BCRYPT, ['cost' => 12]);
    $insU = $pdo->prepare("INSERT IGNORE INTO usuarios (nome, username, senha, perfil, matricula, cargo, senha_temporaria, ativo)
                           VALUES (?, ?, ?, ?, ?, ?, 0, 1)");
    foreach ($testUsers as $tu) {
        $mat = 'TEST-' . strtoupper(substr($tu[2], 0, 3)) . '01';
        $cargo = ucfirst($tu[2]);
        $insU->execute([$tu[0], $tu[1], $senhaTesteHash, $tu[2], $mat, $cargo]);
    }
    $success[] = '[SEED] Usuários de teste criados (senha: Teste@2025)';

    // Seed: Naturezas de Ocorrência básicas
    $natCount = $pdo->query("SELECT COUNT(*) FROM naturezas_ocorrencia")->fetchColumn();
    if (!$natCount) {
        $naturezas = [
            ['Crimes contra Pessoa', 'Lesão Corporal'],
            ['Crimes contra Pessoa', 'Ameaça'],
            ['Crimes contra Patrimônio', 'Furto'],
            ['Crimes contra Patrimônio', 'Roubo'],
            ['Crimes contra Patrimônio', 'Vandalismo'],
            ['Perturbação', 'Perturbação do Sossego'],
            ['Perturbação', 'Briga entre vizinhos'],
            ['Trânsito', 'Acidente de Trânsito'],
            ['Trânsito', 'Veículo abandonado'],
            ['Administrativo', 'Apoio a outras forças'],
            ['Administrativo', 'Policiamento Preventivo'],
            ['Maria da Penha', 'Violência Doméstica'],
        ];
        $ins = $pdo->prepare("INSERT INTO naturezas_ocorrencia (grupo, nome) VALUES (?, ?)");
        foreach ($naturezas as $n) { $ins->execute($n); }
        $success[] = '[SEED] Naturezas de ocorrência criadas';
    }

} catch (Exception $e) {
    $errors[] = 'ERRO FATAL: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup — SIG-GCM</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .card { background: #1e293b; border-radius: 12px; padding: 2rem; max-width: 800px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
    h1 { font-size: 1.5rem; margin-bottom: 0.5rem; color: #38bdf8; }
    p { color: #94a3b8; margin-bottom: 1.5rem; }
    .item { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #334155; font-size: 0.9rem; }
    .item:last-child { border-bottom: none; }
    .ok { color: #4ade80; }
    .err { color: #f87171; }
    .badge { padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-ok { background: #14532d; color: #4ade80; }
    .badge-err { background: #7f1d1d; color: #f87171; }
    .summary { margin-top: 1.5rem; padding: 1rem; border-radius: 8px; background: #0f172a; }
    a.btn { display: inline-block; margin-top: 1.5rem; padding: 0.75rem 1.5rem; background: #3b82f6; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background .2s; }
    a.btn:hover { background: #2563eb; }
</style>
</head>
<body>
<div class="card">
    <h1><i class='fa-solid fa-shield-halved'></i> SIG-GCM — Setup do Banco de Dados</h1>
    <p>Criando tabelas e dados iniciais no banco <strong><?= DB_NAME ?></strong>...</p>

    <?php foreach ($success as $item): ?>
        <div class="item">
            <span class="badge badge-ok">OK</span>
            <span class="ok"><?= htmlspecialchars($item) ?></span>
        </div>
    <?php endforeach; ?>

    <?php foreach ($errors as $err): ?>
        <div class="item">
            <span class="badge badge-err">ERRO</span>
            <span class="err"><?= htmlspecialchars($err) ?></span>
        </div>
    <?php endforeach; ?>

    <div class="summary">
        <strong><i class='fa-solid fa-check'></i> <?= count($success) ?> operações concluídas</strong>
        <?php if ($errors): ?>
            &nbsp; <strong style="color:#f87171"><i class='fa-solid fa-xmark'></i> <?= count($errors) ?> erros</strong>
        <?php endif; ?>
    </div>

    <?php if (!$errors): ?>
        <a class="btn" href="../"><i class='fa-solid fa-key'></i> Ir para o Login</a>
    <?php endif; ?>
</div>
</body>
</html>
