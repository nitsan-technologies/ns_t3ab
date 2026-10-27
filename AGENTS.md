# AGENTS.md — ns_t3ab

Agent router for T3AB (TYPO3 AI Builder). Load only what your task needs.

> **Principles:** `context/principles.md`  
> **Session log:** `context/session-state.md`  
> **Doc index:** `context/docs-map.md`

---

## Always load

| File | Why |
|---|---|
| `context/principles.md` | DRY, guardrails, attribution; **v12+v13+v14 dual-compat rule** |
| `context/core.md` | Compatibility matrix, tooling, multi-major Core API checklist |

**Hard rule:** `composer.json` targets `^12.4 \|\| ^13.4 \|\| ^14.3`. Any TYPO3 Core API change must stay compatible with **v13 and v14** (and v12 where still supported). Prefer version branches / portable fallbacks over v14-only methods. See `context/core.md` → “multi-major TYPO3 changes”.

---

## Task router

| Task | Load |
|---|---|
| AI Forms generation | `context/features/forms.md` |
| Content Blocks generation | `context/features/content-blocks.md` |
| AI engine / providers / `BaseClient` | `context/features/ai-engine.md` |
| Backend modules / routes / hooks | `context/features/backend-integration.md` |
| Architecture / request flow | `context/architecture.md` |
| Update agent context after a landing | Update `context/features/*.md` + `context/session-state.md` |

Prefer `context/features/*.md` over reading entire controllers unless implementing.

---

## Feature status

| Feature | Status | Agent entry |
|---|---|---|
| AI Forms | Done | `context/features/forms.md` |
| AI Content Blocks | Done | `context/features/content-blocks.md` |
| AI Engine (`AiService` + `BaseClient`) | Done | `context/features/ai-engine.md` |
| Backend integration (modules, AJAX, hooks) | Done | `context/features/backend-integration.md` |
| Feature / bug report mail | Done | `context/features/backend-integration.md` |

---

## Skills (`.agents/skills/`)

None yet. Shared skill layout follows `.agents/AGENTS.md`. Add skills under `.agents/skills/<name>/SKILL.md` when needed.

---

## Quick commands

```bash
cd packages/ns_t3ab
# Package has no composer test/stan scripts yet; verify via host DDEV:
# ddev exec typo3 cache:flush
```

See `context/core.md` for compatibility and conventions.
