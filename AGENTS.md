# AGENTS.md

Small PHP app for tracking LTL trips and client invoices (Russian UI). No framework, composer, build step, or test suite.

## Setup / verification

- Requires PHP 8.2+ with `pdo_sqlite` (no dependency manifest). Run `php bin/reset-db.php` then `php -S localhost:8000 -t public` and open http://localhost:8000 (Docker variant in README).
- `data/app.sqlite` is the runtime DB and is gitignored; `lib/db.php` dies with a hint if it is missing. Only `bin/reset-db.php` creates it.
- `bin/reset-db.php` **deletes** the DB and rebuilds it from `data/schema.sql` + `data/seed.sql`. Put schema/data changes in the `.sql` files, not in the SQLite file; ad-hoc DB edits vanish on reset.
- No lint/typecheck/test/CI commands exist. Verification = reset + manual page checks.

## Wiring

- Document root is `public/`; `lib/` and `data/` are outside it. All HTTP entrypoints live in `public/`.
- Every `public/*.php` starts with `require __DIR__ . '/../lib/bootstrap.php'` (`public/api/trips.php` uses `../../lib/bootstrap.php`). `bootstrap.php` sets `Europe/Moscow`, `E_ALL` + display_errors, and requires `db.php`, `functions.php`, `billing.php`; there is no autoloader.
- `index.php` is the only client-rendered view: `public/js/trips.js` fetches `public/api/trips.php` (JSON) via jQuery loaded from a CDN by `page_header()` in `lib/functions.php`. Other pages are server-rendered templates. No JS/CSS build.
- `nbproject/` is NetBeans metadata (PHP 8.2, UTF-8), not part of runtime.

## Business rules / gotchas

- `lib/billing.php`: `calc_share()` splits a trip's cost across clients by cargo weight (`amount = round(cost * share, 2)`). `billing_run_period('YYYY-MM')` picks trips by `departure_date` prefix excluding `status = 'cancelled'`; if an invoice for the month exists it **deletes and recomputes its lines** (status/sent/exported timestamps survive) in one transaction. Period is validated in `public/billing_run.php` as `^\d{4}-\d{2}$`.
- Statuses: trips `planned|shipped|delivered|cancelled`; invoices `draft|sent`. Dates are strings `YYYY-MM-DD HH:MM`, periods `YYYY-MM` matched via `LIKE 'period%'`.
- `public/export_1c.php`: CSV format is agreed with accounting — do not change BOM, `;` delimiter, columns `invoice;inn;client;period;trip;amount`, or comma decimals. It also **mutates data**: just requesting the URL stamps `exported_at` on every `status='sent' AND exported_at IS NULL` invoice, so opening it in a browser has side effects.
- After reset, seed data covers 2026-09/2026-10; only invoice #9 is sent-but-not-exported, so 1C export yields one row. Re-running billing for an existing month reports "updated", not "created".
- Russian number formatting is duplicated: `money()`/`num()` in `lib/functions.php` vs JS equivalents in `public/js/app.js` (`toLocaleString('ru-RU')`) — keep in sync. Escape HTML with `h()`, emit JSON via `json_out()` (unescaped Unicode).
- SQL style: ids cast to `(int)`, period regex-validated, other values interpolated directly; prepared statements only in `api/trips.php` and `invoices.php`.
