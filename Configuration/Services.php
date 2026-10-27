<?php

defined('TYPO3') || die();

use NITSAN\NsT3Ab\EventListener\ModifyButtonBarEventListener;
use NITSAN\NsT3Ab\Service\Form\FormPersistenceBridgeFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\DependencyInjection\ListenerProviderPass;

return function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire(true)
        ->autoconfigure(true)
        ->private();
    $excludeClass = [
        __DIR__ . '/../Classes/Domain/Model',
        __DIR__ . '/../Classes/ViewHelpers',
    ];

    if (!class_exists('TYPO3\CMS\ContentBlocks\Builder\ContentBlockBuilder')) {
        $excludeClass[] = __DIR__. '/../Classes/Controller/ContentBlocksModuleController.php';
    }
    $containerBuilder->registerForAutoconfiguration(ModifyButtonBarEventListener::class)->addTag('event.listener', [
        'identifier' => 'ns-t3ab/backend/modify-button-bar',
    ]);
    $containerBuilder->addCompilerPass(new ListenerProviderPass('event.listener'));

    $services->load('NITSAN\\NsT3Ab\\', '../Classes/*')
        ->exclude($excludeClass);

    // Public so GeneralUtility::makeInstance() resolves DI (controller stays 0-arg for RouteDispatcher).
    $services->set(FormPersistenceBridgeFactory::class)
        ->autowire(true)
        ->public();

};
