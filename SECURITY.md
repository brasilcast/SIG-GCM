# Política de Segurança — SIG-GCM

## Versões Suportadas

| Versão | Suporte |
|---|---|
| 1.2.x | ✅ Suportada |
| < 1.2 | ❌ Sem suporte |

---

## Reportar uma Vulnerabilidade

Se você descobrir uma vulnerabilidade de segurança no SIG-GCM, **não abra uma issue pública**. Entre em contato diretamente com a autora:

- **GitHub:** [@rafaela13c](https://github.com/rafaela13c)
- **LinkedIn:** [rafaela-cas1308](https://www.linkedin.com/in/rafaela-cas1308/)

Descreva a vulnerabilidade com o máximo de detalhes possível: passos para reproduzir, impacto potencial e possível correção.

Você receberá uma resposta em até **5 dias úteis**.

---

## Violações de Licença

Se você encontrar uso indevido deste software que viole os termos da licença **CC BY-NC-SA 4.0** — especialmente uso comercial sem autorização, ou distribuição sem crédito à autora original — reporte da mesma forma acima.

**Autoria Original:** este código foi desenvolvido do zero por **Rafaela Cristina Alcântara da Silva** como projeto acadêmico. O sistema é PHP 8 MVC puro. Qualquer alegação de plágio é infundada e juridicamente contestável.

---

## Medidas de Segurança Implementadas

- ✅ Hash bcrypt (custo 12) para senhas
- ✅ Tokens CSRF em todos os formulários
- ✅ Bloqueio automático após 3 tentativas de login incorretas (15 min)
- ✅ Timeout de sessão por inatividade (30 min) e absoluto (8 h)
- ✅ Log de auditoria completo de todas as ações do sistema
- ✅ Log de todos os acessos (IP, user-agent, timestamp)
- ✅ Sistema de permissões granulares por usuário
- ✅ Dados de produção não incluídos no repositório (`.gitignore`)
