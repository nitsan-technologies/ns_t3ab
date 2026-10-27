# Feature — AI Forms

**Status:** Done  
**Controller:** `Classes/Controller/FormsModuleController.php`  
**Depends:** `typo3/cms-form`

---

## What it does

Generates TYPO3 Form Framework definitions from a natural-language prompt, lets editors tweak fields, then saves YAML into an allowed form persistence mount and redirects into the Form Editor.

UI is injected into the Form Manager via template overrides under `Resources/Private/TemplateOverrides/Form/`.

---

## Flow

1. **Modal** — route/AJAX `ai_form` → `renderFormGenerationTemplateAction` (loads `forms-module.js`).
2. **Generate** — `t3ab_generate_ai_form_fields` → `generateFormFieldsAction`:
   - `AiService::requestAi($prompt, 'AiForm', $parsedBody)`
   - Parse JSON (`form.fields` or `elements`)
   - Seed from BlankForm.yaml; map types (`Input`→`Text`, `Select`→`SingleSelect`, …); drop Submit/Button
   - Preview: `GeneratedFormFields.html`
3. **Create** — `t3ab_ai_form_create` → `createAiFormAction`:
   - Unique persistence identifier + `FormPersistenceManagerInterface::save`
   - Return JSON `{ redirectUrl }` into Form Editor

---

## Key paths

| Area | Path |
|---|---|
| Controller | `Classes/Controller/FormsModuleController.php` |
| AI prompts | `Classes/Service/AiService.php` (`addAiFormPrompt`) |
| Templates | `Resources/Private/Templates/FormsModule/` |
| Partials | `Resources/Private/Partials/FormsModule/` |
| JS | `Resources/Public/JavaScript/forms-module.js` |
| Form Manager override | `Resources/Private/TemplateOverrides/Form/…` |
| Routes | `Configuration/Backend/Routes.php` |
| AJAX | `Configuration/Backend/AjaxRoutes.php` |

---

## Version notes

- Form settings load differs for TYPO3 ≥13 vs v12 (`getFormSettings()`).
- Persistence is version-split under `Classes/Service/Form/`:
  - `FormPersistenceBridgeV14` — storage `'filemount'` API
  - `FormPersistenceBridgeLegacy` — v12 / v13 signatures
  - `FormPersistenceBridgeFactory` — **public** DI service; controller uses `makeInstance(Factory)::get()`
- Controller stays **zero-arg** (backend routes use `GeneralUtility::makeInstance`, not constructor DI).
- Form Editor URL: v14 route `form_editor`; earlier `/FormEditor/index?formPersistenceIdentifier=…`.
- AI JSON may include markdown fences — parse via `HelperUtility::parseAiJsonResponse()`.

---

## Do / Don't

**Do:** Respect EXT:form allowed file mounts and beforeFormCreate/beforeFormSave hooks.

**Don't:** Persist forms outside Form Framework persistence APIs.

---

## Verification

Open Form module → AI form UI → generate with a simple prompt → create → lands in Form Editor with expected fields.
