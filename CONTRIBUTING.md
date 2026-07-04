# Como Contribuir — SIG-GCM

Obrigada por considerar contribuir com o SIG-GCM! 🎉

---

## Antes de Começar

1. Leia o [README.md](./README.md) para entender o projeto
2. Verifique as [Issues abertas](https://github.com/rafaela13c/sig-gcm/issues) para ver se o que você quer fazer já existe
3. Para mudanças grandes, abra uma Issue primeiro para discutir

---

## Autoria Original

**Importante:** Ao contribuir com este projeto, você reconhece que:

- A **autoria original** do SIG-GCM pertence a **Rafaela Cristina Alcântara da Silva**
- Suas contribuições serão licenciadas sob **CC BY-NC-SA 4.0**
- O histórico de commits no GitHub serve como evidência de timeline de desenvolvimento

---

## Como Contribuir

### 1. Fork e Clone

```bash
git clone https://github.com/SEU_USUARIO/sig-gcm.git
cd sig-gcm
cp config/database.php.example config/database.php
# edite config/database.php com suas credenciais locais
```

### 2. Crie uma Branch

```bash
git checkout -b feature/minha-melhoria
```

### 3. Faça suas Alterações

- Siga o padrão de código existente (PHP 8, MVC, sem framework externo)
- Mantenha o cabeçalho de autoria nos arquivos modificados
- Documente o que você alterou

### 4. Commit e Push

```bash
git add .
git commit -m "feat: descrição clara da melhoria"
git push origin feature/minha-melhoria
```

### 5. Abra um Pull Request

Descreva claramente o que foi alterado e por quê.

---

## Padrões de Código

| Padrão | Detalhe |
|---|---|
| PHP | PHP 8.0+, sem frameworks externos, MVC puro |
| Banco | MySQL 8.0+, PDO, prepared statements |
| CSS | Vanilla CSS com variáveis customizadas (sem Tailwind, sem Bootstrap) |
| JS | Vanilla JavaScript (sem jQuery, sem React) |
| Commits | Português ou inglês, descritivo (feat/fix/docs/refactor) |

---

## O que NÃO commitar

- `config/database.php` (senha do banco)
- `config/dev_credentials.php` (credenciais de desenvolvimento)
- Pasta `dev/` (painel oculto de desenvolvimento)
- Arquivos `.env` com segredos
- Pasta `uploads/` com arquivos reais de usuários

Todos estes já estão no `.gitignore`.
