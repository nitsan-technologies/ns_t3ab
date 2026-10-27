# Feature — AI Engine (local providers)

**Status:** Done  
**Service:** `Classes/Service/AiService.php`  
**Client:** `Classes/Client/BaseClient.php`  
**Config:** `ext_conf_template.txt` via `HelperUtility::getExtensionGlobalSettings()`

---

## What it does

Builds provider-specific HTTP requests for form and content-block generation. Model/vendor selection comes from the request body (`text-api-model`, `aiSelectedModel` / per-vendor model fields) plus extension defaults.

This is **package-local** — not `ns_t3af` `AiServiceInterface` / provider DB registry.

---

## Entry point

```php
AiService::requestAi(string $prompt, string $type, array $parsedBody): string
```

`$type`:

| Value | Prompt builder |
|---|---|
| `AiForm` | `addAiFormPrompt` — JSON form structure (`form->fields`) |
| `AiContentBlocks` | `addAiContentBlocksPrompt` — same shape today (JSON fields) |
| other | raw `$prompt` |

Chat-style OpenAI models use `messages[]`; legacy completion uses `prompt`.

---

## Providers (`BaseClient`)

| Key | Notes |
|---|---|
| `openai` | Chat vs legacy completions URL; `gpt-5`, `gpt-5.6-terra`, `gpt-5.6-sol`, and `gpt-6-luna` use `max_completion_tokens` |
| `gemini` | Generative Language API + safety settings |
| `azure` | Deployment chat completions + api-version query |
| `claude` | Anthropic messages API |
| `ollama` | `/api/chat` vs generate from configured URL |
| `deepseek` | OpenAI-compatible chat URL |
| `xai` | Grok chat completions |

Also: `getOpenAiUsageData` for OpenAI usage endpoint (admin/stats style callers).

Model pick from UI: `HelperUtility::getSelectedModal($parsedBody)` maps `text-api-model` → `*-models` fields.

---

## Key paths

| Area | Path |
|---|---|
| Service | `Classes/Service/AiService.php` |
| HTTP payload | `Classes/Client/BaseClient.php` |
| Ext conf UI helpers | `Classes/ExtensionManager/GlobalConfiguration.php`, `AiEngineConfiguration.php` |
| Fluid AI models partial | `Resources/Private/Partials/AiModels.html` — one `.tab-pane` inside `.tab-content`; only `.active` is shown |
| ViewHelpers | `GetAiEngineViewHelper`, `ExtConfigViewHelper` |

---

## Security

- Keys: `openai_api_key`, `gemini_api_key`, `claude_api_key`, `azure_api_key`, `deepseek_api_key`, `xai_api_key`, etc. in ext_conf only.
- Never log request bodies that include Authorization / api-key headers.

---

## Do / Don't

**Do:** Route new generation features through `AiService` so prompts stay centralized.

**Don't:** Document this as using AI Foundation providers unless/until migration lands.

---

## Verification

Configure at least one provider key + model → run Form or Content Block generation → JSON fields return without client exception.
