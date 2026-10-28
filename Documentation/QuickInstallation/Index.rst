.. include:: ../Includes.txt

.. _quick-installation:

==================
Quick Installation
==================

Installing T3AB is quick. Choose one of the ways below. For requirements and
the Content Blocks version matrix, see :ref:`installation`.

.. _quick-installation-composer:

Via Composer using the command line
===================================

..  code-block:: bash
    :caption: Install via Composer

    composer require nitsan/ns-t3ab
    vendor/bin/typo3 extension:setup
    vendor/bin/typo3 cache:flush

Composer also installs ``nitsan/ns-license`` 14.3 or later. For Content
Blocks, also require the matching ``friendsoftypo3/content-blocks`` release.

.. _quick-installation-em:

Via the Extensions module
=========================

In classic mode (without Composer) you can use the Extension Manager in the
TYPO3 backend.

#. Open :guilabel:`Admin Tools > Extensions`.
#. Choose :guilabel:`Get Extensions` from the drop-down at the top, click
   :guilabel:`Update now` to refresh the extension list, then search for
   ``ns_t3ab`` and click :guilabel:`Import and Install`. Install
   ``ns_license`` the same way first.
#. Activate the extension.
#. Clear all caches.

You can also download the extension from the
`TYPO3 Extension Repository (TER) <https://extensions.typo3.org/extension/ns_t3ab>`__.

Next: :ref:`quick-configuration`.
