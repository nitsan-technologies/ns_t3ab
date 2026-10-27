# ns_t3ab — Core Context

*Always load with `context/principles.md`.*

---

## Identity

| Field | Value |
|---|---|
| Extension key | `ns_t3ab` |
| Composer | `nitsan/ns-t3ab` |
| Namespace | `NITSAN\NsT3Ab\` |
| Vendor | NITSAN / T3Planet |
| Role | TYPO3 AI Builder — AI forms and AI content blocks |
| Version | `2.0.0` (`ext_emconf.php`) |

**Vision:** Describe forms and content blocks in natural language; generate ready-to-use TYPO3 artefacts (form YAML, Content Block packages).

**Agent context:** Lives in this package (`AGENTS.md`, `context/`). Pattern borrowed from `ns_t3af` agent layout — content is specific to T3AB.

---

## Compatibility (shipped)

| | Floor |
|---|---|
| PHP | `>=8.2 <9` (`composer.json`); `ext_emconf` lists `8.2.0-8.5.99` |
| TYPO3 | `^12.4 \|\| ^13.4 \|\| ^14.3` |
| Depends | `typo3/cms-form`, `nitsan/ns-license` `>=14.3.0` |
| Soft / runtime | Content Blocks module needs `friendsoftypo3/content-blocks` (`ContentBlockBuilder`); otherwise availability controller. Package majors: 0.7→12, 1.x→13, 2.x→14 |

Match `composer.json` + `ext_emconf.php` — do not invent a separate floor unless those files change.

### Agent rule — multi-major TYPO3 changes

When adding or changing Core API usage for “v14 support”:

1. Confirm the method/class exists on **v13.4** and **v12.4** (or gate with `HelperUtility::getCurrentTypo3Version()` / `method_exists`).
2. Prefer portable access first (e.g. `$GLOBALS['TYPO3_REQUEST']` for button-bar listeners) over v14-only helpers (`ModifyButtonBarEvent::getRequest()`).
3. Smoke the touched backend path on **v13 and v14** before calling the work done. v14-only verification is insufficient.

Known trap (2026-08): `ModifyButtonBarEvent::getRequest()` is **TYPO3 14+ only** — calling it unconditionally fatals on v13 with `Call to undefined method …::getRequest()`.

---

## Tooling

Package uses `vendor-dir: .Build/vendor` when installed in isolation. No first-class `composer test` / `composer stan` scripts in package root yet.

Monorepo DDEV (from distribution root):

```bash
ddev start
ddev composer install
ddev exec typo3 cache:flush
ddev exec typo3 extension:list | grep ns_t3ab
```

---

## Code conventions

- Prefer `declare(strict_types=1);` in new PHP files (many existing files already use it).
- Namespace: `NITSAN\NsT3Ab\…`
- Version branches: check `$this->typo3Version` / `HelperUtility::getCurrentTypo3Version()` for v12 vs v13+ vs v14 (module icons, Content Blocks API, form settings, button-bar request access).
- Fluid namespace: `t3ab` → `NITSAN\NsT3Ab\ViewHelpers` (`ext_localconf.php`).
- ES modules: `@nitsan/nst3ab/` → `Resources/Public/JavaScript/` (`Configuration/JavaScriptModules.php`).

---

## Extension configuration

All LLM keys and default models live in **Extension Manager** settings (`ext_conf_template.txt`), loaded via `HelperUtility::getExtensionGlobalSettings()`.

Categories include: AI Engine defaults, ChatGPT / Gemini / Claude / Azure / Ollama / DeepSeek / XAi, and the content package picker.

See `context/features/ai-engine.md`.

---

## Sibling packages (monorepo)

| Package | Relation |
|---|---|
| `ns_t3af` | Shared AI foundation pattern / agent context template — T3AB does **not** currently call `AiServiceInterface` |
| `ns_license` | Composer and `ext_emconf` dependency. No login or activation check in this extension. |
| `typo3/cms-form` | Required for AI Forms |
| `friendsoftypo3/content-blocks` | Optional for AI Content Blocks create path |
