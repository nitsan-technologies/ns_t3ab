.. include:: ../Includes.txt

.. _quick-configuration:

===================
Quick Configuration
===================

Store one API key, then create a block or a form. The full setting list is
in :ref:`configuration`.

#. Open :guilabel:`Admin Tools > Settings > Extension Configuration > ns_t3ab`.
#. Set :guilabel:`defaultModel` to the provider you want the dialog to open
   with: ``openai``, ``gemini``, ``azure``, ``claude``, ``ollama``,
   ``deepseek``, or ``xai``.
#. Enter the API key for that provider. For Ollama, enable the model and set
   the local server URL instead.
#. For Content Blocks, set the package that should receive generated blocks
   (``selectPackageToStoreAiContentElements``).
#. Save and clear the caches.

Walkthrough: `T3AB Configuration <https://app.supademo.com/demo/cmdy6mkbl8a2j9f96fxtfioeu>`__.

First content block
===================

#. Install ``friendsoftypo3/content-blocks`` for your TYPO3 version.
#. Open :guilabel:`Administration > T3AB (Content Blocks)`.
#. Create a block, describe the fields, review the preview, and save it into
   the selected package.

See :ref:`content-blocks`.

First form
==========

#. Open the Form module.
#. Start an AI form, describe it (for example “contact form with name, email,
   subject, and message”), adjust the fields, and save.
#. TYPO3 opens the Form Editor for that form.

See :ref:`ai-forms`.
