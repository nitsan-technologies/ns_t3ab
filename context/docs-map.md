# Docs Map — ns_t3ab

*Index for fetch-on-demand. Store WHAT each source answers + path — not full page content.*

---

## Agent context (read first for coding)

| Need | Path |
|---|---|
| Router / what to load | `AGENTS.md` |
| Principles | `context/principles.md` |
| Tooling, compatibility | `context/core.md` |
| Runtime architecture | `context/architecture.md` |
| Feature summaries | `context/features/<name>.md` |
| Session log | `context/session-state.md` |
| Agent skills layout | `.agents/AGENTS.md` |

---

## Feature summaries (`context/features/`)

| Feature | File |
|---|---|
| AI Forms | `context/features/forms.md` |
| AI Content Blocks | `context/features/content-blocks.md` |
| AI Engine / providers | `context/features/ai-engine.md` |
| Backend modules / routes / hooks | `context/features/backend-integration.md` |

---

## Source of truth in code

| Topic | Path |
|---|---|
| Composer / extension key | `composer.json`, `ext_emconf.php` |
| Ext conf schema | `ext_conf_template.txt` |
| Modules | `Configuration/Backend/Modules.php`, `ext_tables.php` |
| Routes | `Configuration/Backend/Routes.php` |
| AJAX | `Configuration/Backend/AjaxRoutes.php` |
| DI | `Configuration/Services.php` |
| Icons | `Configuration/Icons.php` |
| JS imports | `Configuration/JavaScriptModules.php` |
| Fluid templates | `Resources/Private/Templates/` |
| Public JS | `Resources/Public/JavaScript/` |
| Labels | `Resources/Private/Language/locallang_be.xlf` |

---

## User-facing docs

Package `README.md` is currently a stub (`# T3AB`). Prefer agent context + code until Sphinx/Documentation is added.

---

## Common developer questions → where to look

| Question | Load |
|---|---|
| How does AI Form generation work? | `context/features/forms.md` |
| How are Content Blocks created? | `context/features/content-blocks.md` |
| Where are API keys? | `ext_conf_template.txt`, `context/features/ai-engine.md` |
| Which routes/AJAX exist? | `context/features/backend-integration.md` |
| v12 / v13 / v14 differences? | `context/architecture.md` § Version branching |
| Does this use ns_t3af AiServiceInterface? | No (today) — see `context/core.md` |
