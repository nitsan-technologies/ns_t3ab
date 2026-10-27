import $ from "jquery";
import Icons from "@typo3/backend/icons.js";
import Modal from "@typo3/backend/modal.js";
import Severity from "@typo3/backend/severity.js";
import Notification from "@typo3/backend/notification.js";
import AjaxRequest from "@typo3/core/ajax/ajax-request.js";

const newAiContentBlockBtn = document.querySelector('#create-new-content-block');

if (newAiContentBlockBtn) {
    newAiContentBlockBtn.addEventListener('click', (e) => {
        let requestUrl = TYPO3.settings.ajaxUrls['ai_content_blocks'] + '&pageId=' + getPageId();
        let modalTitle = TYPO3.lang['new_content_block.modal_title'];
        Modal.advanced({
            type: Modal.types.iframe,
            title: modalTitle,
            content: requestUrl,
            size: Modal.sizes.large,
            callback: (currentModal) => {
                $(currentModal).find("iframe").on("load", function () {
                    Icons.getIcon('t3ab_content_blocks', Icons.sizes.small, null, 'default', 'inline').then(function (markup) {
                        var titleWithIcon = markup + ' ' + modalTitle;
                        currentModal.querySelector('.modal-header .t3js-modal-title').innerHTML = titleWithIcon;
                    });
                    let iframeDocument = $(this).contents();
                    let closeBtn = iframeDocument.find('[t3ab-modal="close"]');
                    handleReportIssueModal(iframeDocument);
                    handleFeatureSuggestionModal(iframeDocument);
                    if (closeBtn.length > 0) {
                        let hasCloseReload = closeBtn.attr('t3ab-close-reload') && closeBtn.attr('t3ab-close-reload') === '1';
                        let headerClose = currentModal.querySelector('.modal-header .t3js-modal-close');
                        if (hasCloseReload && headerClose) {
                            headerClose.addEventListener('click', () => {
                                location.reload();
                            })
                        }
                        closeBtn.on('click', () => {
                            currentModal.hideModal();
                            if (hasCloseReload) {
                                location.reload();
                            }
                        })
                    }
                })
            }
        })
    })
}

const deleteAiContentBlockBtn = document.querySelectorAll('.content-block-delete');

deleteAiContentBlockBtn.forEach((deleteBtn) => {
    deleteBtn.addEventListener('click', (event) => {
        let modalButtons = [];
        let loaderDiv = document.querySelector('#ns-t3ab__loader');
        modalButtons.push({
            text: TYPO3.lang['modal.cancel'] || 'Cancel',
            active: true,
            btnClass: 'btn-default',
            name: 'cancel',
            trigger: function (e, t) {
                t.hideModal();
            }
        });
        modalButtons.push({
            text: TYPO3.lang['modal.yes'] || 'Yes',
            active: true,
            btnClass: 'btn-warning',
            name: 'confirm',
            trigger: function (e, t) {
                showLoader(loaderDiv);
                t.hideModal();
                let formData = new FormData();
                formData.append('cbExtPath', deleteBtn.getAttribute('data-ext-path'));
                formData.append('cbName', deleteBtn.getAttribute('data-name'));

                new AjaxRequest(TYPO3.settings.ajaxUrls['ai_content_blocks_delete'])
                    .post(formData)
                    .then(async (response) => {
                        let responseBody = await response.resolve();
                        if (responseBody.success) {
                            let message = '';
                            if (responseBody.isBackup && responseBody.backupDir && responseBody.backupDir !== '') {
                                message = TYPO3.lang["content_block.delete.backup.path"] + ' ' + responseBody.backupDir + '.'
                            }
                            Notification.success(TYPO3.lang['content_block.delete.success.title'], message);
                        }
                        if (responseBody.error && responseBody.error !== '') {
                            Notification.error(TYPO3.lang["error_title"], responseBody.error);
                        }
                    })
                    .catch((error) => {
                        Notification.error(TYPO3.lang["error_title"], error);
                    })
                    .finally(() => {
                        hideLoader(loaderDiv);
                        window.location.reload();
                    });
            }
        });
        Modal.show(
            TYPO3.lang['content_block.delete_modal.title'] || 'Delete Content Blocks',
            TYPO3.lang['content_block.delete_modal.description'] || 'Are you sure want to register this content block?',
            Severity.warning,
            modalButtons
        );
    })
});

const newAiFormBtn = document.querySelectorAll('.create-ai-form');

newAiFormBtn.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        let requestUrl = TYPO3.settings.ajaxUrls['ai_form'] + '&pageId=' + getPageId();
        let modalTitle = TYPO3.lang['new_ai_form.modal_title'];
        Modal.advanced({
            type: Modal.types.iframe,
            title: modalTitle,
            content: requestUrl,
            size: Modal.sizes.medium,
            callback: (currentModal) => {
                $(currentModal).find("iframe").on("load", function () {
                    Icons.getIcon('t3ab_forms', Icons.sizes.small, null, 'default', 'inline').then(function (markup) {
                        var titleWithIcon = markup + ' ' + modalTitle;
                        currentModal.querySelector('.modal-header .t3js-modal-title').innerHTML = titleWithIcon;
                    });
                    let iframeDocument = $(this).contents();
                    let closeBtn = iframeDocument.find('[t3ab-modal="close"]');
                    handleReportIssueModal(iframeDocument);
                    handleFeatureSuggestionModal(iframeDocument);
                    if (closeBtn.length > 0) {
                        closeBtn.on('click', () => {
                            currentModal.hideModal();
                        })
                    }
                })
            }
        })
    })
})

const contentBlockFilesPreview = document.querySelectorAll('.content-block-files-preview');

contentBlockFilesPreview.forEach((preview) => {
    preview.addEventListener('click', (e) => {
        let title = preview.getAttribute('data-title');
        let packageName = preview.getAttribute('data-package-name');
        let contentBlockKey = preview.getAttribute('data-content-block-key');
        let requestUrl = TYPO3.settings.ajaxUrls['ai_content_block_files_preview'];
        let url = new URL(requestUrl, window.location.origin);
        url.searchParams.append('packageName', packageName);
        url.searchParams.append('contentBlockKey', contentBlockKey);
        let finalUrl = url.toString();
        let modalTitle = title;
        Modal.advanced({
            type: Modal.types.iframe,
            title: modalTitle,
            content: finalUrl,
            size: Modal.sizes.large,
            callback: (currentModal) => {
                $(currentModal).find("iframe").on("load", function () {
                    Icons.getIcon('t3ab_content_blocks', Icons.sizes.small, null, 'default', 'inline').then(function (markup) {
                        var titleWithIcon = markup + ' ' + modalTitle;
                        currentModal.querySelector('.modal-header .t3js-modal-title').innerHTML = titleWithIcon;
                    });
                    let iframeDocument = $(this).contents();
                    let closeBtn = iframeDocument.find('[t3ab-modal="close"]');
                    handleReportIssueModal(iframeDocument);
                    handleFeatureSuggestionModal(iframeDocument);
                    if (closeBtn.length > 0) {
                        closeBtn.on('click', () => {
                            currentModal.hideModal();
                        })
                    }
                })
            }
        });
    })
})

function handleReportIssueModal(iframeDocument)
{
    let reportIssueBtn = iframeDocument.find('[data-button="reportIssue"]');
    if (reportIssueBtn.length > 0) {
        reportIssueBtn.on('click', () => {
            let modalTitle = TYPO3.lang['bug_report.modal.title'] || "Report an Issue";
            Modal.advanced({
                type: Modal.types.iframe,
                title: modalTitle,
                content: TYPO3.settings.ajaxUrls['bug_report'],
                size: Modal.sizes.medium,
                severity: Severity.notification,
                callback: (currentModal) => {
                    $(currentModal).find("iframe").on("load", function () {
                        Icons.getIcon('t3ab_magic_black', Icons.sizes.small, null, 'default', 'inline').then(function (markup) {
                            var titleWithIcon = markup + ' ' + modalTitle;
                            currentModal.querySelector('.modal-header .t3js-modal-title').innerHTML = titleWithIcon;
                        });
                        let iframeDocument = $(this).contents();
                        let closeBtn = iframeDocument.find('[t3ab-modal="close"]');
                        if (closeBtn.length > 0) {
                            closeBtn.on('click', () => {
                                currentModal.hideModal();
                            })
                        }
                        let bugReportForm = iframeDocument.find('#bug-report');
                        if (bugReportForm.length > 0) {
                            bugReportForm.on('submit', function (e) {
                                e.preventDefault();
                                let formData = new FormData(this);
                                fetch(TYPO3.settings.ajaxUrls['submit_feature_bug'], {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => response.json())
                                .then((data) => {
                                    if (data.success) {
                                        Notification.success(TYPO3.lang['bug_report.success'] || "Issue reported successfully");
                                    } else {
                                        Notification.error(data.error || TYPO3.lang['error'] || "Something went wrong. Please try again.");
                                    }
                                })
                                .catch((error) => {
                                    console.log(error);
                                })
                                .finally(()=> {
                                    currentModal.hideModal()
                                })
                            });
                        }
                    })
                }
            });
        })
    }
}

function handleFeatureSuggestionModal(iframeDocument)
{
    let featureSuggestionBtn = iframeDocument.find('[data-button="suggestFeature"]');
    if (featureSuggestionBtn.length > 0) {
        featureSuggestionBtn.on('click', () => {
            let modalTitle = TYPO3.lang['feature_suggestion.modal.title'] || "Suggest Feature";
            Modal.advanced({
                type: Modal.types.iframe,
                title: modalTitle,
                content: TYPO3.settings.ajaxUrls['feature_suggestion'],
                size: Modal.sizes.medium,
                severity: Severity.notification,
                callback: (currentModal) => {
                    $(currentModal).find("iframe").on("load", function () {
                        Icons.getIcon('t3ab_magic_black', Icons.sizes.small, null, 'default', 'inline').then(function (markup) {
                            var titleWithIcon = markup + ' ' + modalTitle;
                            currentModal.querySelector('.modal-header .t3js-modal-title').innerHTML = titleWithIcon;
                        });
                        let iframeDocument = $(this).contents();
                        let closeBtn = iframeDocument.find('[t3ab-modal="close"]');
                        if (closeBtn.length > 0) {
                            closeBtn.on('click', () => {
                                currentModal.hideModal();
                            })
                        }

                        let featureForm = iframeDocument.find('#feature-suggestion');
                        if (featureForm.length > 0) {
                            featureForm.on('submit', function (e) {
                                e.preventDefault();
                                let formData = new FormData(this);
                                fetch(TYPO3.settings.ajaxUrls['submit_feature_bug'], {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => response.json())
                                .then((data) => {
                                    if (data.success) {
                                        Notification.success(TYPO3.lang['feature_suggestion.success'] || "Your request has been submitted successfully");
                                    } else {
                                        Notification.error(data.error || TYPO3.lang['error'] || "Something went wrong. Please try again.");
                                    }
                                })
                                .catch((error) => {
                                    console.log(error);
                                })
                                .finally(()=> {
                                    currentModal.hideModal()
                                })
                            });
                        }
                    })
                }
            });
        })
    }
}

const formEditLinks = document.querySelectorAll(".edit-form-link");

formEditLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        window.top.location = link.getAttribute('data-edit-url');
    })
})

function getPageId() {
    let params = new URL(location.href).searchParams;
    return params.get('id');
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