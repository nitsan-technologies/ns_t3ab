# Changelog

All notable changes to T3AB (`ns_t3ab`) are documented in this file.

## 1.0.0 - 2026-10-27

Initial release. GPL-2.0-or-later.

### Content Blocks

- Backend module **Administration → T3AB (Content Blocks)**.
- Generate a content block from a name, description, category, icon, and prompt.
- Store the block in a selected local package (`selectPackageToStoreAiContentElements`).
- Preview generated fields, regenerate, and review the created files.
- Requires `friendsoftypo3/content-blocks` (0.7.x on TYPO3 12, 1.x on 13, 2.x on 14). Without it, the module explains that Content Blocks is not installed.

### AI Forms

- Create a TYPO3 Form Framework form from a prompt in the Form module.
- Requires `typo3/cms-form`.

### AI providers

Bring your own API key. Keys stay in extension configuration.

| Provider | Default model |
|---|---|
| OpenAI | `gpt-4.1` |
| Gemini | `gemini-3.5-flash-lite` |
| Claude | `claude-sonnet-5-5` |
| DeepSeek | `deepseek-flash` |
| xAI | `grok-2-latest` |
| Azure OpenAI | endpoint, deployment, and API version in extension configuration |
| Ollama | optional local server (`enable_ollama_model`) |

OpenAI also offers `gpt-4o`, `gpt-5`, `gpt-5.6-terra`, `gpt-5.6-sol`, and `gpt-6-luna`. Gemini also offers `gemini-3.8-flash`. Claude also offers `claude-haiku-5-5` and `claude-opus-4-8`. DeepSeek also offers `deepseek-v4-pro`. xAI also offers `grok-2-vision-latest`.

The create dialog shows one provider’s models at a time.

### Requirements

- TYPO3 12.4, 13.4, and 14.3.
- PHP 8.2 to 8.5.
- `nitsan/ns-license` 14.3 or later is installed with the extension. T3AB does not call the license server.

### Project

- Extension configuration for the default provider, model, and the package that receives Content Blocks.
- Documentation in `Documentation/`.
- GitHub Actions for TYPO3 12, 13, and 14 (lint, coding standards, unit tests, PHPStan) and for documentation rendering.
