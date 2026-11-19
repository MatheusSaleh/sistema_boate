# Sistema Boate

Sistema de gestão de boate (portaria, bar, caixa e cadastros) em **Laravel + React + Inertia**, com TypeScript, Tailwind e shadcn/ui.

> O sistema antigo em PHP puro está em [`legacy/`](legacy/) apenas como referência até a paridade ser atingida (issue #16).
> O roadmap da modernização está na issue #31 e em [`docs/proposta-melhorias.md`](docs/proposta-melhorias.md).

## Requisitos

- PHP 8.3+ com as extensões `pdo_sqlite`, `mbstring`, `openssl` e `fileinfo` (no Windows/macOS, o [Laravel Herd](https://herd.laravel.com) já traz tudo)
- [Composer](https://getcomposer.org) 2
- [Node.js](https://nodejs.org) 22+ e npm

## Instalação

```bash
git clone https://github.com/MatheusSaleh/sistema_boate.git
cd sistema_boate

composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan migrate   # cria database/database.sqlite se ainda não existir

composer run dev
```

Acesse <http://localhost:8000>. O `composer run dev` sobe junto o servidor PHP, a fila, os logs e o Vite.

## Banco de dados

- **Desenvolvimento:** SQLite (`database/database.sqlite`), sem configuração extra.
- **Produção:** MySQL. No `.env`, troque `DB_CONNECTION=mysql` e preencha `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`.

O fuso horário (`APP_TIMEZONE=America/Sao_Paulo`) e o idioma (`APP_LOCALE=pt_BR`) já vêm configurados no `.env.example`.

## Comandos úteis

| Comando | O que faz |
|---|---|
| `composer run dev` | Ambiente de desenvolvimento completo |
| `php artisan test` | Roda os testes (Pest) |
| `npm run build` | Build de produção do front-end |
| `npm run check` | Lint e formatação do front-end (`check:fix` corrige) |
| `npm run types:check` | Checagem de tipos TypeScript |
| `vendor/bin/pint` | Formata o código PHP |
