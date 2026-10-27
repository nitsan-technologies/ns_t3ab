# ns_t3ab — Operating Principles

*The common rule. Every change to context, code, or docs must serve the three goals below.*

---

## Goal 1 — Correct, maintainable code

Ship changes that match the current runtime in `Classes/` and `Configuration/`.

- **Supported matrix is TYPO3 12.4 + 13.4 + 14.3** (`composer.json`). A change is not done until it is safe on **every** declared major — especially v13 and v14. “Works on v14” alone is a regression.
- When a Core API exists only on a newer major (e.g. `ModifyButtonBarEvent::getRequest()` is **v14+**), keep a **v12/v13 fallback** (`method_exists` / version branch / `$GLOBALS['TYPO3_REQUEST']`). Prefer patterns already used in sibling packages (`ns_t3aa` AccessibleButtonBar uses `$GLOBALS['TYPO3_REQUEST']`).
- Keep TYPO3 v12 / v13 / v14 branching intentional where APIs differ (`HelperUtility::getCurrentTypo3Version()`, module icons, Content Blocks ctor, Form module routes).
- Forms depend on `typo3/cms-form`; Content Blocks UI depends on `friendsoftypo3/content-blocks` when present.
- `nitsan/ns-license` stays a dependency. This extension does not call `LicenseService`.

## Goal 2 — Token-cost efficiency (agent context)

Spend the fewest tokens that still give accurate guidance.

- **DRY context:** each fact lives in exactly ONE file. Reference, don't copy.
- **Load-on-demand:** read `context/features/<area>.md` for the task — not every controller.
- **Prune as you go:** update `context/session-state.md` briefly after landings.

## Goal 3 — Accuracy and trust

A confident wrong path is worse than asking.

- **Answer from context + code**, not invented APIs or routes.
- Conflicting facts across files = fix the source.
- Do not invent `ns_t3af` / `AiServiceInterface` wiring here unless code actually adds it.

---

## Guardrails (non-negotiable)

- **Dual-major Core API rule:** never call a TYPO3 Core method that landed only in v14 (or only in v13) without a fallback for older supported majors. Before landing TYPO3 API upgrades, check docs/API for the **lowest** supported major (`^12.4`) and verify the call exists there — or branch.
- API keys live in extension configuration (`ext_conf_template.txt`) — never log or echo secrets.
- Do not commit real API keys, license keys, or auto-login tokens.
- Content Block generation writes files into the selected package path — treat as filesystem-mutating.
- Form generation persists YAML via EXT:form persistence manager — respect allowed file mounts.
- GPL-compatible attribution when porting patterns from sibling T3Planet / monorepo packages.

---

## Apply this whenever you

- Edit `context/` or `AGENTS.md`
- Land a feature (update `context/features/*.md` + `session-state.md`)
- Port code from sibling packages in the monorepo
