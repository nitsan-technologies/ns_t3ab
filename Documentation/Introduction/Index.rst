.. include:: ../Includes.txt

.. _introduction:

============
Introduction
============

T3AB (``EXT:ns_t3ab``) adds two backend tools that turn a short description
into TYPO3 code:

*  **Content Blocks**, under :guilabel:`Administration > T3AB (Content Blocks)`.
   Describe a block. T3AB proposes the fields, you review them, and the block
   is written into a local extension. This needs
   `friendsoftypo3/content-blocks <https://github.com/friendsoftypo3/content-blocks>`__.
*  **AI Forms**, in the Form module. Describe a form. T3AB proposes the
   fields, you adjust them, and the form is saved with the TYPO3 Form
   Framework. This needs ``typo3/cms-form``.

You bring your own API key. Keys stay in extension configuration. Supported
providers are OpenAI, Gemini, Claude, Azure OpenAI, DeepSeek, xAI, and an
optional local Ollama server. The create dialog shows one provider at a time.

Supported TYPO3 versions are 12.4, 13.4, and 14.3. PHP 8.2 to 8.5.

``nitsan/ns-license`` 14.3 or later is installed with this extension. T3AB
does not call the license server.

Examples you can describe:

*  A hero section with a headline, text, and image.
*  A testimonial card, an info box, or a call to action.
*  A contact form with name, email, subject, and message.
*  A registration form or a short survey.

The product page is https://t3planet.de/t3ab-typo3-erweiterung.

Next: :ref:`quick-installation`.
