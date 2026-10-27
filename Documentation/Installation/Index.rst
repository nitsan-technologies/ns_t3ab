.. include:: ../Includes.txt

.. _installation:

============
Installation
============

Composer
========

.. code-block:: bash

   composer require nitsan/ns-t3ab
   vendor/bin/typo3 extension:setup

This also installs ``nitsan/ns-license`` 14.3 or later. T3AB does not call the
license server itself.

For Content Blocks, also require the matching ``friendsoftypo3/content-blocks``
release: 0.7.x on TYPO3 12, 1.x on 13, and 2.x on 14.

Classic mode
============

Install ``ns_t3ab`` in :guilabel:`Admin Tools > Extensions`.
