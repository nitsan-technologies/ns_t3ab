.. include:: ../Includes.txt

.. _troubleshooting:

===============
Troubleshooting
===============

.. _faq:

Frequently asked questions
==========================

What can T3AB build?
   Content blocks and Form Framework forms, from a description in plain
   language. The block is stored in a local extension. The form opens in the
   Form Editor.

Do I need a license key?
   T3AB does not call the license server. ``nitsan/ns-license`` 14.3 or later
   is installed with the extension.

Which provider should I use?
   Any provider with a valid API key. OpenAI, Gemini, Claude, Azure OpenAI,
   DeepSeek, and xAI are cloud APIs. Ollama is a local server.

Where do I put the API key?
   :guilabel:`Admin Tools > Settings > Extension Configuration > ns_t3ab`.
   See :ref:`configuration`.

Why does the Content Blocks module say Content Blocks is not installed?
   Require ``friendsoftypo3/content-blocks`` for your TYPO3 version (0.7.x on
   12, 1.x on 13, 2.x on 14) and flush the caches.

Why did generation fail?
   The selected provider has no key, the key is rejected, or the provider
   timed out. Switch provider in the dialog or check the key. The dialog
   shows one provider’s models at a time.

Where did my content block go?
   Into the package selected in the dialog, or the package set in
   ``selectPackageToStoreAiContentElements``. After a cache clear it appears
   in the page module and in :guilabel:`Administration > T3AB (Content Blocks)`.

Can I recover a deleted content block?
   Delete copies the block to ``t3ab/content_blocks_backup/`` in the default
   file storage before removing it. See :ref:`content-blocks`.

Something else is not working
   Open a ticket at https://t3planet.de/support with your TYPO3, PHP, and
   T3AB versions and the exact error text. Source issues:
   https://github.com/nitsan-technologies/ns_t3ab/issues
