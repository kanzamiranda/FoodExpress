-- ============================================================
-- FoodExpress - Schema Completo PostgreSQL
-- ============================================================

-- Extensões
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ============================================================
-- TABELA: utilizadores
-- ============================================================
CREATE TABLE utilizadores (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nome        VARCHAR(150)        NOT NULL,
    email       VARCHAR(200)        NOT NULL UNIQUE,
    telefone    VARCHAR(20),
    senha       TEXT                NOT NULL,
    tipo        VARCHAR(20)         NOT NULL DEFAULT 'cliente'
                    CHECK (tipo IN ('cliente','restaurante','admin')),
    ativo       BOOLEAN             NOT NULL DEFAULT TRUE,
    avatar      TEXT,
    reset_token TEXT,
    reset_expira TIMESTAMPTZ,
    criado_em   TIMESTAMPTZ         NOT NULL DEFAULT NOW(),
    atualizado_em TIMESTAMPTZ       NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: restaurantes
-- ============================================================
CREATE TABLE restaurantes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilizador_id   UUID            NOT NULL REFERENCES utilizadores(id) ON DELETE CASCADE,
    nome            VARCHAR(200)    NOT NULL,
    descricao       TEXT,
    endereco        VARCHAR(300)    NOT NULL,
    cidade          VARCHAR(100),
    telefone        VARCHAR(20),
    email           VARCHAR(200),
    imagem          TEXT,
    banner          TEXT,
    categoria       VARCHAR(100),
    taxa_entrega    NUMERIC(8,2)    NOT NULL DEFAULT 0.00,
    tempo_entrega   INT,            -- minutos estimados
    avaliacao_media NUMERIC(3,2)    DEFAULT 0.00,
    total_avaliacoes INT            DEFAULT 0,
    ativo           BOOLEAN         NOT NULL DEFAULT TRUE,
    aberto          BOOLEAN         NOT NULL DEFAULT TRUE,
    criado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    atualizado_em   TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: categorias_pratos
-- ============================================================
CREATE TABLE categorias_pratos (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    restaurante_id  UUID            NOT NULL REFERENCES restaurantes(id) ON DELETE CASCADE,
    nome            VARCHAR(100)    NOT NULL,
    ordem           INT             DEFAULT 0,
    criado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: pratos
-- ============================================================
CREATE TABLE pratos (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    restaurante_id  UUID            NOT NULL REFERENCES restaurantes(id) ON DELETE CASCADE,
    categoria_id    UUID            REFERENCES categorias_pratos(id) ON DELETE SET NULL,
    nome            VARCHAR(200)    NOT NULL,
    descricao       TEXT,
    preco           NUMERIC(10,2)   NOT NULL,
    imagem          TEXT,
    disponivel      BOOLEAN         NOT NULL DEFAULT TRUE,
    destaque        BOOLEAN         NOT NULL DEFAULT FALSE,
    criado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    atualizado_em   TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: enderecos_entrega
-- ============================================================
CREATE TABLE enderecos_entrega (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilizador_id   UUID            NOT NULL REFERENCES utilizadores(id) ON DELETE CASCADE,
    label           VARCHAR(50)     DEFAULT 'Casa',
    rua             VARCHAR(200)    NOT NULL,
    numero          VARCHAR(20),
    complemento     VARCHAR(100),
    cidade          VARCHAR(100)    NOT NULL,
    codigo_postal   VARCHAR(20),
    principal       BOOLEAN         DEFAULT FALSE,
    criado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: pedidos
-- ============================================================
CREATE TABLE pedidos (
    id                  UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilizador_id       UUID            NOT NULL REFERENCES utilizadores(id),
    restaurante_id      UUID            NOT NULL REFERENCES restaurantes(id),
    endereco_entrega_id UUID            REFERENCES enderecos_entrega(id),
    status              VARCHAR(30)     NOT NULL DEFAULT 'recebido'
                            CHECK (status IN ('recebido','a_preparar','a_caminho','entregue','cancelado')),
    total               NUMERIC(10,2)   NOT NULL,
    taxa_entrega        NUMERIC(8,2)    NOT NULL DEFAULT 0.00,
    notas               TEXT,
    metodo_pagamento    VARCHAR(50)     DEFAULT 'dinheiro',
    criado_em           TIMESTAMPTZ     NOT NULL DEFAULT NOW(),
    atualizado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: itens_pedido
-- ============================================================
CREATE TABLE itens_pedido (
    id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    pedido_id   UUID            NOT NULL REFERENCES pedidos(id) ON DELETE CASCADE,
    prato_id    UUID            NOT NULL REFERENCES pratos(id),
    nome_prato  VARCHAR(200)    NOT NULL,  -- snapshot do nome no momento da compra
    preco_unit  NUMERIC(10,2)   NOT NULL,  -- snapshot do preço
    quantidade  INT             NOT NULL CHECK (quantidade > 0),
    subtotal    NUMERIC(10,2)   NOT NULL
);

-- ============================================================
-- TABELA: avaliacoes
-- ============================================================
CREATE TABLE avaliacoes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilizador_id   UUID            NOT NULL REFERENCES utilizadores(id),
    restaurante_id  UUID            NOT NULL REFERENCES restaurantes(id),
    pedido_id       UUID            UNIQUE REFERENCES pedidos(id),
    nota            INT             NOT NULL CHECK (nota BETWEEN 1 AND 5),
    comentario      TEXT,
    criado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: notificacoes
-- ============================================================
CREATE TABLE notificacoes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilizador_id   UUID            NOT NULL REFERENCES utilizadores(id) ON DELETE CASCADE,
    titulo          VARCHAR(200)    NOT NULL,
    mensagem        TEXT            NOT NULL,
    lida            BOOLEAN         NOT NULL DEFAULT FALSE,
    tipo            VARCHAR(50)     DEFAULT 'info',
    criado_em       TIMESTAMPTZ     NOT NULL DEFAULT NOW()
);

-- ============================================================
-- ÍNDICES
-- ============================================================
CREATE INDEX idx_restaurantes_utilizador  ON restaurantes(utilizador_id);
CREATE INDEX idx_pratos_restaurante       ON pratos(restaurante_id);
CREATE INDEX idx_pedidos_utilizador       ON pedidos(utilizador_id);
CREATE INDEX idx_pedidos_restaurante      ON pedidos(restaurante_id);
CREATE INDEX idx_pedidos_status           ON pedidos(status);
CREATE INDEX idx_itens_pedido             ON itens_pedido(pedido_id);
CREATE INDEX idx_avaliacoes_restaurante   ON avaliacoes(restaurante_id);
CREATE INDEX idx_notificacoes_utilizador  ON notificacoes(utilizador_id);

-- ============================================================
-- TRIGGERS: atualizado_em automático
-- ============================================================
CREATE OR REPLACE FUNCTION set_atualizado_em()
RETURNS TRIGGER AS $$
BEGIN
    NEW.atualizado_em = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_utilizadores_upd   BEFORE UPDATE ON utilizadores   FOR EACH ROW EXECUTE FUNCTION set_atualizado_em();
CREATE TRIGGER trg_restaurantes_upd   BEFORE UPDATE ON restaurantes   FOR EACH ROW EXECUTE FUNCTION set_atualizado_em();
CREATE TRIGGER trg_pratos_upd         BEFORE UPDATE ON pratos         FOR EACH ROW EXECUTE FUNCTION set_atualizado_em();
CREATE TRIGGER trg_pedidos_upd        BEFORE UPDATE ON pedidos        FOR EACH ROW EXECUTE FUNCTION set_atualizado_em();

-- ============================================================
-- TRIGGER: atualiza média de avaliações do restaurante
-- ============================================================
CREATE OR REPLACE FUNCTION atualizar_media_avaliacao()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE restaurantes
    SET avaliacao_media  = (SELECT ROUND(AVG(nota)::numeric, 2) FROM avaliacoes WHERE restaurante_id = NEW.restaurante_id),
        total_avaliacoes = (SELECT COUNT(*) FROM avaliacoes WHERE restaurante_id = NEW.restaurante_id)
    WHERE id = NEW.restaurante_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_avaliacao_insert
AFTER INSERT OR UPDATE ON avaliacoes
FOR EACH ROW EXECUTE FUNCTION atualizar_media_avaliacao();

-- ============================================================
-- DADOS INICIAIS: Admin padrão
-- ============================================================
INSERT INTO utilizadores (nome, email, senha, tipo)
VALUES (
    'Administrador',
    'admin@foodexpress.com',
    crypt('Admin@2024!', gen_salt('bf')),
    'admin'
);
