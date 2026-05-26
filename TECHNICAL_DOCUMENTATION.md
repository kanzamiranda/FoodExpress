# FOOD EXPRESS - Documento Técnico do Sistema

## 1. Introdução

### 1.1 Visão Geral

O FoodExpress é uma plataforma web moderna desenvolvida para gestão e realização de pedidos de comida online. O sistema foi criado com o objetivo de facilitar a comunicação entre clientes, restaurantes e administradores através de uma solução digital centralizada, segura e responsiva, eliminando as barreiras tradicionais do serviço de delivery.

A plataforma permite que os utilizadores possam visualizar restaurantes, consultar menus detalhados com imagens e descrições, realizar pedidos personalizados, acompanhar encomendas em tempo real e gerir todas as operações relacionadas ao delivery de comida de forma integrada.

O sistema foi desenvolvido utilizando tecnologias modernas de frontend, backend e banco de dados, seguindo uma arquitetura desacoplada baseada em API REST, o que garante flexibilidade, manutenibilidade e possibilidade de expansão para aplicações móveis nativas no futuro.

### 1.2 Contexto de Negócio

O mercado de delivery de comida tem crescido exponencialmente, impulsionado pela transformação digital e mudanças nos hábitos de consumo. O FoodExpress surge como uma solução que conecta diretamente restaurantes e consumidores, eliminando intermediários e oferecendo uma experiência mais personalizada e económica para ambos os lados.

---

## 2. Objetivos do Sistema

### 2.1 Objetivo Principal

O principal objetivo do FoodExpress é digitalizar e otimizar o processo completo de encomendas de comida, desde a escolha do restaurante até à entrega final, proporcionando uma experiência fluida e satisfatória para todos os intervenientes.

### 2.2 Objetivos Específicos

**Para Clientes:**
- Realização de pedidos online de forma simples e rápida
- Acesso a múltiplos restaurantes numa única plataforma
- Visualização detalhada de menus com fotos e preços atualizados
- Acompanhamento do estado dos pedidos em tempo real
- Histórico completo de encomendas anteriores
- Sistema de avaliações e feedback

**Para Restaurantes:**
- Gestão autónoma do menu e catálogo de produtos
- Receção e processamento eficiente de pedidos
- Atualização dinâmica do estado das encomendas
- Controlo de disponibilidade de itens
- Visibilidade e alcance de novos clientes

**Para Administradores:**
- Gestão centralizada de todos os utilizadores e restaurantes
- Monitorização de métricas e desempenho da plataforma
- Controlo de qualidade e moderação de conteúdo
- Relatórios analíticos de vendas e utilização
- Gestão de categorias e organização do marketplace

### 2.3 Requisitos Técnicos

- Sistema de autenticação seguro com múltiplos níveis de acesso
- Integração com banco de dados relacional de alta performance
- Disponibilidade em dispositivos móveis e desktop (design responsivo)
- Experiência moderna, intuitiva e acessível
- Arquitetura escalável e preparada para crescimento
- Tempo de resposta otimizado para operações críticas

---

## 3. Tecnologias Utilizadas

### 3.1 Frontend

O frontend foi desenvolvido utilizando tecnologias modernas que garantem performance, manutenibilidade e excelente experiência de utilizador:

**Angular (Framework Principal)**
- Versão: Angular 16+
- Arquitetura baseada em componentes reutilizáveis
- Sistema de rotas com lazy loading para performance otimizada
- Formulários reativos com validação avançada
- Services para gestão de estado e comunicação com API
- Guards para proteção de rotas autenticadas
- Interceptors para manipulação de tokens JWT
- Suporte a PWA (Progressive Web App)

**Tailwind CSS (Framework de Estilização)**
- Sistema de classes utilitárias para design rápido
- Totalmente responsivo (mobile-first)
- Customização de temas (cores, tipografia, espaçamentos)
- Suporte nativo a modo escuro (dark mode)
- Otimização de CSS com purging em produção
- Animações e transições suaves

**TypeScript**
- Tipagem estática para código mais seguro
- Interfaces e tipos para modelos de dados
- Melhor autocompletar e documentação no IDE
- Deteção precoce de erros em tempo de desenvolvimento

**HTML5 e CSS3**
- Semântica HTML para melhor SEO e acessibilidade
- CSS Grid e Flexbox para layouts complexos
- Animações e transições CSS
- Suporte a variáveis CSS para theming

### 3.2 Backend

O backend foi desenvolvido com foco em segurança, performance e organização:

**PHP (Linguagem do Servidor)**
- Versão: PHP 8.1+
- Programação orientada a objetos
- Namespaces e autoloading (PSR-4)
- Tratamento avançado de exceções
- Validação rigorosa de inputs
- Sanitização de dados contra injeção SQL e XSS

**API REST**
- Endpoints organizados por recursos
- Métodos HTTP semânticos (GET, POST, PUT, DELETE)
- Códigos de status HTTP padronizados
- Versionamento de API (v1, v2)
- Documentação automática de endpoints
- Rate limiting para prevenção de abusos
- CORS configurado para segurança

**JSON (Formato de Dados)**
- Serialização padronizada de dados
- Respostas consistentes com envelope padrão
- Tratamento de erros estruturado
- Compressão gzip para otimização de tráfego

**Middleware JWT (Autenticação)**
- Tokens de acesso com expiração configurável
- Refresh tokens para renovação segura
- Blacklist de tokens revogados
- Assinatura com algoritmos seguros (HS256/RS256)

### 3.3 Banco de Dados

**PostgreSQL**
- Versão: 15+
- Suporte a transações ACID
- Índices otimizados para consultas frequentes
- Constraints e validações ao nível do banco
- Stored procedures para operações complexas
- Triggers para atualizações automáticas
- Full-text search para pesquisa de restaurantes e pratos

**Neon Database (Serverless PostgreSQL)**
- Infraestrutura serverless com escalabilidade automática
- Branching de banco de dados para desenvolvimento
- Conexão segura com SSL
- Backups automáticos e point-in-time recovery
- Pooling de conexões otimizado

### 3.4 Hospedagem e Infraestrutura

**Frontend: Vercel**
- CDN global para entrega rápida de assets
- HTTPS automático com certificados SSL
- Deploy contínuo integrado com Git
- Previews de deploy para branches
- Otimização automática de imagens e assets

**Backend: Render**
- Ambiente de execução gerenciado
- Escalabilidade horizontal automática
- Monitorização de saúde do serviço
- Logs centralizados e pesquisáveis
- Variáveis de ambiente seguras
- Deploy automático a partir do repositório

**Banco de Dados: Neon**
- Serverless PostgreSQL gerenciado
- Conexões otimizadas com connection pooling
- Escalabilidade vertical e horizontal
- Backups diários automáticos

**Segurança e CDN: Cloudflare**
- Proteção contra ataques DDoS
- Firewall de aplicação web (WAF)
- Otimização de cache e performance
- DNS gerenciado
- Proteção contra bots maliciosos

---

## 4. Arquitetura do Sistema

### 4.1 Visão Geral da Arquitetura

O sistema segue uma arquitetura desacoplada (decoupled architecture) entre frontend e backend, comunicando-se exclusivamente através de API REST. Esta abordagem traz benefícios significativos:

- Desenvolvimento independente de frontend e backend
- Possibilidade de múltiplos clientes (web, mobile, desktop)
- Escalabilidade independente de cada camada
- Manutenção simplificada
- Testes mais eficientes por camada

### 4.2 Camada Frontend

**Componentes Principais:**
- Módulo de Autenticação: Login, registo, recuperação de senha
- Módulo de Navegação: Rotas, guards, layout responsivo
- Módulo de Restaurantes: Listagem, busca, filtros, detalhes
- Módulo de Pedidos: Carrinho, checkout, acompanhamento
- Módulo de Perfil: Dados pessoais, histórico, preferências
- Módulo de Administração: Dashboards, gestão, relatórios

**Fluxo de Dados:**
1. Componente solicita dados através de Service
2. Service faz requisição HTTP ao backend
3. Interceptor adiciona token JWT automaticamente
4. Resposta é processada e tipada com interfaces TypeScript
5. Dados são exibidos no componente com binding reativo

### 4.3 Camada Backend

**Estrutura MVC:**
- Models: Representação das entidades e regras de negócio
- Controllers: Recebem requisições e coordenam respostas
- Services: Lógica de negócio complexa
- Middleware: Autenticação, validação, logging

**Fluxo de Processamento:**
1. Requisição chega ao servidor
2. Middleware de CORS e segurança processam headers
3. Router identifica o controller apropriado
4. Middleware JWT valida autenticação (rotas protegidas)
5. Controller valida inputs
6. Service executa lógica de negócio
7. Model interage com banco de dados
8. Resposta é formatada em JSON e enviada

### 4.4 Camada de Dados

**Modelagem Relacional:**
- Utilizadores (clientes, restaurantes, administradores)
- Restaurantes (perfis, horários, categorias)
- Produtos/Pratos (com variações e personalizações)
- Pedidos (com estados e histórico)
- Avaliações e comentários
- Categorias e tags

**Padrões de Acesso:**
- Prepared statements para prevenção de SQL injection
- Transações para operações que envolvem múltiplas tabelas
- Índices em colunas frequentemente consultadas
- Paginação em consultas com muitos resultados

---

## 5. Funcionalidades do Sistema

### 5.1 Sistema de Autenticação Completo

**Login:**
- Autenticação por email e senha com validação em dois passos
- Sessão autenticada mantida via JWT (access token + refresh token)
- Validação de credenciais com proteção contra força bruta
- Proteção de rotas via guards que verificam autenticação e permissões
- Opção "Lembrar-me" com persistência segura
- Deteção de múltiplas tentativas falhadas com bloqueio temporário

**Cadastro:**
- Registo de novos utilizadores com formulário reativo
- Validação em tempo real de campos (email único, força de senha)
- Confirmação de senha com indicação visual de correspondência
- Diferentes tipos de utilizador com fluxos específicos:
  - Cliente: cadastro simplificado
  - Restaurante: cadastro com documentos e validação
- Aceitação de termos de uso e política de privacidade

**Recuperação de Senha:**
- Envio de link seguro de redefinição por email
- Token de recuperação com expiração (30 minutos)
- Página de criação de nova senha com requisitos de segurança
- Confirmação de alteração por email

**Verificação de Conta:**
- Envio automático de email de confirmação após registo
- Código OTP (One-Time Password) de 6 dígitos
- Reenvio de código com intervalo mínimo
- Ativação de conta após verificação bem-sucedida
- Contas não verificadas têm acesso limitado

### 5.2 Gestão de Restaurantes

**Para Clientes:**
- Listagem de restaurantes com paginação infinita
- Filtros por categoria, avaliação, distância e preço
- Pesquisa por nome, tipo de cozinha ou prato específico
- Visualização detalhada com fotos, horários e informações
- Menu interativo com categorias e subcategorias
- Indicação de restaurantes abertos/fechados em tempo real

**Para Restaurantes Parceiros:**
- Dashboard com visão geral de pedidos e faturação
- Gestão completa do cardápio com adição/edição/remoção de itens
- Upload de imagens dos pratos com otimização automática
- Definição de preços, promoções e destaques
- Configuração de horários de funcionamento
- Gestão de áreas de entrega e taxas

### 5.3 Sistema de Pedidos

**Processo de Compra:**
1. Seleção de itens do menu com personalizações
2. Adição ao carrinho com cálculo automático de totais
3. Revisão do pedido com possibilidade de edição
4. Aplicação de cupões de desconto
5. Escolha de método de pagamento
6. Confirmação final e envio do pedido

**Acompanhamento em Tempo Real:**
- Estados do pedido atualizados pelo restaurante:
  - 🟡 Recebido: Pedido confirmado pelo restaurante
  - 🔵 A Preparar: Cozinha iniciou preparação
  - 🟠 A Caminho: Estafeta saiu para entrega
  - 🟢 Entregue: Pedido finalizado com sucesso
- Notificações de mudança de estado
- Tempo estimado de entrega atualizado
- Histórico completo de pedidos anteriores
- Possibilidade de repetir pedidos anteriores

### 5.4 Sistema de Avaliações

- Classificação de 1 a 5 estrelas para restaurantes
- Avaliações individuais para pratos
- Comentários textuais com moderação
- Fotos de pratos enviadas por clientes
- Cálculo de média ponderada de avaliações
- Destaque para restaurantes mais bem avaliados

### 5.5 Painéis de Controlo

**Painel do Restaurante:**
- Visão geral de pedidos ativos com atualização automática
- Gestão de menu com interface drag-and-drop
- Histórico de pedidos com filtros e exportação
- Estatísticas de vendas e pratos mais pedidos
- Gestão de perfil e informações do estabelecimento

**Painel do Administrador:**
- Dashboard com métricas gerais da plataforma
- Gestão de utilizadores (todos os tipos)
- Aprovação e gestão de restaurantes
- Moderação de avaliações e comentários
- Configurações globais da plataforma
- Relatórios detalhados de uso e receita

---

## 6. Tipos de Utilizador

### 6.1 Cliente

**Capacidades:**
- Criar e gerir conta pessoal
- Autenticação segura com múltiplos fatores
- Pesquisar restaurantes por diversos critérios
- Visualizar menus detalhados com fotos e descrições
- Adicionar produtos ao carrinho com personalizações
- Realizar pedidos com diferentes métodos de pagamento
- Acompanhar estado da encomenda em tempo real
- Receber notificações de atualizações
- Avaliar restaurantes e pratos com estrelas e comentários
- Gerir endereços de entrega favoritos
- Visualizar histórico completo de pedidos
- Guardar restaurantes e pratos favoritos

**Restrições:**
- Apenas pode visualizar pedidos próprios
- Não pode modificar menus ou preços
- Limitado a ações de consumidor final

### 6.2 Restaurante

**Capacidades:**
- Gerir perfil do estabelecimento
- Criar e organizar menu por categorias
- Adicionar, editar e remover pratos
- Definir preços, promoções e disponibilidade
- Upload de imagens dos pratos
- Receber pedidos em tempo real
- Atualizar estado do pedido durante preparação:
  - Recebido: Confirmação inicial
  - A preparar: Início da preparação
  - A caminho: Enviado para entrega
  - Entregue: Finalizado
- Visualizar histórico de pedidos e faturação
- Responder a avaliações de clientes
- Configurar horários de funcionamento
- Definir áreas de entrega e taxas

**Restrições:**
- Acesso apenas ao próprio restaurante
- Não pode gerir outros restaurantes ou utilizadores
- Não tem acesso a configurações globais da plataforma

### 6.3 Administrador

**Capacidades:**
- Gestão completa de todos os utilizadores
- Criar, editar e suspender contas
- Aprovar novos restaurantes parceiros
- Gerir categorias globais de restaurantes
- Visualizar relatórios detalhados de toda a plataforma
- Controlar sistema e configurações globais
- Monitorizar vendas em tempo real
- Moderar conteúdo (avaliações, comentários, fotos)
- Gerir promoções e campanhas
- Acesso a logs e auditoria do sistema
- Configurar parâmetros de segurança

**Responsabilidades:**
- Garantir qualidade e integridade da plataforma
- Resolver disputas entre clientes e restaurantes
- Manter conformidade com regulamentações
- Monitorizar desempenho e disponibilidade do sistema

---

## 7. Base de Dados

### 7.1 Tecnologia e Infraestrutura

A base de dados foi desenvolvida em PostgreSQL, escolhido pela sua robustez, performance e suporte avançado a relacionamentos complexos. A infraestrutura utiliza Neon Database, que oferece PostgreSQL serverless com escalabilidade automática e gestão simplificada.

### 7.2 Tabelas Principais

**utilizadores**
- id (UUID, PK)
- nome (VARCHAR)
- email (VARCHAR, único)
- password_hash (VARCHAR)
- tipo_utilizador (ENUM: cliente, restaurante, admin)
- telefone (VARCHAR)
- email_verificado (BOOLEAN)
- data_criacao (TIMESTAMP)
- ultimo_login (TIMESTAMP)
- ativo (BOOLEAN)

**restaurantes**
- id (UUID, PK)
- utilizador_id (UUID, FK → utilizadores)
- nome (VARCHAR)
- descricao (TEXT)
- morada (VARCHAR)
- telefone (VARCHAR)
- categoria_id (UUID, FK → categorias)
- imagem_url (VARCHAR)
- horario_funcionamento (JSONB)
- taxa_entrega (DECIMAL)
- pedido_minimo (DECIMAL)
- avaliacao_media (DECIMAL)
- ativo (BOOLEAN)

**pratos**
- id (UUID, PK)
- restaurante_id (UUID, FK → restaurantes)
- nome (VARCHAR)
- descricao (TEXT)
- preco (DECIMAL)
- categoria (VARCHAR)
- imagem_url (VARCHAR)
- disponivel (BOOLEAN)
- destaque (BOOLEAN)
- data_criacao (TIMESTAMP)

**pedidos**
- id (UUID, PK)
- cliente_id (UUID, FK → utilizadores)
- restaurante_id (UUID, FK → restaurantes)
- estado (ENUM: recebido, preparar, caminho, entregue, cancelado)
- total (DECIMAL)
- morada_entrega (TEXT)
- data_pedido (TIMESTAMP)
- data_entrega (TIMESTAMP)
- observacoes (TEXT)

**itens_pedido**
- id (UUID, PK)
- pedido_id (UUID, FK → pedidos)
- prato_id (UUID, FK → pratos)
- quantidade (INTEGER)
- preco_unitario (DECIMAL)
- personalizacoes (TEXT)

**avaliacoes**
- id (UUID, PK)
- cliente_id (UUID, FK → utilizadores)
- restaurante_id (UUID, FK → restaurantes)
- prato_id (UUID, FK → pratos, nullable)
- nota (INTEGER, CHECK 1-5)
- comentario (TEXT)
- data_avaliacao (TIMESTAMP)

### 7.3 Características da Base de Dados

**Relacionamentos:**
- Um utilizador pode ter um restaurante (1:1 para tipo restaurante)
- Um restaurante tem múltiplos pratos (1:N)
- Um cliente faz múltiplos pedidos (1:N)
- Um pedido contém múltiplos itens (1:N)
- Um restaurante recebe múltiplas avaliações (1:N)

**Integridade:**
- Chaves estrangeiras com ON DELETE CASCADE/RESTRICT
- Constraints UNIQUE em campos críticos
- CHECK constraints para validação de valores
- Índices em colunas de busca frequente
- Triggers para atualização automática de médias

**Segurança:**
- Senhas armazenadas com hash bcrypt
- Dados sensíveis isolados
- Conexões exclusivamente via SSL
- Acesso mínimo necessário por utilizador de BD

---

## 8. Interface e Experiência do Utilizador

### 8.1 Design System

**Princípios de Design:**
- Clareza: Informação organizada e hierarquizada
- Consistência: Padrões visuais uniformes em toda a aplicação
- Feedback: Respostas visuais para todas as ações do utilizador
- Eficiência: Fluxos otimizados para tarefas comuns
- Acessibilidade: Conformidade com diretrizes WCAG 2.1

**Paleta de Cores:**
- Modo Claro: Fundos brancos e cinzas claros com destaque em laranja
- Modo Escuro: Fundos escuros com texto claro e destaque mantido

**Tipografia:**
- Família principal: Inter (ou similar sans-serif)
- Hierarquia clara de headings (h1-h6)
- Tamanhos responsivos baseados em rem

### 8.2 Funcionalidades Visuais

**Responsividade:**
- Layout adaptativo para mobile (320px+), tablet (768px+) e desktop (1024px+)
- Menu de navegação colapsável em dispositivos móveis
- Imagens otimizadas com lazy loading
- Touch targets adequados para interação tátil

**Modo Claro/Escuro:**
- Deteção automática da preferência do sistema
- Alternância manual com persistência de escolha
- Transição suave entre modos
- Contraste adequado em ambos os modos

**Sistema Multilíngue:**
- Suporte completo a Português (pt-PT) e Inglês (en-US)
- Deteção automática do idioma do navegador
- Alternância manual com persistência
- Arquivos de tradução organizados por módulo
- Formatação de datas, números e moedas localizada

### 8.3 Navegação

**Estrutura de Navegação:**
- Header: Logo, pesquisa, carrinho, perfil, idioma, tema
- Navegação Principal: Home, Restaurantes, Pedidos, Favoritos
- Breadcrumbs: Indicador de localização atual
- Footer: Links úteis, contactos, redes sociais

**Experiência de Navegação:**
- Transições suaves entre páginas
- Indicadores de carregamento (skeletons)
- Estados vazios com mensagens e ações sugeridas
- Tratamento elegante de erros

---

## 9. Integrações Externas

### 9.1 Serviço de Email

**Fornecedor:** SendGrid / Amazon SES / SMTP configurável

**Funcionalidades Implementadas:**

Verificação de Conta:
- Email de boas-vindas com link de verificação
- Template HTML responsivo com branding
- Link único com token seguro e expiração

Recuperação de Senha:
- Email com instruções e link seguro
- Token de uso único com validade de 30 minutos
- Confirmação de alteração de senha

Confirmação de Pedidos:
- Resumo detalhado do pedido
- Informações do restaurante e entrega
- Link para acompanhamento em tempo real

Notificações Transacionais:
- Alteração de estado do pedido
- Lembretes e follow-ups

### 9.2 APIs REST Internas

**Padrão de Comunicação:**
- Base URL versionada: /api/v1/
- Headers padrão:
  - Content-Type: application/json
  - Authorization: Bearer {token}
  - Accept-Language: {locale}

**Formato de Resposta Padrão:**
```json
{
  "success": true,
  "data": {},
  "message": "Operação realizada com sucesso",
  "errors": [],
  "timestamp": "2024-01-01T00:00:00Z"
}
```

**Principais Endpoints:**
- POST /api/v1/auth/login
- POST /api/v1/auth/register
- GET /api/v1/restaurants
- GET /api/v1/restaurants/{id}/menu
- POST /api/v1/orders
- GET /api/v1/orders/{id}/status
- PUT /api/v1/orders/{id}/status

### 9.3 Integrações Futuras

- Pagamentos: Stripe, PayPal, MB WAY
- Mapas: Google Maps API para geolocalização
- Notificações Push: Firebase Cloud Messaging
- Chat: Sistema de mensagens cliente-restaurante
- Análise: Google Analytics, Hotjar

---

## 10. Segurança do Sistema

### 10.1 Autenticação e Autorização

**JWT (JSON Web Tokens):**
- Access Token: curta duração (15-30 minutos)
- Refresh Token: longa duração (7-30 dias)
- Assinatura HMAC-SHA256
- Payload contém apenas dados não sensíveis

**Proteção de Rotas:**
- Guards no frontend que verificam autenticação
- Middleware no backend que valida token
- Verificação de permissões baseada em roles
- Redirecionamento para login quando não autenticado

### 10.2 Proteção de Dados

**Senhas:**
- Hash bcrypt com fator de custo 12
- Nunca armazenadas em texto plano
- Política de força: mínimo 8 caracteres, maiúsculas, números, símbolos

**Dados em Trânsito:**
- HTTPS forçado em todas as conexões
- HSTS (HTTP Strict Transport Security)
- Certificados SSL/TLS atualizados

**Dados em Repouso:**
- Banco de dados com criptografia em repouso
- Backups criptografados
- Dados sensíveis mascarados em logs

### 10.3 Prevenção de Ataques OWASP Top 10

- ✅ Injeção: Prepared statements, ORM com sanitização
- ✅ Autenticação Quebrada: JWT seguro, sessões gerenciadas
- ✅ Exposição de Dados: Criptografia, HTTPS, mínima exposição
- ✅ XML External Entities: Não utiliza XML
- ✅ Controle de Acesso Quebrado: Middleware de autorização
- ✅ Configurações Incorretas: Headers de segurança, CSP
- ✅ XSS: Sanitização de outputs, CSP
- ✅ Desserialização Insegura: Validação de tipos
- ✅ Componentes Vulneráveis: Atualizações regulares
- ✅ Logging Insuficiente: Logs detalhados de segurança

**Medidas Adicionais:**
- Rate limiting por IP e por utilizador
- Validação rigorosa de inputs (servidor e cliente)
- Proteção CSRF em formulários
- Content Security Policy (CSP)
- CORS configurado com origens específicas

---

## 11. Estrutura das Principais Telas

### 11.1 Tela de Login

- Formulário com campos de email e senha
- Validação em tempo real com mensagens de erro
- Opção "Lembrar-me"
- Link para recuperação de senha
- Link para criação de conta
- Feedback visual durante autenticação
- Redirecionamento automático se já autenticado

### 11.2 Tela de Cadastro

- Formulário dividido em etapas (se aplicável)
- Campos: nome, email, telefone, senha, confirmação
- Indicador de força da senha
- Aceitação de termos de uso
- Validação em tempo real
- Seleção de tipo de conta (cliente/restaurante)

### 11.3 Tela Inicial (Home)

- Hero section com busca principal
- Restaurantes em destaque (carrossel)
- Categorias populares
- Restaurantes mais bem avaliados
- Promoções ativas
- Seções personalizadas para utilizadores autenticados

### 11.4 Tela de Restaurante

- Cabeçalho com imagem, nome e avaliação
- Informações: horário, morada, taxa de entrega
- Menu organizado por categorias
- Itens com foto, descrição, preço e avaliações
- Botão de adicionar ao carrinho
- Indicação de itens indisponíveis
- Avaliações e comentários de clientes

### 11.5 Tela de Carrinho

- Lista de itens adicionados
- Quantidade ajustável por item
- Preço unitário e subtotal
- Cupão de desconto
- Total com taxas e descontos
- Botão para finalizar pedido
- Persistência do carrinho entre sessões

### 11.6 Tela de Checkout

- Confirmação de itens do pedido
- Seleção/adição de morada de entrega
- Escolha de método de pagamento
- Observações para o restaurante
- Resumo financeiro final
- Confirmação e envio do pedido

### 11.7 Tela de Acompanhamento

- Estado atual do pedido com indicador visual
- Timeline dos estados percorridos
- Tempo estimado de entrega
- Informações do restaurante
- Botão de contacto com suporte
- Opção de cancelar (se aplicável)

### 11.8 Painel do Restaurante

- Dashboard com cards de resumo:
  - Pedidos hoje
  - Faturação do dia
  - Pedidos pendentes
  - Avaliação média
- Lista de pedidos ativos com ações rápidas
- Gestão de menu com CRUD completo
- Estatísticas e gráficos

### 11.9 Painel Administrativo

- Dashboard com métricas da plataforma
- Gestão de utilizadores (tabela com filtros)
- Gestão de restaurantes (aprovação, suspensão)
- Moderação de conteúdo
- Relatórios exportáveis
- Configurações do sistema

---

## 12. Organização do Código

### 12.1 Estrutura do Frontend (Angular)

```
src/
├── app/
│   ├── core/                  # Singleton services, guards, interceptors
│   │   ├── guards/
│   │   ├── interceptors/
│   │   └── services/
│   ├── shared/               # Componentes, pipes, directives reutilizáveis
│   │   ├── components/
│   │   ├── directives/
│   │   └── pipes/
│   ├── features/             # Módulos funcionais
│   │   ├── auth/
│   │   ├── restaurants/
│   │   ├── orders/
│   │   ├── cart/
│   │   └── admin/
│   ├── models/               # Interfaces e tipos TypeScript
│   └── assets/               # Imagens, ícones, fontes
├── environments/             # Configurações por ambiente
└── styles/                   # Estilos globais
```

**Padrões e Convenções:**
- Nomenclatura consistente (kebab-case para ficheiros)
- Componentes standalone quando possível
- Services com providedIn: 'root'
- Lazy loading para módulos de features
- Tipagem estrita com TypeScript

### 12.2 Estrutura do Backend (PHP)

```
api/
├── controllers/       # Controladores por recurso
├── models/           # Modelos e entidades
├── middleware/       # JWT, validação, CORS
├── services/        # Lógica de negócio
├── routes/          # Definição de rotas
├── config/          # Configurações
├── helpers/         # Funções utilitárias
└── migrations/      # Migrações de banco de dados
```

**Padrões e Convenções:**
- PSR-4 para autoloading
- PSR-12 para estilo de código
- Namespaces organizados
- Tratamento consistente de exceções
- Logging estruturado

### 12.3 Benefícios da Organização

**Manutenção:**
- Código modular e coeso
- Responsabilidades bem definidas
- Fácil localização de funcionalidades

**Escalabilidade:**
- Novos recursos adicionados como módulos
- Equipa pode trabalhar em paralelo
- Testes isolados por módulo

**Legibilidade:**
- Estrutura previsível
- Convenções consistentes
- Documentação inline

**Reutilização:**
- Componentes e serviços partilhados
- Redução de duplicação
- Biblioteca de componentes UI

---

## 13. Performance e Otimização

### 13.1 Frontend

- Lazy loading de módulos e imagens
- Compressão Gzip/Brotli de assets
- Cache de recursos estáticos
- Tree shaking para eliminar código não utilizado
- Minificação de CSS e JavaScript
- Server-side rendering (SSR) para SEO

### 13.2 Backend

- Pooling de conexões com banco de dados
- Cache de consultas frequentes (Redis opcional)
- Paginação em listagens
- Compressão de respostas
- Otimização de queries SQL
- Rate limiting para proteção

### 13.3 Banco de Dados

- Índices otimizados em colunas de busca
- Vacuum e análise regular
- Connection pooling
- Consultas otimizadas com EXPLAIN ANALYZE

---

## 14. Testes e Qualidade

### 14.1 Estratégia de Testes

**Frontend:**
- Testes unitários com Jasmine/Karma
- Testes de integração
- Testes end-to-end com Cypress

**Backend:**
- Testes unitários com PHPUnit
- Testes de API com Postman/Newman
- Testes de integração

### 14.2 Qualidade de Código

- Linting com ESLint (frontend) e PHP_CodeSniffer (backend)
- Formatação automática com Prettier
- Análise estática com TypeScript strict mode
- Code review obrigatório
- CI/CD com verificações automáticas

---

## 15. Monitorização e Logging

### 15.1 Monitorização

- Uptime monitoring do backend e frontend
- Métricas de performance (tempo de resposta, taxa de erros)
- Alertas para anomalias
- Dashboard de saúde do sistema

### 15.2 Logging

- Logs estruturados em JSON
- Níveis: DEBUG, INFO, WARNING, ERROR, CRITICAL
- Logs de acesso e erros
- Logs de segurança (autenticação, autorização)
- Retenção e rotação configuráveis

---

## 16. Conclusão

O FoodExpress é uma solução moderna de delivery online desenvolvida utilizando tecnologias atuais e uma arquitetura desacoplada que garante flexibilidade e escalabilidade. O sistema atende os principais requisitos técnicos exigidos para plataformas de encomenda de comida, incluindo:

- ✅ Autenticação segura com JWT
- ✅ Base de dados relacional PostgreSQL
- ✅ API REST documentada e versionada
- ✅ Gestão completa de pedidos com estados
- ✅ Múltiplos tipos de utilizador com permissões
- ✅ Interface responsiva e multilíngue
- ✅ Segurança implementada em múltiplas camadas
- ✅ Arquitetura preparada para crescimento

A escolha tecnológica de Angular + PHP + PostgreSQL + Render + Neon + Vercel proporciona:

- **Performance:** Frontend otimizado, backend eficiente, BD rápido
- **Escalabilidade:** Infraestrutura serverless e arquitetura modular
- **Manutenibilidade:** Código organizado e padronizado
- **Segurança:** Múltiplas camadas de proteção
- **Custo-benefício:** Serviços gerenciados com plano gratuito inicial

O projeto apresenta uma estrutura profissional, organizada e preparada para utilização em ambiente real de produção, com potencial de evolução contínua e adaptação a novos requisitos de negócio.

### Próximos Passos Sugeridos

1. Implementação de gateway de pagamentos
2. Aplicação mobile nativa (iOS/Android)
3. Sistema de fidelidade e recompensas
4. Inteligência artificial para recomendações

---

**Data de Criação:** Maio de 2026  
**Versão:** 1.0  
**Status:** Documento Técnico Completo
