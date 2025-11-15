<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    )
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        instanceOf: true,
        typeDeclarations: true,
        earlyReturn: true,
        privatization: true,
        symfonyConfigs: true,
        doctrineCodeQuality: true,
        phpunitCodeQuality: true,
        symfonyCodeQuality: true,
    )
    ->withAttributesSets(symfony: true, doctrine: true, phpunit: true)
    ->withComposerBased(symfony: true, doctrine: true, phpunit: true, twig: true)
    ->withSymfonyContainerPhp(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.php')
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');
