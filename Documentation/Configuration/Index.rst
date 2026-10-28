.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============

Open :guilabel:`Admin Tools > Settings > Extension Configuration > ns_t3ab`.

Interactive demo: `T3AB Configuration <https://app.supademo.com/demo/cmdy6mkbl8a2j9f96fxtfioeu>`__.

``defaultModel`` selects the provider shown when a dialog opens:
``openai``, ``gemini``, ``azure``, ``claude``, ``ollama``, ``deepseek``, or
``xai``.

Store an API key for each provider you use. The dialog shows one provider’s
models at a time.

OpenAI
======

*  ``openai_api_key``
*  ``openai_model``, default ``gpt-4.1``

Also available: ``gpt-4o``, ``gpt-5``, ``gpt-5.6-terra``, ``gpt-5.6-sol``,
``gpt-6-luna``.

Gemini
======

*  ``gemini_api_key``
*  ``gemini_model``, default ``gemini-3.5-flash-lite``

Also available: ``gemini-3.8-flash``.

Claude
======

*  ``claude_api_key``
*  ``claude_api_model``, default ``claude-sonnet-5-5``

Also available: ``claude-haiku-5-5``, ``claude-opus-4-8``.

Azure OpenAI
============

*  ``azure_api_key``
*  ``azure_api_endpoint``
*  ``azure_api_model``
*  ``azure_api_version``

DeepSeek
========

*  ``deepseek_api_key``
*  ``deepseek_model``, default ``deepseek-flash``

Also available: ``deepseek-v4-pro``.

xAI
===

*  ``xai_api_key``
*  ``xai_model``, default ``grok-2-latest``

Also available: ``grok-2-vision-latest``.

Ollama
======

*  ``enable_ollama_model``
*  ``ollama_api_url``
*  ``ollama_api_model``

Use this for a local Ollama server. No cloud API key is required when Ollama
is the selected provider.

Content Blocks package
======================

``selectPackageToStoreAiContentElements`` is the extension that receives
generated Content Blocks. You can still pick another installed package in the
create dialog.

Next: :ref:`content-blocks` or :ref:`ai-forms`.
