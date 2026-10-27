.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============

Open :guilabel:`Admin Tools > Settings > Extension Configuration > ns_t3ab`.

``defaultModel`` selects the provider shown when a dialog opens:
``openai``, ``gemini``, ``azure``, ``claude``, ``ollama``, ``deepseek``, or
``xai``.

Store an API key for each provider you use:

*  OpenAI: ``openai_api_key``, ``openai_model`` (default ``gpt-4.1``)
*  Gemini: ``gemini_api_key``, ``gemini_model`` (default ``gemini-3.5-flash-lite``)
*  Claude: ``claude_api_key``, ``claude_api_model`` (default ``claude-sonnet-5-5``)
*  Azure OpenAI: ``azure_api_key``, ``azure_api_endpoint``, ``azure_api_model``,
   ``azure_api_version``
*  DeepSeek: ``deepseek_api_key``, ``deepseek_model`` (default ``deepseek-flash``)
*  xAI: ``xai_api_key``, ``xai_model`` (default ``grok-2-latest``)
*  Ollama: ``enable_ollama_model``, ``ollama_api_url``, ``ollama_api_model``

``selectPackageToStoreAiContentElements`` is the package that receives
generated Content Blocks.
