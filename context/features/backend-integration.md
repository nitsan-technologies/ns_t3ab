# Feature — Backend integration

**Status:** Done  
**Modules / routes / hooks / support UI**

---

## What it does

Registers backend modules, HTTP routes, AJAX endpoints, icons, JS imports, form template overrides, the button-bar entry, and feature/bug report mailers.

---

## Modules

| Identifier | Controller | Notes |
|---|---|---|
| `nst3ab_content_blocks` | Content Blocks or Availability | Parent `tools` (Administration; alias of `admin` on v14); v14 `icon`, v12/13 `iconIdentifier` |

Registered in `Configuration/Backend/Modules.php`.

---

## Routes (`Configuration/Backend/Routes.php`)

| Route | Action |
|---|---|
| `t3ab_generate_ai_form_fields` | Forms generate |
| `ai_form` | Forms modal template |
| `t3ab_ai_form_create` | Forms save |
| `content_blocks_field_generation` | CB field preview |
| `create_content_element` | CB create |
| `ai_content_blocks` | CB modal template |

---

## AJAX (`Configuration/Backend/AjaxRoutes.php`)

| Route | Action |
|---|---|
| `content_block_key_suggest` | Suggest unique key |
| `ai_content_blocks` | CB modal |
| `ai_content_blocks_delete` | Delete + backup |
| `ai_form` | Forms modal |
| `ai_content_block_files_preview` | Generated files view |
| `feature_suggestion` / `bug_report` | Report UI |
| `submit_feature_bug` | Mail submit |

---

## Events / hooks

| Mechanism | Class |
|---|---|
| ModifyButtonBar | `Classes/EventListener/ModifyButtonBarEventListener.php` |

DI tags registered in `Configuration/Services.php` with `ListenerProviderPass`.

### ModifyButtonBar — multi-major request access

- **v14+:** `ModifyButtonBarEvent::getRequest()` exists (core #108764).
- **v12 / v13:** that method does **not** exist — use `$GLOBALS['TYPO3_REQUEST']` (same pattern as pre-v14 T3AB and `ns_t3aa` `AccessibleButtonBar`).
- Portable pattern: `method_exists($event, 'getRequest') ? $event->getRequest() : $GLOBALS['TYPO3_REQUEST']` (assert `ServerRequestInterface`).
- Form manager route id: `form_manager` on v14, `web_FormFormbuilder` on v12/v13 (`getReturnUrl()` already branches).

### ModifyButtonBar — link button factory

- **v14+:** `ComponentFactory::createLinkButton()` (preferred; `ButtonBar::makeLinkButton()` deprecated, removed in v15).
- **v12 / v13:** `ComponentFactory` class does **not** exist — use `$event->getButtonBar()->makeLinkButton()`.
- Portable pattern: `class_exists(ComponentFactory::class)` then factory, else `makeLinkButton()`.

Do not land button-bar changes verified only on v14.

---

## Feature / bug report

`FeatureAndBugReportController` — FluidEmail + `MailerInterface` to T3Planet addresses.

Templates: `Resources/Private/Templates/FeatureAndBugReport/`, `Resources/Private/Templates/Email/FeatureBug.html`.

---

## Assets

| Kind | Path |
|---|---|
| CSS (always) | `ext_tables.php` adds `Resources/Public/Css/backend.css` |
| Icons | `Configuration/Icons.php` |
| ES modules | `@nitsan/nst3ab/*` |
| Inline labels | `locallang_be.xlf` |

---

## Do / Don't

**Do:** Add v14 `icon` paths when registering new modules.

**Do:** Keep Form Manager AI button working on v12, v13, **and** v14 — never assume a new Core event method is polyfilled backward.

**Don't:** Register Content Blocks controller when Content Blocks package is absent — DI exclude already handles it.

**Don't:** Call `$event->getRequest()` on `ModifyButtonBarEvent` or `ComponentFactory` without a v12/v13 fallback.

---

## Verification

Modules appear under Web; Form Manager shows AI entry; magic button present; feature/bug AJAX returns JSON success when mail is configured.
