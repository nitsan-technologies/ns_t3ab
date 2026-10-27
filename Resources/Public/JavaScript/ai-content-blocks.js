import Modal from "@typo3/backend/modal.js";
import Severity from "@typo3/backend/severity.js";
import Notification from "@typo3/backend/notification.js";

initializeModule();

/**
 * Initialize the main module logic.
 */
async function initializeModule() {
    try {
        const loaderDiv = await waitForElement('#ns-t3ab__loader');

        const regenerateForm = document.querySelector('#content-block-regenerate-form');
        if (regenerateForm) {
            regenerateForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (regenerateForm.checkValidity()) {
                    showLoader(loaderDiv);
                    regenerateForm.submit();
                } else {
                    setFieldInvalid(regenerateForm);
                }
            });
        }

        const fieldGenerationForm = document.querySelector('#content-blocks-fields-generation');
        if (fieldGenerationForm) {
            fieldGenerationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (fieldGenerationForm.checkValidity()) {
                    showLoader(loaderDiv);
                    fieldGenerationForm.submit();
                } else {
                    setFieldInvalid(fieldGenerationForm);
                }
            });

            initializeContentBlockKeyHandling();
        }

        const generateBtn = document.querySelector('#create-content-element');
        if (generateBtn) {
            generateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                let generateForm = document.querySelector('#generate-content-element-form');
                if (generateForm && generateForm.checkValidity()) {
                    Modal.show(
                        TYPO3.lang['content_blocks.confirmation.title'] || 'Create Content Element',
                        TYPO3.lang['content_blocks.confirmation.description'] || 'Are you sure want to create content element?',
                        Severity.warning,
                        [
                            {
                                text: TYPO3.lang['content_blocks.confirm.cancel'] || 'Cancel',
                                btnClass: 'btn-default',
                                name: 'cancel',
                                trigger: (_, modal) => modal.hideModal()
                            },
                            {
                                text: 'Yes',
                                btnClass: 'btn-warning',
                                name: 'confirm',
                                trigger: (_, modal) => {
                                    modal.hideModal();
                                    showLoader(loaderDiv);
                                    generateForm.submit();
                                }
                            }
                        ]
                    );
                } else {
                    setFieldInvalid(generateForm);
                    Notification.warning(TYPO3.lang['content_blocks.form_validation_error']);
                }
            });
        }

        initAddNewField();
        initCustomIconSelector();
        syncAiEngineTabs();

    } catch (err) {
        console.error(err);
    }
}

/**
 * Dynamically initializes content block key handling once all necessary elements are available.
 */
function initializeContentBlockKeyHandling() {
    let isInitialized = false;
    let isBlurSet = false;

    const interval = setInterval(() => {
        const contentBlockKey = document.querySelector("[name='contentBlockKey']");
        const contentBlockKeyInput = document.querySelector('#content-block-key');
        const elementName = document.querySelector('#element-name');
        const differentProposalField = document.querySelector('.t3js-form-proposal-different');
        const packageName = document.querySelector("[name='packageName']");

        if (elementName && contentBlockKeyInput && !isBlurSet) {
            isBlurSet = true;
            elementName.addEventListener('blur', (e) => {
                contentBlockKeyInput.value = e.target.value;
                contentBlockKeyInput.dispatchEvent(new Event('change'));
            });
        }

        if (contentBlockKey && contentBlockKeyInput && differentProposalField && packageName && !isInitialized) {
            isInitialized = true;
            clearInterval(interval);

            const handleContentBlockKey = async (e) => {
                if (e.target.value !== '') {
                    let formData = new FormData();
                    formData.append('providedKey', e.target.value);
                    formData.append('packageName', packageName.value);

                    try {
                        const response = await fetch(TYPO3.settings.ajaxUrls['content_block_key_suggest'], {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        contentBlockKey.setAttribute('value', data.validateKey);
                        contentBlockKeyInput.value = data.validateKey;
                        differentProposalField.querySelector('span').innerText = data.validateKey;
                        differentProposalField.classList.remove('hidden');
                    } catch (error) {
                        Notification.error(TYPO3.lang['t3ab.error'], error);
                    }
                } else {
                    differentProposalField.classList.add('hidden');
                }
            };

            contentBlockKeyInput.addEventListener('keyup', handleContentBlockKey);
            contentBlockKeyInput.addEventListener('change', handleContentBlockKey);
        }
    }, 300);
}

/**
 * Add new field dynamically into field list section.
 */
function initAddNewField() {
    const addNewFieldBtn = document.querySelector('#add-new-field');
    const listSection = document.querySelector('#content-blocks-field-list');
    const addContentData = document.querySelector('#new-row-content');

    if (addNewFieldBtn && listSection && addContentData) {
        addNewFieldBtn.addEventListener('click', (e) => {
            e.preventDefault();
            let newItem = addContentData.querySelector('.list-group-item').cloneNode(true);
            let uniqueId = getUniqueId('NEW');

            let checkbox = newItem.querySelector('input[type="checkbox"]');
            if (checkbox) checkbox.name = `new-required[${uniqueId}]`;

            let label = newItem.querySelector('input[name^="new-label"]');
            if (label) label.name = `new-label[${uniqueId}]`;

            let select = newItem.querySelector('select[name^="new-type"]');
            if (select) select.name = `new-type[${uniqueId}]`;

            listSection.appendChild(newItem);

            let deleteButton = newItem.querySelector('.sortable-wrap button.delete');
            if (deleteButton) {
                deleteButton.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    this.closest(".list-group-item.typo3-form").remove();
                });
            }
        });
    }
}

/**
 * Initialize custom icon select box functionality.
 */
function initCustomIconSelector() {
    const trigger = document.querySelector('.ns-t3ab-custom-select-trigger');
    const options = document.querySelector('.ns-t3ab-custom-options');
    const iconField = document.querySelector("[name='cbIcon']");

    if (trigger && options && iconField) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        options.querySelectorAll('.ns-t3ab-custom-option').forEach(option => {
            option.addEventListener('click', (e) => {
                e.stopPropagation();
                const identifier = e.target.getAttribute('data-att');
                iconField.setAttribute('value', identifier);
                trigger.innerHTML = option.innerHTML;
                options.style.display = 'none';
            });
        });

        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target) && !options.contains(e.target)) {
                options.style.display = 'none';
            }
        });
    }
}

/**
 * Create a unique ID.
 * @param {string} prefix
 * @returns {string}
 */
function getUniqueId(prefix = '') {
    const unique = (Date.now().toString(36) + Math.random().toString(36).substr(2));
    return prefix + unique;
}

/**
 * Show the loader.
 * @param {HTMLElement} loaderDiv
 */
function showLoader(loaderDiv) {
    if (loaderDiv) {
        loaderDiv.classList.add('ns-show-overlay');
    }
}

/**
 * Waits for a DOM element to be present.
 * @param {string} selector
 * @param {number} timeout
 * @returns {Promise<HTMLElement>}
 */
function waitForElement(selector, timeout = 5000) {
    return new Promise((resolve, reject) => {
        const intervalTime = 200;
        let timePassed = 0;
        const interval = setInterval(() => {
            const element = document.querySelector(selector);
            if (element) {
                clearInterval(interval);
                resolve(element);
            }
            timePassed += intervalTime;
            if (timePassed >= timeout) {
                clearInterval(interval);
                reject(`Timeout: ${selector} not found`);
            }
        }, intervalTime);
    });
}

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