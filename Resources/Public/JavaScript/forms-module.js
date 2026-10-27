import Notification from "@typo3/backend/notification.js";
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";

let fieldGenerateForm = document.querySelector("#fieldGenerateForm");
let regenerationForm = document.querySelector("#regenerateForm");
let generateForm = document.querySelector("#generateForm");

[fieldGenerateForm, regenerationForm, generateForm].forEach((form) => {
  if (form) {
    form.addEventListener("submit", (e) => {
      let loaderDiv = document.querySelector("#ns-t3ab__loader");
      if (form.checkValidity()) {
        showLoader(loaderDiv);
        if (form === generateForm) {
          e.preventDefault();
          handleGenerateForm(form);
        } else {
          form.submit();
        }
      } else {
        e.preventDefault();
      }
    });
  }
})
syncAiEngineTabs();

function syncAiEngineTabs() {
    const tabList = document.querySelector('#api-model');
    if (!tabList) {
        return;
    }
    document.addEventListener('typo3:tab:shown', (event) => {
        const tab = event.target;
        if (!(tab instanceof HTMLElement) || !tabList.contains(tab)) {
            return;
        }
        const targetSelector = tab.getAttribute('data-typo3-tab');
        const target = targetSelector ? document.querySelector(targetSelector) : null;
        if (!target) {
            return;
        }
        tabList.querySelectorAll('label.active').forEach((label) => {
            if (label !== tab) {
                label.classList.remove('active');
            }
        });
        document.querySelectorAll('.text-api-model-collapse').forEach((panel) => {
            panel.classList.toggle('active', panel === target);
        });
        const inputId = tab.getAttribute('for');
        const input = inputId ? document.getElementById(inputId) : null;
        if (input instanceof HTMLInputElement && input.type === 'radio') {
            input.checked = true;
        }
    });
}

function handleGenerateForm(form) {
  if (form.checkValidity()) {
    let loaderDiv = document.querySelector("#ns-t3ab__loader");
    let url = form.getAttribute('action');
    let formData = new FormData(form);
    new AjaxRequest(url)
      .post(formData)
      .then(async function (response) {
        const responseBody = await response.resolve();
        if (responseBody.error) {
          Notification.error(TYPO3.lang["error_title"], responseBody.error);
        } else {
          Notification.success(TYPO3.lang['module.forms.generate_ai_form.success']);
          if (responseBody.redirectUrl) {
            setTimeout(function() {
              top.window.location.replace(responseBody.redirectUrl);
            }, 3000);
          }
        }
      })
      .catch((error) => {
        Notification.error(TYPO3.lang["error_title"], error);
        hideLoader(loaderDiv)
      });
  } else {
    setFieldInvalid(form);
    Notification.warning(TYPO3.lang['content_blocks.form_validation_error']);
  }
}

function showLoader(loaderDiv) {
  if (loaderDiv) {
    loaderDiv.classList.add('ns-show-overlay');
  }
}

function hideLoader(loaderDiv) {
  if (loaderDiv) {
    loaderDiv.classList.remove("ns-show-overlay");
  }
}

function setFieldInvalid(form)
{
    const invalidFields = form.querySelectorAll(':invalid');
    const validFields = form.querySelectorAll(':valid');

    invalidFields.forEach(field => {
        field.classList.add('border-danger');
    });

    validFields.forEach(field => {
        field.classList.remove('border-danger');
    });
}