.. include:: ../Includes.txt

.. _ai-forms:

========
AI Forms
========

AI Forms lives in the TYPO3 Form module and needs ``typo3/cms-form``. A form
is saved through the Form Framework into an allowed form storage, then opened
in the Form Editor.

Interactive demos:

*  `T3AB AI Forms <https://app.supademo.com/demo/cmdycvcmj8koo9f963beq8u9v>`__
*  `Create Forms <https://app.supademo.com/demo/cmjveaijy9okx3zz2ty1an7ku>`__

Create a form
=============

#. Open the Form module and start an AI form.
#. Choose the AI provider and model.
#. Describe the form. For example: “contact form with name, email, subject,
   and message” or “event registration with name, company, and ticket type”.
#. Submit. T3AB proposes fields. You can change the type and label of each
   field, add a field, or remove one.
#. Generate again from the same prompt if you want a new proposal.
#. Save. TYPO3 redirects to the Form Editor for the new form.

Finish the form in the Form Editor: finishers, recipients, layout, and the
pages where the form is inserted. T3AB does not send mail and does not publish
the form on a page.

The form identifier is unique. Saving again does not overwrite an existing
form with the same name.

If generation fails, check the API key for the selected provider in
:ref:`configuration`, and check that the Form module has a writable
persistence mount.
