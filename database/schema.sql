-- =========================================
-- SIG-GCM — Sistema Integrado de Gestão
-- Guarda Civil Municipal
-- =========================================
-- SISTEMA DE TESTE — Projeto acadêmico (TG/TC)
-- Desenvolvido 100% por:
-- Autor   : Rafaela Cristina Alcântara da Silva
-- GitHub  : https://github.com/rafaela13c
-- LinkedIn: https://www.linkedin.com/in/rafaela-cas1308/
-- =========================================
-- Este sistema NÃO é oficial e NÃO será
-- utilizado pela GCM. Uso exclusivo para
-- fins de estudo acadêmico.
-- =========================================
-- =============================================
-- SIG-GCM — Script SQL Completo do Banco de Dados
-- Banco: u178632972_gcm73
-- Executar uma única vez no banco ZERADO
-- =============================================

SET NAMES utf8mb4;
SET time_zone = '-03:00';
SET foreign_key_checks = 0;

-- =============================================
-- USUÁRIOS E AUTENTICAÇÃO
-- =============================================

CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120) NOT NULL,
    matricula     VARCHAR(30)  UNIQUE,
    username      VARCHAR(60)  NOT NULL UNIQUE,
    email         VARCHAR(120) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    perfil        ENUM('operador','inspetor','subcomandante','comandante','corregedor','admin') NOT NULL DEFAULT 'operador',
    foto          VARCHAR(255),
    ativo         TINYINT(1)   NOT NULL DEFAULT 1,
    primeiro_acesso TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS permissoes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NOT NULL,
    recurso     VARCHAR(100) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_recurso (usuario_id, recurso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS login_log (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id       INT UNSIGNED NULL,
    username_tentado VARCHAR(100),
    ip               VARCHAR(45),
    user_agent       VARCHAR(255),
    sucesso          TINYINT(1) DEFAULT 0,
    tentado_em       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_id (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- AUDITORIA
-- =============================================

CREATE TABLE IF NOT EXISTS audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NULL,
    acao        ENUM('CREATE','UPDATE','DELETE','VIEW','EXPORT','LOGIN','LOGOUT') NOT NULL,
    tabela      VARCHAR(80),
    registro_id INT UNSIGNED NULL,
    dados_antes JSON,
    dados_depois JSON,
    ip          VARCHAR(45),
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tabela_registro (tabela, registro_id),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- NATUREZAS DE OCORRÊNCIA
-- =============================================

CREATE TABLE IF NOT EXISTS naturezas_ocorrencia (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grupo    VARCHAR(60) NOT NULL,
    nome     VARCHAR(100) NOT NULL,
    codigo   VARCHAR(20),
    ativa    TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TALÕES OPERACIONAIS
-- =============================================

CREATE TABLE IF NOT EXISTS ocorrencias (
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
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_agente (agente_id),
    INDEX idx_data (data_hora_abertura),
    FOREIGN KEY (natureza_id) REFERENCES naturezas_ocorrencia(id) ON DELETE SET NULL,
    FOREIGN KEY (agente_id)   REFERENCES usuarios(id),
    FOREIGN KEY (criado_por)  REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ocorrencias_agentes (
    ocorrencia_id INT UNSIGNED NOT NULL,
    usuario_id    INT UNSIGNED NOT NULL,
    PRIMARY KEY (ocorrencia_id, usuario_id),
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)    REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ocorrencias_envolvidos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT UNSIGNED NOT NULL,
    tipo          ENUM('autor','vitima','testemunha','outro') NOT NULL DEFAULT 'outro',
    nome          VARCHAR(120),
    cpf           VARCHAR(14),
    rg            VARCHAR(20),
    data_nascimento DATE,
    telefone      VARCHAR(20),
    endereco      VARCHAR(200),
    observacoes   TEXT,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ocorrencias_atualizacoes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT UNSIGNED NOT NULL,
    autor_id      INT UNSIGNED NOT NULL,
    texto         TEXT NOT NULL,
    criado_em     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id)      REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ocorrencias_fotos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT UNSIGNED NOT NULL,
    caminho       VARCHAR(255) NOT NULL,
    descricao     VARCHAR(200),
    criado_em     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- FROTA
-- =============================================

CREATE TABLE IF NOT EXISTS viaturas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prefixo         VARCHAR(20) NOT NULL UNIQUE,
    placa           VARCHAR(10),
    modelo          VARCHAR(80),
    marca           VARCHAR(60),
    ano             YEAR,
    chassi          VARCHAR(40),
    cor             VARCHAR(40),
    tipo            ENUM('viatura','moto','bicicleta','van','outros') DEFAULT 'viatura',
    combustivel     ENUM('gasolina','etanol','diesel','flex','gnv','eletrico') DEFAULT 'flex',
    km_atual        DECIMAL(10,1) DEFAULT 0,
    status          ENUM('disponivel','em_uso','manutencao','inativa') DEFAULT 'disponivel',
    ativa           TINYINT(1) DEFAULT 1,
    observacoes     TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS viaturas_checklists (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    viatura_id INT UNSIGNED NOT NULL,
    agente_id  INT UNSIGNED NOT NULL,
    km         DECIMAL(10,1),
    status_geral ENUM('ok','atencao','problema') DEFAULT 'ok',
    itens      JSON,
    observacoes TEXT,
    foto       VARCHAR(255),
    criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (viatura_id) REFERENCES viaturas(id) ON DELETE CASCADE,
    FOREIGN KEY (agente_id)  REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS viaturas_abastecimentos (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    viatura_id        INT UNSIGNED NOT NULL,
    agente_id         INT UNSIGNED NOT NULL,
    tipo_combustivel  ENUM('gasolina','etanol','diesel','gnv') DEFAULT 'gasolina',
    litros            DECIMAL(8,3),
    valor_total       DECIMAL(10,2),
    km_atual          DECIMAL(10,1),
    km_anterior       DECIMAL(10,1),
    consumo_calculado DECIMAL(8,3),
    posto             VARCHAR(100),
    data_abastecimento DATE NOT NULL,
    observacoes       TEXT,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (viatura_id) REFERENCES viaturas(id) ON DELETE CASCADE,
    FOREIGN KEY (agente_id)  REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- ESCALAS E RH
-- =============================================

CREATE TABLE IF NOT EXISTS escalas (
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
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_agente_data (agente_id, data),
    FOREIGN KEY (agente_id)  REFERENCES usuarios(id),
    FOREIGN KEY (viatura_id) REFERENCES viaturas(id) ON DELETE SET NULL,
    FOREIGN KEY (criado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trocas_plantao (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS banco_horas (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agente_id      INT UNSIGNED NOT NULL,
    tipo           ENUM('credito','debito') NOT NULL,
    horas          DECIMAL(5,2) NOT NULL,
    motivo         VARCHAR(255),
    referencia_data DATE,
    lancado_por    INT UNSIGNED,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agente_id)   REFERENCES usuarios(id),
    FOREIGN KEY (lancado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- RONDA ESCOLAR
-- =============================================

CREATE TABLE IF NOT EXISTS escolas (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome     VARCHAR(120) NOT NULL,
    endereco VARCHAR(200),
    bairro   VARCHAR(100),
    diretor  VARCHAR(120),
    telefone VARCHAR(20),
    turno    VARCHAR(40),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    ativa    TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rondas_escolares (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    escola_id   INT UNSIGNED NOT NULL,
    agente_id   INT UNSIGNED NOT NULL,
    viatura_id  INT UNSIGNED NULL,
    latitude    DECIMAL(10,8),
    longitude   DECIMAL(11,8),
    foto        VARCHAR(255),
    observacoes TEXT,
    realizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (escola_id)  REFERENCES escolas(id),
    FOREIGN KEY (agente_id)  REFERENCES usuarios(id),
    FOREIGN KEY (viatura_id) REFERENCES viaturas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ocorrencias_escolares (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    escola_id   INT UNSIGNED NOT NULL,
    agente_id   INT UNSIGNED NOT NULL,
    tipo        VARCHAR(80),
    descricao   TEXT,
    acao_tomada TEXT,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (escola_id) REFERENCES escolas(id),
    FOREIGN KEY (agente_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- MARIA DA PENHA
-- =============================================

CREATE TABLE IF NOT EXISTS vitimas_penha (
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
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_prioridade (prioridade),
    FOREIGN KEY (cadastrado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS medidas_protetivas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vitima_id       INT UNSIGNED NOT NULL,
    tipo            VARCHAR(120) NOT NULL,
    numero_processo VARCHAR(60),
    data_inicio     DATE,
    data_vencimento DATE NOT NULL,
    status          ENUM('vigente','vencida','renovada','arquivada') DEFAULT 'vigente',
    observacoes     TEXT,
    criado_por      INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vitima_id)  REFERENCES vitimas_penha(id) ON DELETE CASCADE,
    FOREIGN KEY (criado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS visitas_penha (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vitima_id    INT UNSIGNED NOT NULL,
    agente_id    INT UNSIGNED NOT NULL,
    situacao     ENUM('normal','necessita_atencao','urgente') DEFAULT 'normal',
    relato       TEXT,
    latitude     DECIMAL(10,8),
    longitude    DECIMAL(11,8),
    foto         VARCHAR(255),
    realizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vitima_id) REFERENCES vitimas_penha(id) ON DELETE CASCADE,
    FOREIGN KEY (agente_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CORREGEDORIA
-- =============================================

CREATE TABLE IF NOT EXISTS processos_corregedoria (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero           VARCHAR(60) NOT NULL UNIQUE,
    tipo             ENUM('sindicancia','pad','investigacao_preliminar') NOT NULL DEFAULT 'sindicancia',
    investigado_id   INT UNSIGNED NULL,
    fato_descricao   TEXT NOT NULL,
    data_abertura    DATE NOT NULL,
    prazo_legal      DATE NULL,
    status           ENUM('aberto','em_andamento','concluido','arquivado') DEFAULT 'aberto',
    conclusao        TEXT,
    criado_por       INT UNSIGNED NOT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (investigado_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (criado_por)     REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS processos_movimentacoes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    processo_id INT UNSIGNED NOT NULL,
    descricao   TEXT NOT NULL,
    registrado_por INT UNSIGNED,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (processo_id)    REFERENCES processos_corregedoria(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- COMUNICADOS
-- =============================================

CREATE TABLE IF NOT EXISTS comunicados (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo               VARCHAR(200) NOT NULL,
    conteudo             TEXT NOT NULL,
    tipo                 ENUM('informacao','atencao','urgente') DEFAULT 'informacao',
    destino              ENUM('todos','perfil','usuario') DEFAULT 'todos',
    perfil_destino       VARCHAR(40) NULL,
    usuario_destino_id   INT UNSIGNED NULL,
    requer_confirmacao   TINYINT(1) DEFAULT 0,
    valido_ate           DATE NULL,
    criado_por           INT UNSIGNED NOT NULL,
    criado_em            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criado_por)           REFERENCES usuarios(id),
    FOREIGN KEY (usuario_destino_id)   REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS comunicados_leitura (
    comunicado_id INT UNSIGNED NOT NULL,
    usuario_id    INT UNSIGNED NOT NULL,
    confirmado    TINYINT(1) DEFAULT 0,
    confirmado_em TIMESTAMP NULL,
    PRIMARY KEY (comunicado_id, usuario_id),
    FOREIGN KEY (comunicado_id) REFERENCES comunicados(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DADOS INICIAIS (SEED)
-- =============================================

-- Naturezas de ocorrência
INSERT INTO naturezas_ocorrencia (grupo, nome, codigo) VALUES
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
('Trânsito','Acidente de Trânsito','TR-01'),
('Trânsito','Veículo Abandonado','TR-02'),
('Outros','Abordagem Preventiva','OT-01'),
('Outros','Apoio a Órgãos Públicos','OT-02'),
('Outros','Encontro de Material Ilícito','OT-03');

-- Usuários de teste (senha: Teste@2025 / Admin@2025)
INSERT INTO usuarios (nome, matricula, username, perfil, password_hash, ativo, primeiro_acesso) VALUES
('Administrador do Sistema','0000','admin','admin','$2y$12$LKyI.h9z5oB0pHijzEQ5tOoLn5YxAlE4E6GVhf9MNGWCJYkKOmdHy',1,0),
('Carlos Operador','001','operador.teste','operador','$2y$12$LKyI.h9z5oB0pHijzEQ5tOoLn5YxAlE4E6GVhf9MNGWCJYkKOmdHy',1,1),
('Ana Inspetora','002','inspetor.teste','inspetor','$2y$12$LKyI.h9z5oB0pHijzEQ5tOoLn5YxAlE4E6GVhf9MNGWCJYkKOmdHy',1,1),
('Roberto Subcomandante','003','subcmd.teste','subcomandante','$2y$12$LKyI.h9z5oB0pHijzEQ5tOoLn5YxAlE4E6GVhf9MNGWCJYkKOmdHy',1,1),
('Maria Comandante','004','comandante.teste','comandante','$2y$12$LKyI.h9z5oB0pHijzEQ5tOoLn5YxAlE4E6GVhf9MNGWCJYkKOmdHy',1,1),
('José Corregedor','005','corregedor.teste','corregedor','$2y$12$LKyI.h9z5oB0pHijzEQ5tOoLn5YxAlE4E6GVhf9MNGWCJYkKOmdHy',1,1);
-- Senha hash acima = 'Teste@2025' para operador/inspetor/subcmd/comandante/corregedor
-- Admin usa 'Admin@2025' — gere um novo hash se necessário com password_hash()

SET foreign_key_checks = 1;
