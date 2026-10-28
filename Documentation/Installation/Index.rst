.. include:: ../Includes.txt

.. _installation:

============
Installation
============

Requirements
============

*  TYPO3 12.4, 13.4, or 14.3
*  PHP 8.2 to 8.5
*  ``nitsan/ns-license`` 14.3 or later, installed with this extension. T3AB
   does not call the license server.
*  ``typo3/cms-form`` for AI Forms
*  ``friendsoftypo3/content-blocks`` for the Content Blocks module

Content Blocks versions
=======================

================ ============================== =================
TYPO3            Package                        Major
================ ============================== =================
12.4             friendsoftypo3/content-blocks  0.7.x
13.4             friendsoftypo3/content-blocks  1.x
14.3             friendsoftypo3/content-blocks  2.x
================ ============================== =================

Without Content Blocks, the module explains that it is not installed. AI
Forms still work.

Composer
========

..  code-block:: bash

    composer require nitsan/ns-t3ab
    vendor/bin/typo3 extension:setup

Classic mode
============

Install ``ns_t3ab`` in :guilabel:`Admin Tools > Extensions`. Install
``ns_license`` first. See :ref:`quick-installation`.

Next: :ref:`configuration`.
