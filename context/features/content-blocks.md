# Feature — AI Content Blocks

**Status:** Done (requires `friendsoftypo3/content-blocks`)  
**Controller:** `Classes/Controller/ContentBlocksModuleController.php`  
**Fallback:** `Classes/Controller/ContentBlocksAvailabilityController.php`

---

## What it does

Lists T3AB-tagged Content Blocks, generates field definitions from a prompt, previews/edits them, then creates a Content Block package in a chosen host extension via `ContentBlockBuilder`. Supports key suggest, file preview, and delete-with-backup.

---

## Flow

1. **Index** — module `nst3ab_content_blocks` lists blocks where YAML has `t3ab: 1`.
2. **Modal** — AJAX `ai_content_blocks` → generation form (icons, packages, wizard groups).
3. **Preview fields** — route `content_blocks_field_generation`:
   - Validate vendor/key; ensure block does not already exist
   - `AiService::requestAi(…, 'AiContentBlocks', …)`
   - Parse via `HelperUtility::parseAiJsonResponse()` (strips markdown fences)
   - Map fields via `ContentBlocksYamlConfig::getFieldConfig`
4. **Create** — route `create_content_element`:
   - Build `LoadedContentBlock` (v12 vs v13+ vs v14 constructor/icon differs)
   - `ContentBlockBuilder::create`
   - Write frontend template (`<f:debug>{data._processed}</f:debug>`), optional SVG icon on ≥13
   - Clear all caches + opcode cache
5. **Delete** — AJAX `ai_content_blocks_delete`: copy to `fileadmin…/t3ab/content_blocks_backup/{name}`, then `rmdir`.
6. **Key suggest** — AJAX `content_block_key_suggest` normalizes + uniquifies folder key.

If `ContentBlockBuilder` is missing, DI excludes the create controller and the module shows `ContentBlocksNotInstalled`.

---

## Content Blocks package matrix

| TYPO3 | Composer package | Major |
|---|---|---|
| 12.4+ | `friendsoftypo3/content-blocks` | 0.7.x |
| 13.4+ | `friendsoftypo3/content-blocks` | 1.x |
| 14.3+ | `friendsoftypo3/content-blocks` | 2.x |

---

## Key paths

| Area | Path |
|---|---|
| Controller | `Classes/Controller/ContentBlocksModuleController.php` |
| Availability | `Classes/Controller/ContentBlocksAvailabilityController.php` |
| YAML field map | `Classes/Utility/ContentBlocksYamlConfig.php` |
| Icons helper | `Classes/Utility/ContentBlocksIconUtility.php` |
| Package list | `Classes/Service/PackageResolverService.php` |
| Templates | `Resources/Private/Templates/ContentBlocksModule/` |
| JS | `Resources/Public/JavaScript/ai-content-blocks.js`, `modal-view.js` |
| TCA override | `Configuration/TCA/Overrides/tt_content.php` |
| Page TS | `Configuration/page.tsconfig` |
| DI exclude | `Configuration/Services.php` |

---

## Naming rules

Vendor and content-block key must match `^[a-z0-9]((-{1,2})?[a-z0-9]+)*$` (`isValid()`).

Default host package can be set via ext_conf `selectPackageToStoreAiContentElements`.

---

## Do / Don't

**Do:** Keep `t3ab: 1` (+ `t3abTitle` / `t3abDescription`) so generated blocks stay visible in the T3AB list.

**Don't:** Assume Content Blocks API is identical across v12 / v13 / v14 — check constructor / icon helpers per version.

---

## Verification

With Content Blocks installed: generate a block → preview fields → create → appears in list → files preview OK → delete backs up then removes.
