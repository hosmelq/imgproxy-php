<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Option;
use HosmelQ\Imgproxy\OptionName;

it('encodes options with all null arguments as name only', function (): void {
    $option = new Option(OptionName::Format, [null, null]);

    expect($option->encode(useShortName: false))->toBe('format');
});

it('trims only trailing null arguments', function (): void {
    $option = new Option(OptionName::Resize, ['fill', '', null, null]);

    expect($option->encode(useShortName: true))->toBe('rs:fill:');
});
