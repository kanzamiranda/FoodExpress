-- ============================================================
-- FoodExpress — PostgreSQL Database Schema
-- Compatível com Neon Serverless PostgreSQL
-- ============================================================

-- Extensões
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ============================================================
-- TABELA: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(120)        NOT NULL,
    email       VARCHAR(200)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,   -- bcrypt hash
    role        VARCHAR(20)         NOT NULL DEFAULT 'client'
                                    CHECK (role IN ('client', 'admin')),
    phone       VARCHAR(30),
    avatar_url  TEXT,
    is_active   BOOLEAN             NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: categories
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(80)         NOT NULL UNIQUE,
    slug        VARCHAR(80)         NOT NULL UNIQUE,
    icon        VARCHAR(10)         NOT NULL DEFAULT '🍽️',
    sort_order  SMALLINT            NOT NULL DEFAULT 0,
    is_active   BOOLEAN             NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: products
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id              SERIAL PRIMARY KEY,
    category_id     INTEGER             NOT NULL REFERENCES categories(id) ON DELETE RESTRICT,
    name            VARCHAR(120)        NOT NULL,
    description     TEXT,
    price           NUMERIC(10,2)       NOT NULL CHECK (price >= 0),
    emoji           VARCHAR(10)         NOT NULL DEFAULT '🍽️',
    badge           VARCHAR(40),
    rating          NUMERIC(2,1)        DEFAULT 0.0 CHECK (rating BETWEEN 0 AND 5),
    prep_time       VARCHAR(20),
    is_available    BOOLEAN             NOT NULL DEFAULT TRUE,
    image_url       TEXT,
    created_at      TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: addresses
-- ============================================================
CREATE TABLE IF NOT EXISTS addresses (
    id          SERIAL PRIMARY KEY,
    user_id     INTEGER             NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    label       VARCHAR(60)         NOT NULL DEFAULT 'Casa',
    street      VARCHAR(200)        NOT NULL,
    city        VARCHAR(100)        NOT NULL,
    postal_code VARCHAR(20),
    country     VARCHAR(80)         NOT NULL DEFAULT 'Portugal',
    lat         DOUBLE PRECISION,
    lng         DOUBLE PRECISION,
    is_default  BOOLEAN             NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: orders
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id                  SERIAL PRIMARY KEY,
    user_id             INTEGER             NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    address_id          INTEGER             REFERENCES addresses(id) ON DELETE SET NULL,
    status              VARCHAR(30)         NOT NULL DEFAULT 'pending'
                                            CHECK (status IN (
                                                'pending', 'confirmed', 'preparing',
                                                'out_for_delivery', 'delivered', 'cancelled'
                                            )),
    payment_method      VARCHAR(30)         NOT NULL DEFAULT 'cash'
                                            CHECK (payment_method IN ('cash', 'card', 'transfer')),
    payment_status      VARCHAR(20)         NOT NULL DEFAULT 'unpaid'
                                            CHECK (payment_status IN ('unpaid', 'paid', 'refunded')),
    subtotal            NUMERIC(10,2)       NOT NULL DEFAULT 0,
    delivery_fee        NUMERIC(10,2)       NOT NULL DEFAULT 2.50,
    total               NUMERIC(10,2)       NOT NULL DEFAULT 0,
    notes               TEXT,
    estimated_delivery  TIMESTAMP WITH TIME ZONE,
    delivered_at        TIMESTAMP WITH TIME ZONE,
    created_at          TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: order_items
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id          SERIAL PRIMARY KEY,
    order_id    INTEGER             NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id  INTEGER             NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    quantity    SMALLINT            NOT NULL DEFAULT 1 CHECK (quantity > 0),
    unit_price  NUMERIC(10,2)       NOT NULL,
    subtotal    NUMERIC(10,2)       NOT NULL,
    created_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: refresh_tokens (JWT refresh)
-- ============================================================
CREATE TABLE IF NOT EXISTS refresh_tokens (
    id          SERIAL PRIMARY KEY,
    user_id     INTEGER             NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token       VARCHAR(512)        NOT NULL UNIQUE,
    expires_at  TIMESTAMP WITH TIME ZONE NOT NULL,
    revoked     BOOLEAN             NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- ============================================================
-- ÍNDICES
-- ============================================================
CREATE INDEX IF NOT EXISTS idx_products_category   ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_available  ON products(is_available);
CREATE INDEX IF NOT EXISTS idx_orders_user         ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status       ON orders(status);
CREATE INDEX IF NOT EXISTS idx_order_items_order   ON order_items(order_id);
CREATE INDEX IF NOT EXISTS idx_addresses_user      ON addresses(user_id);
CREATE INDEX IF NOT EXISTS idx_refresh_tokens_user ON refresh_tokens(user_id);

-- ============================================================
-- FUNÇÃO: auto-update de updated_at
-- ============================================================
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER trg_users_updated
    BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE OR REPLACE TRIGGER trg_products_updated
    BEFORE UPDATE ON products
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE OR REPLACE TRIGGER trg_orders_updated
    BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ============================================================
-- SEED: Categorias
-- ============================================================
INSERT INTO categories (name, slug, icon, sort_order) VALUES
    ('Pizza',      'pizza',      '🍕', 1),
    ('Burgers',    'burgers',    '🍔', 2),
    ('Massas',     'massas',     '🍝', 3),
    ('Saladas',    'saladas',    '🥗', 4),
    ('Sobremesas', 'sobremesas', '🍰', 5),
    ('Bebidas',    'bebidas',    '🥤', 6)
ON CONFLICT (slug) DO NOTHING;

-- ============================================================
-- SEED: Produtos
-- ============================================================
INSERT INTO products (category_id, name, description, price, emoji, badge, rating, prep_time) VALUES
    -- Pizzas
    (1, 'Pizza Margherita',   'Molho de tomate, mozzarella fresca e manjericão',         12.50, '🍕', 'Popular',     4.9, '25 min'),
    (1, 'Pizza Pepperoni',    'Pepperoni fatiado, queijo mozzarella e orégãos',           13.90, '🍕', NULL,          4.8, '25 min'),
    (1, 'Pizza Vegetariana',  'Pimentos, cogumelos, azeitonas e queijo',                  13.00, '🍕', 'Vegan',       4.7, '25 min'),
    (1, 'Pizza 4 Queijos',    'Mozzarella, gorgonzola, parmesão e gouda',                 14.50, '🍕', NULL,          4.9, '28 min'),
    (1, 'Pizza Frango',       'Frango grelhado, pimentos e creme de alho',                13.50, '🍕', NULL,          4.6, '30 min'),
    -- Burgers
    (2, 'Classic Burger',     'Carne 180g, alface, tomate, queijo cheddar',               9.90,  '🍔', 'Best Seller', 4.8, '20 min'),
    (2, 'BBQ Burger',         'Dupla carne, bacon crocante e molho BBQ',                  12.50, '🍔', NULL,          4.9, '22 min'),
    (2, 'Frango Crispy',      'Frango crocante, coleslaw e pickles',                      10.50, '🍔', NULL,          4.7, '20 min'),
    (2, 'Double Smash',       'Dois smash burgers com queijo americano',                  14.90, '🍔', 'Novo',        4.9, '25 min'),
    -- Massas
    (3, 'Pasta Carbonara',    'Spaghetti, pancetta, ovo e parmesão',                      11.00, '🍝', NULL,          4.8, '20 min'),
    (3, 'Bolonhesa Clássica', 'Molho bolonhesa lento com tagliatelle',                    10.50, '🍝', 'Popular',     4.7, '20 min'),
    (3, 'Penne ao Pesto',     'Penne com pesto de manjericão e pinhões',                  10.90, '🍝', 'Vegan',       4.6, '18 min'),
    (3, 'Lasanha de Carne',   'Lasanha italiana com carne e béchamel',                    12.00, '🍝', NULL,          4.8, '30 min'),
    -- Saladas
    (4, 'Salada Caesar',      'Alface romana, croutons, parmesão e molho Caesar',         8.50,  '🥗', NULL,          4.6, '10 min'),
    (4, 'Salada Grega',       'Tomate, pepino, azeitonas, feta e orégãos',                8.90,  '🥗', 'Vegan',       4.7, '10 min'),
    (4, 'Salada Tropical',    'Frango, manga, abacate e vinagrete de lima',                10.50, '🥗', NULL,          4.8, '12 min'),
    -- Sobremesas
    (5, 'Cheesecake',         'Cheesecake de frutos vermelhos cremoso',                   5.50,  '🍰', 'Popular',     4.9, '5 min'),
    (5, 'Brownie Quente',     'Brownie de chocolate com gelado de baunilha',              5.90,  '🍫', NULL,          4.9, '8 min'),
    (5, 'Gelado Artesanal',   'Seleção de 3 bolas de gelado artesanal',                   4.90,  '🍦', NULL,          4.7, '3 min'),
    (5, 'Pudim Português',    'Pudim flan tradicional com caramelo',                       4.50,  '🍮', NULL,          4.8, '5 min'),
    -- Bebidas
    (6, 'Sumo Natural',       'Sumo de laranja espremido na hora',                        3.50,  '🍊', NULL,          4.8, '3 min'),
    (6, 'Coca-Cola',          'Coca-Cola gelada 33cl',                                    2.50,  '🥤', NULL,          4.5, '2 min'),
    (6, 'Água com Gás',       'Água mineral com gás 50cl',                                1.90,  '💧', NULL,          4.4, '1 min'),
    (6, 'Smoothie Frutas',    'Manga, morango e banana batidos',                           4.90,  '🥤', 'Saudável',    4.9, '5 min')
ON CONFLICT DO NOTHING;

-- ============================================================
-- SEED: Admin padrão (password: Admin@1234)
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
    ('Administrador', 'admin@foodexpress.pt',
     '$2y$12$hashed_replace_with_real_bcrypt_hash', 'admin')
ON CONFLICT (email) DO NOTHING;
