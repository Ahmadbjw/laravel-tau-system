# System Map — Layer 1 (SmartOps)

## Project
**SmartOps** — Intelligent Operations & Ticketing System  
Goal: Laravel-based system that evolves layer-by-layer (system → intelligence → autonomy).

## Current Environment
- Laravel 11+ (new bootstrap style)
- Sail (Docker)
- Local development
- Git: main + ahmad_dev

## High-Level Request Lifecycle (Laravel 11+)
Client
→ `public/index.php`
→ Composer autoload: `vendor/autoload.php`
→ App bootstrap/config: `bootstrap/app.php`
   - routing registration (web/api/console)
   - middleware pipeline registration
   - exception handling configuration
→ Middleware pipeline
→ Routes (`routes/web.php` / `routes/api.php`)
→ Controller / Closure
→ Response (JSON/HTML)
→ Middleware AFTER (response headers etc.)
→ Middleware TERMINATE (post-response tasks like auditing)

## Implemented (So Far)
### Observability & Tracing
- Trace middleware (`TraceMiddleware`)
  - BEFORE log
  - AFTER response header `X-Trace-Id`
  - TERMINATE phase audit write

### Request Audit Logging (DB)
- Table: `request_audits`
- Stored fields:
  - trace_id, user_id (nullable)
  - method, path, status_code
  - duration_ms
  - ip, user_agent
- Verified via Tinker:
  - `RequestAudit::latest()->first()`

## Existing Endpoints
### API
- GET `/api/health`  → JSON health check

### Web (debug/learning)
- GET `/trace` → middleware lifecycle proof (before/after/terminate)
- GET `/autoload-test` → composer autoload proof (Support folder)

## Notes / Decisions
- Laravel 11+ lifecycle is managed via `bootstrap/app.php` (Kernel groups are not the primary place here).
- `terminate()` is used for lightweight post-response work; heavy work should go to queues/jobs later.
- This doc is a living architecture log; updated weekly/day-wise as we progress.
