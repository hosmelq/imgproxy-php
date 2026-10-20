<?php

declare(strict_types=1);

use HosmelQ\PhpCodingStandard\CodingStandard;
use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withCache(__DIR__.'/.cache/ecs')
    ->withPaths([__DIR__.'/src', __DIR__.'/tests'])
    ->withSets([CodingStandard::ECS_PHP])
    ->withSkip([
        // URL and cryptographic operations require byte-based string functions.
        MbStrFunctionsFixer::class,
    ]);
