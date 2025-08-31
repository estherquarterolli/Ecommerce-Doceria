# Admin CRUD (PHP + PDO)

**Como usar:**
1. Suba a pasta inteira `admin_crud_php/` para seu servidor com PHP 8.1+.
2. Ajuste as credenciais do banco em `config.php` (host/IP, porta, nome da base, usuário e senha).
3. Acesse `login.php`. Um admin padrão é criado automaticamente se a tabela estiver vazia:
   - Email: `admin@local`
   - Senha: `admin123`
4. Após logar, troque a senha na tabela `admin_usuario` conforme sua política.

**Páginas:**
- `clientes_*` → CRUD completo de clientes (com hash de senha).
- `produtos_*` → CRUD completo de produtos.
- `pedidos_list.php` → lista somente leitura.
- `itens_pedido_list.php?id_pedido=...` → itens de um pedido (somente leitura).

**Observações de segurança (mínimo viável):**
- Uso de PDO + prepared statements.
- Token CSRF simples para POSTs.
- Sessões para autenticação.
- Sem upload de arquivos (campo `foto` é apenas URL).
