<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Utility;

use TYPO3\CMS\Core\Utility\StringUtility;

class ContentBlocksYamlConfig
{
    /**
     * @var array
     */
    private static $allowedTextFields = [
        'Text',
        'Email',
        'Textarea',
        'Number',
        'DateTime',
    ];

    /**
     * @var array
     */
    private static $allowedMultiSelectFields = [
        'Checkbox',
        'Radio',
        'Select',
    ];

    /**
     * @var array
     */
    private static $allowedFalFields = [
        'Image',
        'File',
    ];

    /**
     * @return array
     */
    public static function getContentBlocksFieldTypes(): array
    {
        return array_merge(
            self::$allowedTextFields,
            self::$allowedFalFields,
            self::$allowedMultiSelectFields
        );
    }

    /**
     * @return array
     */
    public static function getAllowedTextFields(): array
    {
        return self::$allowedTextFields;
    }

    /**
     * @return array
     */
    public static function getAllowedFalFields(): array
    {
        return self::$allowedFalFields;
    }

    /**
     * @return array
     */
    public static function getAllowedMultiSelectFields(): array
    {
        return self::$allowedMultiSelectFields;
    }

    /**
     * @param string $fieldType
     * @return string
     */
    private static function proceedFieldType(string $fieldType): string
    {
        $fieldType = ucfirst(strtolower($fieldType)); // Normalize input (e.g., 'email' -> 'Email')

        switch ($fieldType) {
            case 'Email':
                return 'Email';
            case 'File':
                return 'File';
            case 'Image':
                return 'Image';
            case 'Checkbox':
                return 'Checkbox';
            case 'Select':
                return 'Select';
            case 'Radio':
                return 'Radio';
            case 'Date':
            case 'Datetime':
            case 'Time':
                return 'DateTime';
            case 'Number':
            case 'Phone':
                return 'Number';
            case 'Textarea':
                return 'Textarea';
            default:
                return 'Text';
        }
    }

    /**
     * @param string $fieldType
     * @return boolean
     */
    public static function isValidField(string $fieldType): bool
    {
        return in_array(self::proceedFieldType($fieldType), self::getContentBlocksFieldTypes());
    }

    /**
     * @param array $field
     * @return array
     */
    public static function getFieldConfig(array $field): array
    {
        $fieldType = self::proceedFieldType($field['type'] ?? '');
        switch ($fieldType) {
            case 'Email':
                return self::emailFieldConfig($field);
            case 'File':
                return self::fileFieldConfig($field);
            case 'Image':
                return self::fileFieldConfig($field, 'image');
            case 'Checkbox':
                return self::optionsRelatedFieldConfig($field, 'Checkbox');
            case 'Select':
                return self::optionsRelatedFieldConfig($field, 'Select');
                ;
            case 'Radio':
                return self::optionsRelatedFieldConfig($field, 'Radio');
                ;
            case 'DateTime':
                return self::dateTimeFieldConfig($field);
            case 'Number':
                return self::numberFieldConfig($field);
            case 'Textarea':
                return self::textAreaFieldConfig($field);
            default:
                return self::textFieldConfig($field);
        }
    }

    /**
     * @param array $field
     * @return array
     */
    private static function textFieldConfig(array $field): array
    {
        $uniqueId = StringUtility::getUniqueId();
        return [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => 'Text',
            'label' => $field['label'] ?? 'Label',
            'placeholder' => $field['placeholder'] ?? '',
            'required' => (bool)$field['required'] ?? false,
        ];
    }

    /**
     * @param array $field
     * @return array
     */
    private static function emailFieldConfig(array $field): array
    {
        $uniqueId = StringUtility::getUniqueId();
        return [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => 'Email',
            'label' => $field['label'] ?? 'Label',
            'placeholder' => $field['placeholder'] ?? '',
            'required' => (bool)$field['required'] ?? false,
        ];
    }

    /**
     * @param array $field
     * @param string $type
     * @return array
     */
    private static function fileFieldConfig(array $field, $type = ''): array
    {
        $uniqueId = StringUtility::getUniqueId();
        $config = [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => 'File',
            'label' => $field['label'] ?? 'Label',
            'required' => (bool)$field['required'] ?? false,
        ];
        if ($type === 'image') {
            $config['allowed'] = 'common-image-types';
        }
        return $config;
    }

    /**
     * @param array $field
     * @param string $type
     * @return array
     */
    private static function optionsRelatedFieldConfig(array $field, string $type): array
    {
        $uniqueId = StringUtility::getUniqueId();
        if (empty($field['options'])) {
            return self::textFieldConfig($field);
        }
        $config = [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => $type,
            'label' => $field['label'] ?? 'Label',
            'required' => (bool)($field['required'] ?? false),
        ];
        if ($type === 'Select') {
            $config['renderType'] = 'selectSingle';
        }
        foreach ($field['options'] as $option) {
            $config['items'][] = [
                'label' => $option['label'] ?? '',
                'value' => $option['value'] ?? '',
            ];
        }
        return $config;
    }

    /**
     * @param array $field
     * @return array
     */
    private static function dateTimeFieldConfig(array $field): array
    {
        $uniqueId = StringUtility::getUniqueId();
        return [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => 'DateTime',
            'format' => 'datetime',
            'label' => $field['label'] ?? 'Label',
            'size' => 20,
            'required' => (bool)$field['required'] ?? false,
        ];
    }

    /**
     * @param array $field
     * @return array
     */
    private static function numberFieldConfig(array $field): array
    {
        $uniqueId = StringUtility::getUniqueId();
        return [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => 'Number',
            'label' => $field['label'] ?? 'Label',
            'format' => 'integer',
            'required' => (bool)$field['required'] ?? false,
        ];
    }

    /**
     * @param array $field
     * @return array
     */
    private static function textAreaFieldConfig(array $field): array
    {
        $uniqueId = StringUtility::getUniqueId();
        return [
            'identifier' => $field['identifier'] ?? $uniqueId,
            'type' => 'Textarea',
            'label' => $field['label'] ?? 'Label',
            'placeholder' => $field['placeholder'] ?? '',
            'required' => (bool)$field['required'] ?? false,
            'rows' => 15,
        ];
    }
}
