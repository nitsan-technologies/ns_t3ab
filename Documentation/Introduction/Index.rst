.. include:: ../Includes.txt

.. _introduction:

============
Introduction
============

T3AB (``EXT:ns_t3ab``) adds two backend tools:

*  **Content Blocks**, under :guilabel:`Administration > T3AB (Content Blocks)`.
   Describe a content block and generate it into a local extension. This needs
   `friendsoftypo3/content-blocks`.
*  **AI Forms**, in the Form module. Describe a form and generate Form
   Framework fields. This needs ``typo3/cms-form``.

Supported TYPO3 versions are 12.4, 13.4, and 14.3. PHP 8.2 to 8.5.

Providers are OpenAI, Gemini, Claude, Azure OpenAI, DeepSeek, xAI, and an
optional local Ollama server. Each provider uses the API key stored in
extension configuration.

The product page is https://t3planet.de/t3ab-typo3-erweiterung.
