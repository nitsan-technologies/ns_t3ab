<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\ExtensionManager;

use NITSAN\NsT3Ab\Service\PackageResolverService;
use NITSAN\NsT3Ab\Utility\HelperUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GlobalConfiguration implements SingletonInterface
{
    /**
     * @var array
    */
    protected $config;

    public function __construct()
    {
        $this->config = HelperUtility::getExtensionGlobalSettings();
    }

    /**
     * @param array $params
     * @return string
     */
    public function getConfiguration(array $params, $type = ''): string
    {
        switch ($params['fieldName']) {
            case 'contentFeature':
                return $this->getContentFeatureConfiguration();
                break;
                break;

            case 'content':
                return $this->getContentConfiguration($type);
                break;

            case 'defaultOllamaModel':
                return $this->getOllamaModel();
                break;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getOllamaModel(): string
    {

        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

        if (isset($extensionConfiguration->get('ns_t3ab')['ollama_api_model']) &&
            !empty($extensionConfiguration->get('ns_t3ab')['ollama_api_model'])
        ) {
            $ollamaApiModels = GeneralUtility::trimExplode(',', $extensionConfiguration->get('ns_t3ab')['ollama_api_model']);
            return $this->createSelectBoxes(['defaultOllamaModel' => array_combine($ollamaApiModels, $ollamaApiModels)]);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    protected function getContentFeatureConfiguration(): string
    {
        $fieldsForCheckBox = [
            [
                'label' => 'Content Rewriter',
                'name' => 'enableContentRewriter',
                'checked' => true,
            ],
            [
                'label' => 'News Content Rewriter',
                'name' => 'enableNewsContentRewriter',
                'checked' => true,
            ],
            [
                'label' => 'Blog Content Rewriter',
                'name' => 'enableBlogContentRewriter',
                'checked' => true,
            ],
            [
                'label' => 'Content Elements (Create)',
                'name' => 'enableNewsContentElements',
                'checked' => true,
            ],
        ];

        return $this->createCheckboxFields($fieldsForCheckBox);
    }


    /**
     *
     * @return string
     */
    protected function getContentConfiguration($type): string
    {
        $packageResolver = GeneralUtility::makeInstance(PackageResolverService::class);
        $availablePackages = $packageResolver->getAvailablePackages();
        $availablePackagesKeyValue = [];
        if (!empty($availablePackages)) {
            foreach ($availablePackages as $key => $value) {
                $availablePackagesKeyValue[$key] = $key;
            }
        }
        if ($type == 'setup_wizard') {
            $selectData = [
                $this->sL('packageForContentBlocks') => $availablePackagesKeyValue,
            ];
            return '<fieldset class="form-section mb-3"><div class="row">'.$this->createSelectBoxes($selectData, $type).'</div></fieldset>';
        } else {
            $selectData = [
                $this->sL('packageForContentBlocks') => $availablePackagesKeyValue,
            ];
            return $this->createSelectBoxes($selectData);
        }
    }

    /**
     * @param array $selectData
     * @param string $type
     * @return string
     */
    public function createSelectBoxes(array $selectData, string $type = ''): string
    {

        $out = [];

        foreach ($selectData as $label => $options) {
            if ($type == 'setup_wizard') {
                $out[] = '<div class="col-sm-6 col-md-3">';
            }

            $out[] = '<div class="form-group form-group-dashed">';
            if (htmlspecialchars($label) != '' && htmlspecialchars($label) != '0' && $label != 'defaultOllamaModel') {
                $out[] = '<label class="form-label" for="select-box-' . $this->convertToCamelCase(htmlspecialchars($label)) . '"><span>' . htmlspecialchars($label) . '</span></label>';
            }
            $out[] = '<div class="form-control-wrap">';
            $out[] = '<select class="form-select" id="select-box-' . $this->convertToCamelCase(htmlspecialchars($label)) . '" name="' .  $this->convertToCamelCase(htmlspecialchars($label)) . '">';

            if (array_key_exists($this->convertToCamelCase(htmlspecialchars($label)), $this->config)) {
                foreach ($this->config as $settingKey => $settingValue) {

                    $shortLable = $this->convertToCamelCase(htmlspecialchars($label));
                    if ($settingKey == $shortLable) {
                        foreach ($options as $value => $text) {
                            if ($settingValue == $value) {
                                $out[] = '<option value="' . htmlspecialchars($value) . '" selected>' . htmlspecialchars($text) . '</option>';
                            } else {
                                $out[] = '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($text) . '</option>';
                            }
                        }
                    }
                }

            } else {
                foreach ($options as $value => $text) {
                    $out[] = '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($text) . '</option>';
                }
            }
            $out[] = '</select>';
            $out[] = '</div></div>';
            if ($type == 'setup_wizard') {
                $out[] = '</div>';
            }
        }

        return $this->generateHtml($out);
    }

    /**
     * @param array $fields
     * @param string $type
     * @return string
     */
    public function createNumberInputFields(array $fields, string $type = ''): string
    {
        $out = [];
        foreach ($fields as $field) {
            if ($type == 'setup_wizard') {
                $out[] = '<div class="col-sm-6 col-md-3">';
            }
            $out[] = '<div class="form-group form-group-dashed">';
            $out[] = '<label class="form-label" for="' . htmlspecialchars($field['name']) . '">';
            $out[] = '<span>' . htmlspecialchars($field['label']) . '</span>';
            $out[] = '</label>';
            $out[] = '<div class="form-control-wrap">';
            if (array_key_exists(htmlspecialchars($field['name']), $this->config)) {
                foreach ($this->config as $settingKey => $settingValue) {
                    $shortLable = htmlspecialchars($field['name']);

                    if ($settingKey == $shortLable) {
                        $out[] = '<input class="form-control" id="' . htmlspecialchars($field['name']) . '" type="number" name="' . (htmlspecialchars($field['name'])) . '" value="' . $settingValue . '">';
                    }
                }
            } else {
                $out[] = '<input class="form-control" id="' . htmlspecialchars($field['name']) . '" type="number" name="' . (htmlspecialchars($field['name'])) . '" value="' . htmlspecialchars($field['defaultValue']) . '">';
            }
            $out[] = '</div></div>';
            if ($type == 'setup_wizard') {
                $out[] = '</div>';
            }
        }

        return $this->generateHtml($out);
    }

    /**
     * @param array $fields
     * @param string $type
     * @return string
     */
    public function createCheckboxFields(array $fields, string $type = ''): string
    {
        $out = [];
        foreach ($fields as $field) {
            $isChecked = $this->getCheckedState($field['name']);
            if ($type == 'setup_wizard') {
                $out[] = '<div class="col-sm-6 col-md-3">';
            }

            $out[] = '<div class="form-group form-group-dashed">';
            $out[] = '<label class="form-label" for="' . htmlspecialchars($field['name']) . '">';
            $out[] = '<span>' . htmlspecialchars($field['label']) . '</span>';
            $out[] = '</label>';
            $out[] = '<div class="form-control-wrap">';
            if ($type == 'setup_wizard') {
                $out[] = '<div class="form-check form-switch mb-0">';
            } else {
                $out[] = '<div class="form-check">';
            }

            $out[] = '<input type="hidden" name="' . htmlspecialchars($field['name']) . '" value="0">';
            $out[] = '<input type="checkbox" name="' . (htmlspecialchars($field['name'])) . '" id="' . htmlspecialchars($field['name']) . '" class="form-check-input" value="1"' . ($isChecked ? ' checked' : '') . '>';
            $out[] = '</div></div></div>';
            if ($type == 'setup_wizard') {
                $out[] = '</div>';
            }
        }

        return $this->generateHtml($out);
    }

    /**
     * @param string $fieldName
     */
    public function getCheckedState(string $fieldName): bool
    {
        if (!isset($this->config[$fieldName])) {
            return true;
        }
        return $this->config[$fieldName] == '1';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function sL(string $key): string
    {
        return $GLOBALS['LANG']->sL('LLL:EXT:ns_t3ab/Resources/Private/Language/locallang_be.xlf:' . $key);
    }

    /**
     * @param array $content
     * @return string
     */
    protected function generateHtml(array $content): string
    {
        if (!defined('LF')) {
            define('LF', chr(10));
        }
        // Return the HTML as a single string with LF as the line separator
        return implode(LF, $content);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function convertToCamelCase(string $string): string
    {
        $output = preg_replace('/\s*\(.*?\)\s*/', '', $string);

        // Remove any extra spaces that might have been left behind
        $output = trim(preg_replace('/\s+/', ' ', $output));

        // Convert the string to lowercase
        $string = strtolower($output);

        // Replace spaces with underscores
        $string = str_replace(' ', '_', $string);

        // Convert underscores to camel case
        $string = ucwords($string, '_');

        // Remove underscores and make the first letter lowercase
        $string = lcfirst(str_replace('_', '', $string));

        return $string;
    }
}
