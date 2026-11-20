<?php

declare(strict_types=1);

use HosmelQ\PhpCodingStandard\CodingStandard;
use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withCache('./.cache/ecs')
    ->withPaths([__DIR__.'/src', __DIR__.'/tests'])
    ->withRootFiles()
    ->withSets([CodingStandard::ECS_PHP])
    ->withSkip([
        // Keys, IVs, digests and encoded sources are byte strings.
        MbStrFunctionsFixer::class,
    ]);
