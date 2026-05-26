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

-- ============================================================
-- DADOS DE SEEDING: Restaurante e Cardápio Completo (Premium)
-- ============================================================

-- 1. Restaurante Premium Padrão
INSERT INTO restaurantes (id, utilizador_id, nome, descricao, endereco, cidade, telefone, email, imagem, banner, categoria, taxa_entrega, tempo_entrega, avaliacao_media, total_avaliacoes, ativo, aberto)
VALUES (
    'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11',
    (SELECT id FROM utilizadores WHERE email = 'admin@foodexpress.com' LIMIT 1),
    'FoodExpress Premium',
    'Os melhores pratos de Luanda, confecionados com ingredientes frescos e entregues em tempo recorde à sua porta.',
    'Avenida Comandante Gika, 150',
    'Luanda',
    '+244 923 000 111',
    'premium@foodexpress.ao',
    'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&auto=format&fit=crop&q=80',
    'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&auto=format&fit=crop&q=80',
    'Internacional',
    800.00,
    30,
    4.9,
    145,
    TRUE,
    TRUE
);

-- 2. Categorias de Pratos
INSERT INTO categorias_pratos (id, restaurante_id, nome, ordem) VALUES
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a01', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Pizza', 1),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a02', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Burgers', 2),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a03', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Massas', 3),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a04', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Saladas', 4),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a05', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Sobremesas', 5),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a06', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Bebidas', 6);

-- 3. Pratos (24 pratos deliciosos com imagens do Unsplash)
INSERT INTO pratos (restaurante_id, categoria_id, nome, descricao, preco, imagem, disponivel, destaque) VALUES
-- PIZZAS (Categoria 01)
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a01', 'Pizza Margherita', 'Molho de tomate, mozzarella fresca e manjericão', 4500.00, 'https://images.unsplash.com/photo-1604382355076-af4b0eb60143?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a01', 'Pizza Pepperoni', 'Pepperoni fatiado, queijo mozzarella e orégãos', 5500.00, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a01', 'Pizza Vegetariana', 'Pimentos, cogumelos, azeitonas e queijo', 5000.00, 'https://images.unsplash.com/photo-1571407970349-bc81e7e96d47?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a01', 'Pizza 4 Queijos', 'Mozzarella, gorgonzola, parmesão e gouda', 6000.00, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a01', 'Pizza Frango', 'Frango grelhado, pimentos e creme de alho', 5800.00, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),

-- BURGERS (Categoria 02)
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a02', 'Classic Burger', 'Carne 180g, alface, tomate, queijo cheddar', 3500.00, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a02', 'BBQ Burger', 'Dupla carne, bacon crocante e molho BBQ', 4800.00, 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a02', 'Frango Crispy', 'Frango crocante, coleslaw e pickles', 4200.00, 'https://images.unsplash.com/photo-1625813506062-0aeb1d7a094b?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a02', 'Double Smash', 'Dois smash burgers com queijo americano', 5500.00, 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),

-- MASSAS (Categoria 03)
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a03', 'Pasta Carbonara', 'Spaghetti, pancetta, ovo e parmesão', 4000.00, 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a03', 'Bolonhesa Clássica', 'Molho bolonhesa lento com tagliatelle', 3800.00, 'https://images.unsplash.com/photo-1546549032-9571cd6b27df?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a03', 'Penne ao Pesto', 'Penne com pesto de manjericão e pinhões', 3900.00, 'https://images.unsplash.com/photo-1484156818044-c040038b0719?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a03', 'Lasanha de Carne', 'Lasanha italiana com carne e béchamel', 4500.00, 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),

-- SALADAS (Categoria 04)
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a04', 'Salada Caesar', 'Alface romana, croutons, parmesão e molho Caesar', 2800.00, 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a04', 'Salada Grega', 'Tomate, pepino, azeitonas, feta e orégãos', 2900.00, 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a04', 'Salada Tropical', 'Frango, manga, abacate e vinagrete de lima', 3200.00, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),

-- SOBREMESAS (Categoria 05)
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a05', 'Cheesecake', 'Cheesecake de frutos vermelhos cremoso', 2000.00, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a05', 'Brownie Quente', 'Brownie de chocolate com gelado de baunilha', 2200.00, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80', TRUE, TRUE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a05', 'Gelado Artesanal', 'Seleção de 3 bolas de gelado artesanal', 1800.00, 'https://images.unsplash.com/photo-1501443762994-82bd5dace89a?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a05', 'Pudim Português', 'Pudim flan tradicional com caramelo', 1500.00, 'https://images.unsplash.com/photo-1528975604071-b4dc52a2d18c?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),

-- BEBIDAS (Categoria 06)
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a06', 'Sumo Natural', 'Sumo de laranja espremido na hora', 1200.00, 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a06', 'Coca-Cola', 'Coca-Cola gelada 33cl', 800.00, 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a06', 'Água com Gás', 'Água mineral com gás 50cl', 600.00, 'https://images.unsplash.com/photo-1548813730-e841804a1a08?w=600&auto=format&fit=crop&q=80', TRUE, FALSE),
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a06', 'Smoothie Frutas', 'Manga, morango e banana batidos', 1600.00, 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600&auto=format&fit=crop&q=80', TRUE, TRUE);
