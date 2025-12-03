<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\Imgproxy;

it('throws when signing pair is incomplete', function (): void {
    Imgproxy::create('https://imgproxy.test', key: 'abcd');
})->throws(InvalidConfig::class);

it('throws when signature size is invalid', function (): void {
    Imgproxy::create('https://imgproxy.test', signatureSize: 0);
})->throws(InvalidConfig::class);

it('throws when signature size exceeds max', function (): void {
    Imgproxy::create('https://imgproxy.test', signatureSize: 33);
})->throws(InvalidConfig::class, 'Signature size must be between 1 and 32 bytes.');

it('throws when key hex length is invalid', function (): void {
    Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: 'abc',
        salt: '68656c6c6f'
    );
})->throws(InvalidConfig::class, 'The key must be a non-empty even-length hex string.');

it('throws when key hex value is invalid', function (): void {
    Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: 'zz',
        salt: '68656c6c6f'
    );
})->throws(InvalidConfig::class, 'The key must be a valid hex string.');

it('throws when salt hex length is invalid', function (): void {
    Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: '736563726574',
        salt: '1'
    );
})->throws(InvalidConfig::class, 'The salt must be a non-empty even-length hex string.');

it('throws when salt hex value is invalid', function (): void {
    Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: '736563726574',
        salt: 'zz'
    );
})->throws(InvalidConfig::class, 'The salt must be a valid hex string.');
