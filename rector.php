<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );
    $rectorConfig->sets(
        [
            LevelSetList::UP_TO_PHP_82,
            PHPUnitLevelSetList::UP_TO_PHPUNIT_100,
            SymfonyLevelSetList::UP_TO_SYMFONY_64,
            SymfonySetList::SYMFONY_CODE_QUALITY,
            SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        ]
    );
};
