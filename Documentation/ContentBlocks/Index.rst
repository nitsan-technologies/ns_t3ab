.. include:: ../Includes.txt

.. _content-blocks:

==============
Content Blocks
==============

The module is :guilabel:`Administration > T3AB (Content Blocks)`. It lists
content blocks that T3AB created. Creating a block needs
``friendsoftypo3/content-blocks``. See :ref:`installation` for the version
that matches your TYPO3 release.

Interactive demos:

*  `T3AB Content Blocks <https://app.supademo.com/demo/cmdyd06zf8l369f96iacjds6b>`__
*  `Create Content Blocks <https://app.supademo.com/demo/cmjvboson9nmf3zz2migyyycc>`__
*  `AI Powered TYPO3 Content Block <https://app.supademo.com/demo/cmdyd94lv8lbw9f9643kq97l5>`__

Create a block
==============

#. Open the module and start a new content block.
#. Choose the AI provider and model. The dialog shows one provider at a time.
#. Choose the package that should store the block. The default comes from
   ``selectPackageToStoreAiContentElements``.
#. Choose a wizard category, then enter the name, description, and content
   block key. On TYPO3 13 and 14 you can also choose an icon.
#. Describe the fields in the prompt. For example: “two-column layout with
   an image and text” or “testimonial with quote, name, and role”.
#. Submit. T3AB asks the selected provider for a field list.
#. Review the proposed fields. Change the prompt and generate again if the
   fields are wrong.
#. Save. T3AB writes the block into the selected package and clears the caches.

The content block key may contain lowercase letters, numbers, and single or
double hyphens. If that key already exists in the package, T3AB suggests
another one.

What is stored
==============

The block is a normal Content Blocks element inside the host extension. The
definition includes:

*  ``config.yaml``, including the T3AB title and description so the block
   stays in this module’s list
*  a frontend Fluid template
*  a backend preview template
*  a language file
*  an icon on TYPO3 13 and 14, when you selected one

After a cache clear, the new element is available in the page module under
the category you selected.

Delete a block
==============

Delete from the list. T3AB copies the block into the default file storage at
``t3ab/content_blocks_backup/`` (usually under ``fileadmin/``) and then
removes it from the host extension.

If the module says Content Blocks is not installed, require
``friendsoftypo3/content-blocks`` and flush the caches.
