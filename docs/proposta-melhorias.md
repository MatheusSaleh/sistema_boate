# Assis Night Club — Proposta de evolução

> Documento de planejamento. Nada aqui foi implementado ainda.
> Base analisada: commit `f7f7305` (PHP puro + mysqli + Bootstrap 5.3 via CDN).

---

## 0. Ponto de partida: o que existe hoje

| Módulo | O que faz | Observações relevantes |
|---|---|---|
| Login (`VIEW/index.php`) | E-mail + senha contra a tabela `usuarios` | Senha comparada em texto puro |
| Painel (`VIEW/painel.php`) | 4 cartões de navegação | Única página protegida por `UTIL/protect.php` |
| Funcionários | CRUD nome / idade / cargo | Separado de `usuarios`: funcionário não faz login e não tem permissão |
| Bebidas (`bebibas.php`) | CRUD nome / tipo / preço | Preço em campo de texto e `type="number"` sem casas decimais; sem estoque |
| Clientes | CRUD nome / e-mail / telefone | Sem documento, sem data de nascimento |
| Artistas + biografia | CRUD com upload de foto e redes sociais | A listagem **apaga automaticamente** artistas cuja data do show já passou |

### Problemas que bloqueiam qualquer evolução

Estes itens não são "melhorias": são pré-requisitos. Qualquer módulo financeiro construído em cima deles herdaria as falhas.

1. **Páginas de CRUD sem autenticação.** Só `painel.php` inclui `protect.php`. Qualquer pessoa que conheça a URL `CONTROLLER/clientes.php` consegue ler, editar e excluir dados.
2. **SQL Injection em todos os controllers.** Valores de `$_POST`/`$_GET` são concatenados direto no SQL (ex.: `CONTROLLER/clientes.php:562`, `CONTROLLER/artistas.php:189`).
3. **Exclusão via GET, sem confirmação e sem CSRF.** `<a href="?id=5">` apaga o registro. Um link malicioso, um pré-carregamento do navegador ou um toque acidental bastam.
4. **XSS armazenado.** Todo dado é impresso sem `htmlspecialchars` (nome de cliente, biografia, links de redes sociais em `href`).
5. **Upload inseguro e com bug de caminho.** `CONTROLLER/criar_artista.php:672` aceita qualquer extensão (inclusive `.php`, o que permite execução remota de código) e concatena `'../IMG' . $nome` sem `/`. Por isso existem arquivos `IMG1686…jpeg` soltos na raiz do repositório.
6. **Senhas em texto puro** e credenciais do banco (`root` sem senha) fixas no código.
7. **Perda de dados silenciosa.** A listagem de artistas executa `DELETE` em quem tem show no passado; o histórico some ao abrir a página.
8. **Sem schema versionado.** Não há `.sql` no repositório, então ninguém consegue subir o projeto do zero.
9. Detalhes: `logout.php` redireciona para `../view/index.php` (minúsculo, quebra em Linux); `criar_clientes.php:848` mostra "adicionado com sucesso" quando dá erro; erros crus do MySQL são exibidos ao usuário; `header()` chamado depois de `echo`.

---

## 4. Melhorias propostas

Escala de esforço (1 dev, conhecendo o projeto): **P** = até 3 dias · **M** = 1 a 2 semanas · **G** = 3+ semanas.

### F0 — Fundação técnica e segurança  *(obrigatória, vem primeiro)*

- **Problema:** os itens 1–9 acima.
- **Valor para o usuário:** dados de clientes e valores financeiros deixam de estar expostos; o dono pode confiar no que o sistema mostra.
- **Relevância técnica:** prepared statements em todo acesso ao banco; `protect.php` em todas as páginas (idealmente um front controller único); `password_hash`/`password_verify`; token CSRF em todo POST; exclusão só via POST; `htmlspecialchars` centralizado numa função `e()`; upload com lista branca de MIME/extensão, nome gerado pelo servidor e pasta sem execução de PHP; `config.php` fora do Git (com `config.example.php`); `database/schema.sql` + seeds; tabelas InnoDB (necessário para transações e chaves estrangeiras); layout/cabeçalho compartilhado em vez de HTML repetido em cada arquivo.
- **Esforço:** M.
- **Dependências:** nenhuma. Tudo abaixo depende disto.

### F1 — Usuários, perfis e permissões

- **Problema:** existe login, mas não há papéis. `funcionarios` e `usuarios` são tabelas sem relação; o barman e o dono teriam o mesmo acesso.
- **Valor:** cada pessoa vê só o que precisa; operações de risco (estorno, cancelamento, ajuste de estoque) ficam restritas.
- **Relevância técnica:** unificar em `usuarios` (com `funcionario_id` opcional) + `perfis` + `permissoes` (RBAC simples). Perfis sugeridos: **Admin**, **Gerente**, **Caixa**, **Bar/Garçom**, **Portaria**, **Promoter**. Checagem de permissão no servidor (nunca só esconder botão). Login rápido por PIN para bar e portaria, com troca de operador sem fechar a tela. Desativar usuário em vez de excluir.
- **Esforço:** M.
- **Dependências:** F0.

### F2 — Auditoria de operações sensíveis

- **Problema:** hoje não há como saber quem apagou um cliente ou alterou um preço.
- **Valor:** reduz fraude interna (o maior vazamento financeiro em casas noturnas costuma ser cancelamento de item e "cortesia" não autorizada) e resolve disputas.
- **Relevância técnica:** tabela `auditoria` só de inserção (`usuario_id`, `acao`, `entidade`, `entidade_id`, `antes` JSON, `depois` JSON, `motivo`, `ip`, `criado_em`). Registrar no mínimo: login/falha de login, alteração de preço, cancelamento de item, estorno, desconto/cortesia, ajuste de estoque, abertura/fechamento/sangria de caixa, check-in manual, alteração de permissões. Exigir **motivo** e, em alguns casos, **autorização de gerente** (PIN) no momento da ação. Trocar `DELETE` por exclusão lógica (`excluido_em`) nas entidades de negócio.
- **Esforço:** P a M.
- **Dependências:** F1.

### 1 — Eventos (evolução do módulo de Artistas)

- **Problema:** o sistema gira em torno do artista, mas a unidade de operação de uma boate é a **noite/evento**. O apagamento automático destrói o histórico.
- **Valor:** tudo (ingressos, lista, consumo, caixa, indicadores) passa a ser filtrável por noite, permitindo responder "quanto rendeu a festa de sábado?".
- **Relevância técnica:** tabela `eventos` (nome, início, fim, capacidade, status `rascunho → publicado → em_andamento → encerrado → cancelado`) e `evento_artistas` (line-up N:N, cachê, horário). O artista vira cadastro permanente; "próximo show" passa a ser derivado dos eventos. Definir **data operacional**: um evento que começa às 23h e termina às 5h pertence a um único dia de negócio.
- **Esforço:** M.
- **Dependências:** F0.

### 2 — Ingressos, lotes e capacidade

- **Problema:** não há controle de quantas pessoas podem entrar nem por qual preço.
- **Valor:** venda escalonada (1º lote, 2º lote, porta), relatório de receita de bilheteria e respeito ao limite do alvará/AVCB.
- **Relevância técnica:** `lotes` (evento, nome, preço, quantidade, início/fim de vigência, tipo: inteira/meia/VIP/camarote) e `ingressos` (código único não sequencial, lote, cliente, status `emitido → utilizado | cancelado | estornado`). Virada automática de lote por esgotamento ou data. Reserva com expiração para pagamentos pendentes.
- **Esforço:** G.
- **Dependências:** 1, F1; idealmente 8 (pagamentos).

### 3 — Lista de convidados e promoters

- **Problema:** listas de nome hoje ficam em papel ou WhatsApp, sem controle de quem trouxe quem.
- **Valor:** portaria encontra o nome em segundos; o promoter vê a própria lista e o dono sabe quanto público cada promoter trouxe de fato (base para comissão).
- **Relevância técnica:** `listas` (evento, promoter, tipo: VIP/aniversário/free até X h, limite de nomes, horário de corte) e `lista_convidados`. Cada convidado recebe o mesmo tipo de código/QR do ingresso, para que o check-in seja um fluxo só. Busca por nome tolerante a acentos.
- **Esforço:** M.
- **Dependências:** 1, F1.

### 4 — Check-in por QR Code

- **Problema:** conferência manual na porta é lenta e permite reaproveitar o mesmo ingresso.
- **Valor:** fila menor, lotação em tempo real, fim do "print do ingresso passado para o amigo".
- **Relevância técnica:** leitura pela câmera do celular no navegador (ex.: biblioteca `html5-qrcode`); o QR contém um token aleatório/assinado (não o ID). Resposta da validação em < 1 s com três estados claros: **liberado**, **já utilizado (com hora e operador)**, **inválido/outro evento**. Busca manual por nome/documento como alternativa. Contador de presentes × capacidade. Registrar saída opcional (para casas que controlam lotação instantânea).
- **Esforço:** M a G.
- **Dependências:** 2 e/ou 3.

### 5 — Produtos (evolução de Bebidas)

- **Problema:** "Bebidas" não cobre porções, narguilé, combos e garrafas de camarote; preço sem decimais; sem custo.
- **Valor:** cardápio completo e margem por produto.
- **Relevância técnica:** `produtos` (categoria, preço de venda e custo em `DECIMAL(10,2)`, unidade, ativo/inativo, controla estoque sim/não) e `categorias`. Combos/fichas técnicas (ex.: dose = 50 ml de uma garrafa de 1 L; balde = 5 long necks) para baixar estoque correto. Corrigir o nome "Bebibas".
- **Esforço:** P (cadastro) / M (combos e ficha técnica).
- **Dependências:** F0.

### 6 — Estoque e movimentações

- **Problema:** não se sabe o que entrou, o que saiu e o que sumiu.
- **Valor:** compra no momento certo, detecção de perda/desvio (estoque teórico × contagem física), fim de bebida acabando no meio da festa.
- **Relevância técnica:** modelo de **livro-razão**: `movimentacoes_estoque` (produto, tipo `entrada | venda | estorno_venda | perda | ajuste | transferencia`, quantidade com sinal, custo, referência à comanda/compra, usuário, motivo). O saldo é a soma das movimentações (ou um campo mantido **na mesma transação**), nunca editado à mão. Múltiplos locais (depósito, bar principal, bar VIP). Inventário de fechamento da noite com diferença auditada. Alerta de estoque mínimo.
- **Esforço:** M a G.
- **Dependências:** 5, F2.

### 7 — Comandas, consumo e fechamento

- **Problema:** não existe registro de consumo; clientes cadastrados não se ligam a nada.
- **Valor:** é o coração financeiro da noite: o bar lança, o caixa recebe, o gerente enxerga.
- **Relevância técnica:** `comandas` (número/cartão/QR físico, cliente opcional, evento, status `aberta → em_fechamento → paga | cancelada`, limite de crédito opcional) e `comanda_itens` (produto, quantidade, **preço unitário congelado no momento do lançamento**, usuário que lançou, status `ativo | cancelado` com motivo). Lançamento debita o estoque na mesma transação. Consumação mínima e couvert como regras do evento. Transferência de itens entre comandas (com auditoria).
- **Esforço:** G.
- **Dependências:** 5, 6, F1, F2, 1.

### 8 — Caixa, pagamentos e conciliação

- **Problema:** sem controle de quanto deveria haver em cada caixa nem por qual meio entrou.
- **Valor:** fechamento da noite em minutos, diferença de caixa identificada por operador, conferência com o extrato da maquininha e do PIX.
- **Relevância técnica:** `caixas` / `sessoes_caixa` (operador, abertura com fundo de troco, fechamento com contagem cega — o operador informa quanto contou antes de ver o esperado), `pagamentos` (comanda ou ingresso, meio: dinheiro/débito/crédito/PIX, valor, NSU/TXID, status `pendente → confirmado | estornado`, **chave de idempotência**), `sangrias`/`suprimentos`. Pagamento dividido entre meios e entre pessoas. Relatório de conciliação: esperado por meio × informado × extrato importado (CSV da adquirente). Integração automática com PIX/TEF fica para uma fase posterior.
- **Esforço:** G.
- **Dependências:** 7, F1, F2.

### 9 — Clientes (evolução)

- **Problema:** cadastro mínimo, sem dado que ajude a operação.
- **Valor:** histórico de frequência e gasto, aniversariantes, bloqueio de clientes problemáticos, verificação de maioridade.
- **Relevância técnica:** data de nascimento (obrigatória para venda de bebida alcoólica), CPF com validação e unicidade, flag de bloqueio com motivo, consentimento de marketing e base legal (LGPD), exportação/eliminação de dados a pedido do titular. Busca rápida por nome/telefone/CPF.
- **Esforço:** P a M.
- **Dependências:** F0.

### 10 — Indicadores operacionais e financeiros

- **Problema:** hoje não existe nenhum número consolidado.
- **Valor:** decisões de preço, line-up e compra baseadas em dados.
- **Relevância técnica:** painel ao vivo da noite (presentes × capacidade, entradas por faixa de 15 min, faturamento por meio de pagamento, top produtos, comandas abertas e valor em aberto) e relatórios por período (ticket médio, receita por evento/artista/promoter, CMV e margem, perdas de estoque, cancelamentos por operador, diferenças de caixa). Consultas agregadas com índices por `evento_id` e data; exportação CSV.
- **Esforço:** M.
- **Dependências:** 1–8 (cresce conforme os módulos entram).

### Ordem sugerida

| Fase | Entregas | Resultado |
|---|---|---|
| 1 | F0, F1, F2 | Sistema seguro e com responsáveis por cada ação |
| 2 | 1, 5, 9 | Cadastros corretos: eventos, produtos, clientes |
| 3 | 3, 2, 4 | Porta funcionando: lista, ingresso, check-in |
| 4 | 6, 7, 8 | Bar e caixa funcionando com estoque consistente |
| 5 | 10 | Gestão por indicadores |

---

### Regras críticas de negócio

Regras que precisam ser garantidas **no banco e no servidor**, não só na interface. Todas exigem InnoDB e transações.

**Check-in duplicado**
- Validar e marcar numa única operação atômica:
  `UPDATE ingressos SET utilizado_em = NOW(), checkin_por = ? WHERE token = ? AND evento_id = ? AND status = 'emitido'`
  — se `affected_rows = 0`, o ingresso já foi usado, é inválido ou é de outro evento. Isso elimina a corrida entre dois porteiros lendo o mesmo QR ao mesmo tempo.
- Na recusa, mostrar **quando** e **por quem** foi usado, para resolver a discussão na porta.
- Check-in manual (sem QR) exige permissão e fica auditado.

**Limite de capacidade**
- Venda de ingresso/lote e check-in devem travar o contador do evento (`SELECT … FOR UPDATE` na linha do evento ou do lote) antes de incrementar, dentro de uma transação.
- Soma de todos os lotes + listas não pode ultrapassar a capacidade do evento (validar na criação do lote).
- Reservas pendentes ocupam vaga até expirar; um job ou a própria consulta libera as expiradas.
- Definir política para lotação atingida: bloquear entrada ou permitir override de gerente (auditado).

**Consistência de estoque**
- Toda alteração de saldo gera uma movimentação; saldo nunca é editado diretamente.
- Lançamento de item e baixa de estoque na **mesma transação**: ou os dois acontecem, ou nenhum.
- Cancelamento de item gera movimentação de estorno (não apaga a movimentação original).
- Definir se o estoque pode ficar negativo (bloquear venda × permitir e alertar). Em boate, bloquear costuma travar o bar por erro de cadastro; recomenda-se permitir com alerta forte e relatório.
- Combos baixam cada componente conforme a ficha técnica.

**Cobranças duplicadas**
- Cada tentativa de pagamento carrega uma **chave de idempotência** gerada na tela; reenvio com a mesma chave devolve o resultado anterior em vez de cobrar de novo.
- `UNIQUE` em NSU (cartão) e TXID/E2E (PIX).
- O botão de pagar é desabilitado após o primeiro clique e o servidor rejeita pagamento em comanda que não esteja `aberta`/`em_fechamento`.
- A comanda só passa para `paga` quando a soma dos pagamentos confirmados é igual ao total; sobra gera troco registrado, falta mantém a comanda aberta.
- Estorno é um novo registro vinculado ao original, nunca edição/exclusão do pagamento.

**Outras regras importantes**
- **Valores monetários** em `DECIMAL(10,2)` (ou inteiros em centavos), nunca `FLOAT` ou texto.
- **Preço congelado** no item da comanda; alterar o cardápio não altera comandas abertas.
- **Caixa fechado não aceita lançamentos**; cada pagamento pertence a uma sessão de caixa aberta.
- **Comanda não pode ser reaberta** depois de paga sem permissão de gerente e motivo.
- **Maioridade:** venda de bebida alcoólica a menor de 18 anos é crime (ECA, art. 243). O sistema deve sinalizar menor de idade no check-in e na comanda.
- **Comanda perdida:** regra de multa/consumação definida no evento e aplicada por perfil autorizado.
- **Sincronização de horário:** usar a hora do servidor (não do aparelho) e fuso `America/Sao_Paulo` para todos os registros.

---

## 5. Proposta visual e experiência de uso

### Princípios

1. **Velocidade acima de tudo nas telas operacionais.** Portaria, bar e caixa trabalham com fila; cada toque a mais custa dinheiro.
2. **Ambiente escuro, mãos ocupadas, barulho alto.** A interface precisa ser legível com pouca luz, operável com uma mão e não depender de som.
3. **Cada perfil vê o seu sistema.** Nada de menu com 15 itens para quem só faz check-in.
4. **Erro caro exige confirmação; erro barato exige desfazer.** Não colocar confirmação em tudo (vira clique automático).

### Identidade visual

- **Tema escuro como padrão** nas telas operacionais: reduz ofuscamento no salão e não denuncia o operador na pista. O Bootstrap 5.3 já usado no projeto suporta `data-bs-theme="dark"`, então é possível migrar sem trocar de framework. Telas administrativas podem oferecer tema claro.
- **Paleta:** fundo grafite (não preto puro, ex. `#14141A`), superfícies `#1E1E26`, texto `#F2F2F5`. Uma cor de marca de acento (ex.: violeta/magenta neon) usada **só** em elementos de marca e ação principal.
- **Cores semânticas fixas e exclusivas:** verde = liberado/pago, âmbar = atenção/pendente, vermelho = recusado/erro, azul = informação. Sempre acompanhadas de **ícone e texto** (nunca só cor).
- **Tipografia:** uma família sem serifa legível (Inter ou a fonte do sistema), números com algarismos tabulares (`font-variant-numeric: tabular-nums`) para valores alinharem em colunas. Tamanho base mínimo 16 px; valores e resultados de check-in bem maiores.
- **Imagens:** substituir as imagens de terceiros carregadas por URL externa no painel por ícones/ilustrações próprias (evita quebra de link e problemas de direito de uso).
- **Nome e logo** "Assis Night Club" consistentes no cabeçalho, login e tela de check-in.

### Navegação

- **Após o login, o usuário cai na tela do seu perfil**, não num painel genérico.
- **Seletor de evento ativo** fixo no topo: todas as telas operacionais trabalham sobre "a noite de hoje" sem o usuário precisar escolher toda vez.
- **Computador:** menu lateral recolhível, agrupado por área (Operação · Cadastros · Financeiro · Relatórios · Administração).
- **Celular:** barra inferior com no máximo 4–5 destinos do perfil.
- **Tablet:** modo quiosque/tela cheia para bar e caixa, sem menus desnecessários.
- **Troca rápida de operador** por PIN, sem perder a tela em que estava.

### Principais telas por perfil

| Perfil | Dispositivo principal | Telas | Foco |
|---|---|---|---|
| **Portaria** | Celular | Scanner de QR em tela cheia · Busca por nome/documento · Contador de lotação | Resultado do check-in ocupando a tela inteira (verde/âmbar/vermelho + ícone + texto grande + vibração). Volta sozinho ao scanner em 2 s. Mostra nome, tipo (VIP, lista, lote) e alerta de menor de idade. |
| **Bar / Garçom** | Tablet ou celular | Abrir/buscar comanda (número ou QR) · Grade de produtos por categoria · Carrinho · Confirmar lançamento | Botões grandes com nome e preço, mais vendidos primeiro, busca por digitação. Quantidade com +/−. Confirmação mostra comanda e total antes de lançar. |
| **Caixa** | Computador ou tablet | Buscar comanda · Resumo de consumo · Pagamento (dividir por meio/pessoa) · Abertura/fechamento/sangria | Teclado numérico na tela, atalhos de teclado no desktop, troco calculado, comprovante. Valor restante sempre visível em destaque. |
| **Promoter** | Celular | Minhas listas · Adicionar nomes · Ver quem já entrou | Formulário curto, link compartilhável para convidados se cadastrarem. |
| **Gerente** | Computador e celular | Painel ao vivo da noite · Eventos e lotes · Estoque e inventário · Autorizações pendentes · Relatórios | Números grandes no topo, alertas (estoque baixo, lotação perto do limite, cancelamentos acima do normal). |
| **Admin** | Computador | Usuários e perfis · Configurações · Auditoria | Filtros por usuário/ação/período na auditoria. |

### Clareza

- Rótulos em linguagem do negócio ("Fechar comanda", "Registrar entrada"), sem jargão técnico.
- Uma ação principal por tela, visualmente dominante; ações secundárias discretas.
- Estados vazios explicativos ("Nenhuma comanda aberta. Toque em **Nova comanda** para começar.").
- Mensagens de erro em português claro e com o que fazer a seguir — nunca o erro cru do MySQL, como acontece hoje.
- Datas no formato `dd/mm/aaaa`, moeda `R$ 1.234,56`, hora em 24 h.
- Tabelas do desktop viram cartões no celular (as tabelas atuais estouram em telas pequenas).

### Acessibilidade

- Contraste mínimo WCAG AA (4,5:1 para texto, 3:1 para ícones e bordas de campos) — atenção especial ao tema escuro.
- Áreas de toque de pelo menos 44–48 px, com espaçamento entre botões adjacentes.
- Todos os campos com `<label>` associado (hoje alguns `for` apontam para o id errado, ex. `clientes.php:636`).
- Resultado do check-in e confirmações anunciados com `aria-live` para leitores de tela.
- Navegação completa por teclado no caixa, com foco visível.
- Não depender apenas de cor nem apenas de som; vibração como reforço no celular.
- Respeitar `prefers-reduced-motion` em animações.

### Prevenção de erros

- **Exclusão** vira desativação com opção de **desfazer** por alguns segundos; exclusão definitiva só para admin.
- **Ações financeiras** (estorno, cancelamento de item, desconto, cortesia) pedem motivo e, conforme o valor, PIN do gerente.
- **Botões destrutivos** separados fisicamente dos de confirmação e com cor/ícone distintos (hoje "Voltar" e "Excluir" usam o mesmo vermelho).
- **Botão de envio desabilitado após o clique**, com indicador de carregamento, para evitar lançamento ou cobrança em dobro.
- **Máscaras e validação imediata:** telefone, CPF, moeda, data; campos de preço aceitando vírgula.
- **Confirmação com resumo** antes de lançar consumo e antes de receber pagamento (comanda, itens, total).
- **Contexto sempre visível:** evento ativo, operador logado e caixa aberto no cabeçalho, para ninguém lançar na noite ou no caixa errado.
- **Aviso de conexão perdida** bem visível; em vez de "fingir" que salvou, bloquear a ação e avisar.
- **Encerramento automático da sessão** por inatividade nos terminais compartilhados.

---

## 6. Apresentação no GitHub

*(O enunciado desta seção estava incompleto no material recebido.)*
