<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\EncryptionConfig;

it('selects cipher based on key length', function (): void {
    expect((new EncryptionConfig(str_repeat('a', 16)))->cipher())->toBe('aes-128-cbc')
        ->and((new EncryptionConfig(str_repeat('b', 24)))->cipher())->toBe('aes-192-cbc')
        ->and((new EncryptionConfig(str_repeat('c', 32)))->cipher())->toBe('aes-256-cbc');
});

it('derives deterministic iv when generator is not provided', function (): void {
    $key = str_repeat('k', 32);
    $config = new EncryptionConfig($key);

    $iv = $config->ivFor('https://example.com/photo.jpg');
    $expected = substr(hash_hmac('sha256', 'https://example.com/photo.jpg', $key, true), 0, 16);

    expect($iv)->toBe($expected);
});
