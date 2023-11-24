<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Ibericode\Vat\Bundle\Validator\Constraints\VatNumber;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82
    ]);
    // $rectorConfig->sets([
    //     \Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    //     \Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
    //     \Rector\Symfony\Set\SensiolabsSetList::ANNOTATIONS_TO_ATTRIBUTES,
    // ]);
    // $rectorConfig->ruleWithConfiguration(AnnotationToAttributeRector::class, [
    //     new AnnotationToAttribute(\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity::class),
    //     new AnnotationToAttribute(\Ibericode\Vat\Bundle\Validator\Constraints\VatNumber::class),
    // ]);
};
