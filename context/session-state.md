# Session State

*Living work log — update at end of each session.*

## 2026-10-27 — CI composer advisory block

**Done:** `composerInstallHighest` and `composerInstallLowest` set `policy.advisories.block false` before resolving TYPO3. The published `composer.json` is restored afterwards.

**Last touched:** 2026-10-27

## 2026-10-27 — Version 1.0.0

**Done:** Extension version is 1.0.0. `CHANGELOG.md` records the initial release.

**Last touched:** 2026-10-27

## 2026-10-27 — CI runner and documentation

**Done:** Added `Build/Scripts/runTests.sh`, unit test, PHPStan, PHP-CS-Fixer, `Documentation/`, and workflows `core12.yml`, `core13.yml`, `core14.yml`, and `documentation.yml`.

**Last touched:** 2026-10-27

## 2026-10-27 — GitHub community files and README

**Done:** README uses the product-page hero and describes Content Blocks and AI Forms. Added community docs and `.github` issue, PR, Dependabot, labeler, and TER release files. Core and documentation workflows stay out until a test runner and `Documentation/` exist.

**Last touched:** 2026-10-27

## 2026-10-27 — License server check removed

**Done:** Deleted the backend-login listener and the package-activation `Setup` class. `nitsan/ns-license` remains a dependency. No `connectToServer` call remains.

**Last touched:** 2026-10-27

## 2026-10-27 — AI engine tabs in the create dialog

**Done:** Model lists are `.tab-pane` panels. Only the active engine stays visible, and its radio is checked when the tab opens. Same partial is used by Content Blocks and AI Forms.

**Last touched:** 2026-10-27

## 2026-10-27 — Content Blocks under Administration

**Done:** `nst3ab_content_blocks` parent is `tools` (Administration)Page tree removed.

**Last touched:** 2026-10-27

## 2026-10-27 — OpenAI shutdown models

**Done:** Removed shut-down `gpt-3.5-turbo` and `gpt-4`. Kept `gpt-4.1` (default), `gpt-4o`, and `gpt-5`. Added `gpt-5.6-terra`, `gpt-5.6-sol`, and `gpt-6-luna`.

**Last touched:** 2026-10-27

## 2026-10-27 — Claude and DeepSeek model IDs

**Done:** Replaced retired Claude 3 and DeepSeek chat/reasoner IDs. Defaults are `claude-sonnet-5-5` and `deepseek-flash`.

**Last touched:** 2026-10-27

## 2026-10-27 — Gemini model IDs

**Done:** Replaced shut-down Gemini 1.x IDs with `gemini-3.5-flash-lite` (default) and `gemini-3.8-flash` in `ext_conf_template.txt`, `AiEngineConfiguration`, and this instance's `settings.php`.

**Last touched:** 2026-10-27

## 2026-10-27 — Remove hosted AI Builder

**Done:** Removed `nst3ab_ai_builder`, auto-login controller, `LicenseRepository`, `LogService`, `t3ab_api`, and builder icons. Forms and Content Blocks are unchanged. Backend login still calls `ns_license`.

**Not done:** Shutdown of `https://t3ab.t3planet.de/t3ab`. That app is not in this repository. v11 and v12 copies of `ns_t3ab` still call it.

**Last touched:** 2026-10-27

## 2026-08-10 — v13 fatal: ModifyButtonBarEvent::getRequest()

**Symptom (v13):** `Call to undefined method TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent::getRequest()` — v14 OK.

**Cause:** `e2a3c642` (“Add TYPO3 v14 support and drop v11”) switched `ModifyButtonBarEventListener` from `$GLOBALS['TYPO3_REQUEST']` to `$event->getRequest()`, which exists only in TYPO3 14+.

**Agent update (done):** Documented dual-major rule in `AGENTS.md`, `context/principles.md`, `context/core.md`, `context/features/backend-integration.md`.

**Code fix:** `ModifyButtonBarEventListener::resolveRequest()` — `method_exists($event, 'getRequest')` else `$GLOBALS['TYPO3_REQUEST']`. Also `createLinkButton()` — `ComponentFactory` when class exists (v14), else `ButtonBar::makeLinkButton()` (v12/v13).

**Last touched:** 2026-08-10

## 2026-07-29 — v14 Forms DI + Content Blocks AI JSON

**Done:**
- `FormsModuleController` public DI + constructor injection of form persistence/config managers (fixes ServiceNotFound / makeInstance ctor errors)
- Form persistence API branched for v12 / v13 / v14 (`filemount` storage on v14)
- `HelperUtility::parseAiJsonResponse()` strips markdown fences; used by Forms + Content Blocks
- Earlier: `CharsetConverter::utf8_char_mapping`, iframe modal shell CSS

**Not done:** Manual smoke of AI Form create + Content Block generate on v14.

**Last touched:** 2026-07-29

## 2026-07-28 — Drop v11, add v14 (2.0.0)

**Done:** TYPO3 v11 support removed; floor raised to PHP ≥8.2 and TYPO3 12.4–14.x.

- `composer.json` / `ext_emconf.php` → v2.0.0, `ns_license >=14.3.0`
- Deleted v11-only: ButtonBarHook, JS v11, TemplateOverrides/v11, FormModuleV11 layout
- Simplified bootstrap (ext_localconf, ext_tables, Services) and controllers (no StandaloneView / MailMessage)
- v14 module icons in `Modules.php`; LicenseService-only activation restored
- Content Blocks: v14 `ContentTypeIcon()` branch, registry match default, Select/Radio items fix
- Agent context updated for new compatibility matrix

**Not done:** Package test/stan scripts, manual backend smoke on all three TYPO3 majors.

**Last touched:** 2026-07-28

## 2026-07-28 — Initial agent context scaffold

**Done:** Added Approach 2 agentic layout borrowed from `ns_t3af` pattern, tailored to T3AB.

**Not done:** Package test/stan scripts, Sphinx docs, skills under `.agents/skills/`.
