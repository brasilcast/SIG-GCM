# Changelog — SIG-GCM

Todas as mudanças notáveis neste projeto serão documentadas aqui.

Formato baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

---

## [1.2.0] — 2026-07-04

### Adicionado
- **Modo Ajuda com IA (Gemini 2.5 Flash):** assistente virtual integrado ao sistema com base de conhecimento JSON, controle de escopo estrito (responde apenas sobre o SIG-GCM) e visual de chat premium com glassmorphism
- **Geração de PDF das Escalas Semanais:** botão "Gerar PDF" que exibe escala 12×36 e 5×2 juntas em layout de impressão limpo
- **Escala Administrativa (5×2):** módulo de escalas para equipes administrativas com turnos de manhã (07:30–11:30) e tarde (13:00–17:00) de segunda a sexta

### Corrigido
- Constraint de chave estrangeira em `trocas_plantao` (SQLSTATE 150) corrigida padronizando para `INT UNSIGNED`
- Layout da tela de escalas semanais (`/escalas`) com colunas faltando e exibição incorreta de equipes
- Página em branco no PDF de escalas causada por `pdf_layout.php` não ecoando o conteúdo
- Avisos de depreciação do PHP 8.1+ no `views/escalas/pdf.php` com `substr()` e valores nulos

---

## [1.1.0] — 2026-04-15

### Adicionado
- **Módulo de Trocas de Plantão:** fluxo completo de solicitação, aprovação e registro de trocas
- **Módulo de Corregedoria:** processos disciplinares, sindicâncias e movimentações
- **Módulo Maria da Penha:** patrulha e acompanhamento de vítimas com medidas protetivas
- **Relatórios Avançados:** KPIs com gráficos, exportação e filtros por período
- **Auditoria Detalhada:** log de todas as ações (INSERT/UPDATE/DELETE) com dados anteriores e posteriores

### Alterado
- Sistema de permissões refatorado para modelo granular por usuário/recurso
- Dashboard diferenciado por perfil de usuário
- Instalação automática (auto-install) com seeding de dados de demonstração

---

## [1.0.0] — 2025-12-01

### Adicionado
- Estrutura base MVC PHP 8 do zero
- Sistema de autenticação com bcrypt, CSRF, bloqueio por tentativas e auditoria de logins
- **Módulo de Talões de Ocorrência:** registro, atualização, mapa de calor e tipos de ocorrência
- **Módulo de Frota:** cadastro de viaturas, checklists diários e abastecimentos
- **Módulo de Escalas 12×36:** geração de escalas operacionais semanais
- **Módulo de Ronda Escolar:** controle de visitas preventivas com status
- **Módulo de Comunicados:** mural de avisos com confirmação de leitura
- **Consultas Rápidas:** CEP, CNPJ, FIPE, feriados, validador de documentos
- **Clima:** integração com API OpenWeather
- **Gestão de Usuários e Permissões:** 6 perfis + permissões granulares individuais
- Design System completo em CSS Vanilla com tema claro/escuro
- Banco de dados com instalação automática e dados de demonstração

---

*Este changelog é parte da documentação de autoria e prova de desenvolvimento progressivo do SIG-GCM por Rafaela Cristina Alcântara da Silva.*
