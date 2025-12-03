<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->security();

arch('strict types')
    ->expect('HosmelQ\Imgproxy')
    ->toUseStrictTypes();

arch('annotations')
    ->expect('HosmelQ\Imgproxy')
    ->toHaveMethodsDocumented()
    ->toHavePropertiesDocumented();
