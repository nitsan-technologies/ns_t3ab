<?php

declare(strict_types=1);

use NITSAN\NsT3Ab\Controller\FormsModuleController;
use NITSAN\NsT3Ab\Controller\ContentBlocksModuleController;


return [
    't3ab_generate_ai_form_fields' => [
        'path' => '/generate/ai-form-fields',
        'target' => FormsModuleController::class . '::generateFormFieldsAction',
    ],
    'ai_form' => [
        'path' => '/get/ai-form/config',
        'target' => FormsModuleController::class . '::renderFormGenerationTemplateAction'
    ],
    't3ab_ai_form_create' => [
        'path' => '/create/ai-form',
        'target' => FormsModuleController::class . '::createAiFormAction',
    ],
    'content_blocks_field_generation' => [
        'path' => '/content-blocks-field-generation',
        'target' => ContentBlocksModuleController::class . '::contentElementFieldPreviewAction',
    ],
    'create_content_element' => [
        'path' => '/create-content-element',
        'target' => ContentBlocksModuleController::class . '::createContentElementAction',
    ],
    'ai_content_blocks' => [
        'path' => '/get/ai-content-element/config',
        'target' => ContentBlocksModuleController::class . '::renderContentElementGenerationTemplateAction'
    ],
];