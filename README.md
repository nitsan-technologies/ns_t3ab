[![Latest Stable Version](https://img.shields.io/badge/Stable-1.0.0-success)](https://extensions.typo3.org/extension/ns_t3ab)
[![T3AB GitHub](https://img.shields.io/badge/ns__t3ab-informational?logo=github)](https://github.com/nitsan-technologies/ns_t3ab)
[![TYPO3 14](https://img.shields.io/badge/TYPO3-14-important.svg?logo=typo3)](https://get.typo3.org/version/14)
[![TYPO3 13](https://img.shields.io/badge/TYPO3-13-important.svg?logo=typo3)](https://get.typo3.org/version/13)
[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-important.svg?logo=typo3)](https://get.typo3.org/version/12)
[![PHP](https://img.shields.io/badge/PHP-8.2%20to%208.5-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![core 12](https://github.com/nitsan-technologies/ns_t3ab/actions/workflows/core12.yml/badge.svg)](https://github.com/nitsan-technologies/ns_t3ab/actions/workflows/core12.yml)
[![core 13](https://github.com/nitsan-technologies/ns_t3ab/actions/workflows/core13.yml/badge.svg)](https://github.com/nitsan-technologies/ns_t3ab/actions/workflows/core13.yml)
[![core 14](https://github.com/nitsan-technologies/ns_t3ab/actions/workflows/core14.yml/badge.svg)](https://github.com/nitsan-technologies/ns_t3ab/actions/workflows/core14.yml)

# T3AB – TYPO3 AI Builder (`ns_t3ab`)

[![T3AB – TYPO3 AI Builder](Resources/Public/Images/Readme/readme.png)](https://t3planet.de/t3ab-typo3-erweiterung)

Describe what you need in the TYPO3 backend. T3AB generates Content Blocks and Form Framework forms with your own AI provider key.

- **Content Blocks** — Administration → T3AB (Content Blocks). Name the block, describe the fields, and generate it into a local package. Requires [friendsoftypo3/content-blocks](https://github.com/friendsoftypo3/content-blocks).
- **AI Forms** — create a form from a prompt in the Form module. Requires `typo3/cms-form`.
- **Your API key** — OpenAI, Gemini, Claude, Azure, DeepSeek, xAI, or a local Ollama server.

| | URL |
|---|---|
| **Repository:** | https://github.com/nitsan-technologies/ns_t3ab |
| **Issues:** | https://github.com/nitsan-technologies/ns_t3ab/issues |
| **Product page:** | https://t3planet.de/t3ab-typo3-erweiterung |
| **Support:** | https://t3planet.de/support |
| **License:** | [LICENSE](LICENSE) (GPL-2.0-or-later) |

## Requirements

- TYPO3 12.4 LTS, 13.4 LTS, or 14.3 LTS (classic or Composer mode)
- PHP 8.2 to 8.5
- `nitsan/ns-license` 14.3 or later (installed with this extension; T3AB does not call the license server itself)
- `typo3/cms-form` for AI Forms
- `friendsoftypo3/content-blocks` for the Content Blocks module (0.7.x on TYPO3 12, 1.x on 13, 2.x on 14)

## Installation

Composer mode:

```bash
composer require nitsan/ns-t3ab
vendor/bin/typo3 extension:setup
```

Classic mode: install the extension in **Admin Tools → Extensions**.

## Configuration

**Admin Tools → Settings → Extension Configuration → ns_t3ab**

| Setting | Purpose |
|---|---|
| `defaultModel` | Provider used when a dialog opens: `openai`, `gemini`, `azure`, `claude`, `ollama`, `deepseek`, `xai` |
| `openai_api_key`, `openai_model` | OpenAI. Default model `gpt-4.1` |
| `gemini_api_key`, `gemini_model` | Gemini. Default model `gemini-3.5-flash-lite` |
| `claude_api_key`, `claude_api_model` | Claude. Default model `claude-sonnet-5-5` |
| `azure_api_key`, `azure_api_endpoint`, `azure_api_model`, `azure_api_version` | Azure OpenAI |
| `deepseek_api_key`, `deepseek_model` | DeepSeek. Default model `deepseek-flash` |
| `xai_api_key`, `xai_model` | xAI. Default model `grok-2-latest` |
| `enable_ollama_model`, `ollama_api_url`, `ollama_api_model` | Local Ollama |
| `selectPackageToStoreAiContentElements` | Package that receives generated Content Blocks |

Store the API key for each provider you select. Keys stay in extension configuration.

## Development

```bash
composer install
composer test
composer ci
```

GitHub Actions (`.github/workflows/core12.yml`, `core13.yml`, `core14.yml`) run lint, unit tests, PHP-CS-Fixer, and PHPStan for each TYPO3 major through the Docker test runner:

```bash
Build/Scripts/runTests.sh -t 14 -p 8.3 -s composerInstallHighest
Build/Scripts/runTests.sh -t 14 -p 8.3 -s unit
Build/Scripts/runTests.sh -h
```

`Documentation/` is rendered by `.github/workflows/documentation.yml`. `Build/`, `Tests/`, and `context/` stay in git and are left out of release archives.

## Changelog

See [CHANGELOG.md](CHANGELOG.md). Version 1.0.0 is the initial release.

## License

GPL-2.0-or-later
