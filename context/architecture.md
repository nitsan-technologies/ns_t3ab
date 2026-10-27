# ns_t3ab — Architecture

*Current runtime layout (not future wish-list).*

---

## High-level modules

```
Backend (Web)
├── nst3ab_content_blocks → ContentBlocksModuleController | AvailabilityController (Administration / `tools`)
└── EXT:form overlay      → FormsModuleController (AJAX + routes) + template overrides
```

Plus: feature/bug report AJAX and the button-bar magic button.

---

## AI request flow (local providers)

```
FormsModuleController / ContentBlocksModuleController
    → AbstractController::$aiService (AiService)
        → AiService::requestAi($prompt, $type, $parsedBody)
            → builds prompt payload (AiForm | AiContentBlocks)
            → BaseClient::getRequestData($aiModel, …)
            → RequestFactory::request(…)
            → BaseClient::getResponseData(…)
```

Config source: `ext_conf` via `HelperUtility::getExtensionGlobalSettings()` (not DB provider registry).

Providers supported in `BaseClient`: openai, gemini, azure, claude, ollama, deepseek, xai.

---

## Content Blocks create flow

```
Modal (ai-content-blocks.js)
    → content_blocks_field_generation (AI field preview)
    → create_content_element
        → ContentBlockBuilder::create(LoadedContentBlock)
        → write frontend template, optional icon (v13+)
        → ClearCacheService + OpcodeCacheService
```

DI excludes `ContentBlocksModuleController` when `ContentBlockBuilder` class is missing (`Configuration/Services.php`).

---

## Forms create flow

```
Modal (forms-module.js)
    → t3ab_generate_ai_form_fields
        → AiService (AiForm) → parse JSON → FormDefinitionArray
        → preview template GeneratedFormFields
    → t3ab_ai_form_create
        → FormPersistenceManagerInterface::save(…)
        → JSON redirectUrl into Form Editor
```

Template overrides inject AI UI into EXT:form Form Manager (`Resources/Private/TemplateOverrides/…`).

---

## Key classes

| Piece | Path |
|---|---|
| Shared controller base | `Classes/Controller/AbstractController.php` |
| AI service | `Classes/Service/AiService.php` |
| Provider HTTP client | `Classes/Client/BaseClient.php` |
| Helpers / ext_conf / severity | `Classes/Utility/HelperUtility.php` |
| Content Block YAML mapping | `Classes/Utility/ContentBlocksYamlConfig.php` |
| Package picker | `Classes/Service/PackageResolverService.php` |

---

## Configuration entry points

| File | Role |
|---|---|
| `Configuration/Backend/Modules.php` | Backend module `nst3ab_content_blocks`; v14 uses `icon`, v12/13 use `iconIdentifier` |
| `ext_tables.php` | Backend CSS + inline language labels |
| `Configuration/Backend/Routes.php` | Form/content-block generation & create |
| `Configuration/Backend/AjaxRoutes.php` | Modals, key suggest, delete, feature/bug |
| `Configuration/Services.php` | DI + event listeners + Content Blocks exclude |
| `ext_localconf.php` | Fluid namespace, form template paths, MAIL paths |
| `ext_conf_template.txt` | AI provider settings |

---

## Version branching (important)

| Concern | v12 | v13 | v14 |
|---|---|---|---|
| Module icons | `iconIdentifier` | `iconIdentifier` | `icon` (EXT SVG path) |
| Button bar | `ModifyButtonBarEventListener` | same | same (`IconSize::SMALL`) |
| Views | ModuleTemplateFactory | same | same |
| Form settings / persistence | legacy API | `>= 13` settings + persistence signature | same as v13 |
| Content Blocks `LoadedContentBlock` | `iconHideInMenu` arg (CB 0.7) | 6-arg ctor + `setIcon` | 6-arg ctor + `setIcon`; `ContentTypeIcon()` empty ctor (CB 2.x) |

---

## No shared tables

T3AB does not define `tx_nst3ab_*` provider/request-log tables. Persistence is: ext_conf, form YAML mounts, Content Block folders under selected packages, optional backup under fileadmin `t3ab/content_blocks_backup/`.
