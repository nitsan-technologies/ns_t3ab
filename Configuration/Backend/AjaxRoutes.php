<?php

declare(strict_types=1);

use NITSAN\NsT3Ab\Controller\FormsModuleController;
use NITSAN\NsT3Ab\Controller\ContentBlocksModuleController;
use NITSAN\NsT3Ab\Controller\FeatureAndBugReportController;

return [
    'content_block_key_suggest' => [
        'path' => '/content-blocks/key/suggest',
        'target' => ContentBlocksModuleController::class . '::keySuggestAction',
    ],
    'ai_content_blocks' => [
        'path' => '/get/ai-content-element/config',
        'target' => ContentBlocksModuleController::class . '::renderContentElementGenerationTemplateAction'
    ],
    'ai_content_blocks_delete' => [
        'path' => '/content-block/delete',
        'target' => ContentBlocksModuleController::class . '::deleteContentBlockAction'
    ],
    'ai_form' => [
        'path' => '/get/ai-form/config',
        'target' => FormsModuleController::class . '::renderFormGenerationTemplateAction'
    ],
    'ai_content_block_files_preview' => [
        'path' => '/content-block/files/preview',
        'target' => ContentBlocksModuleController::class . '::filesPreviewAction'
    ],
    'feature_suggestion' => [
        'path' => '/t3ab/feature-suggestion',
        'target' => FeatureAndBugReportController::class . '::featureSuggestionAction'
    ],
    'bug_report' => [
        'path' => '/t3ab/bug-report',
        'target' => FeatureAndBugReportController::class . '::bugReportAction'
    ],
    'submit_feature_bug' => [
        'path' => '/t3ab/submit-feature-bug',
        'target' => FeatureAndBugReportController::class . '::submitFeatureBugAction'
    ],
];
