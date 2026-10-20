<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\EncryptionConfig;
use HosmelQ\Imgproxy\Exception\InvalidConfig;

it('selects cipher based on key length', function (): void {
    expect(new EncryptionConfig(str_repeat('a', 16))->cipher())->toBe('aes-128-cbc')
        ->and(new EncryptionConfig(str_repeat('b', 24))->cipher())->toBe('aes-192-cbc')
        ->and(new EncryptionConfig(str_repeat('c', 32))->cipher())->toBe('aes-256-cbc');
});

it('derives deterministic iv when generator is not provided', function (): void {
    $key = str_repeat('k', 32);
    $config = new EncryptionConfig($key);

    $iv = $config->ivFor('https://example.com/photo.jpg');
    $expected = substr(hash_hmac('sha256', 'https://example.com/photo.jpg', $key, true), 0, 16);

    expect($iv)->toBe($expected);
});

it('matches the official imgproxy AES source URL vector', function (): void {
    $key = hex2bin('1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1');
    $iv = hex2bin('a79563a2b35d86cee6451c3c800f535a');
    $config = new EncryptionConfig($key, fn (): string => $iv);

    expect($config->encrypt('http://example.com/images/curiosity.jpg'))
        ->toBe('p5VjorNdhs7mRRw8gA9TWoRlGci3l1kuzqN43UQlRaRIQ0qtBKW3qFABIsx-ZRz_cVc8iVTYbhsNsxNBL1BHaQ');
});

it('round trips the exact source for every supported AES key length and block boundary', function (int $keyLength, int $sourceLength): void {
    $key = str_repeat('k', $keyLength);
    $source = str_repeat('x', $sourceLength);
    $encrypted = new EncryptionConfig($key)->encrypt($source);
    $payload = base64_decode(strtr($encrypted, '-_', '+/'), true);
    $plain = openssl_decrypt(substr($payload, 16), 'aes-'.($keyLength * 8).'-cbc', $key, OPENSSL_RAW_DATA, substr($payload, 0, 16));

    expect($plain)->toBe($source);
})->with([[16, 15], [24, 16], [32, 17]]);

it('rejects invalid directly constructed encryption configuration', function (): void {
    new EncryptionConfig('invalid');
})->throws(InvalidConfig::class);

it('rejects custom IVs of the wrong length', function (): void {
    new EncryptionConfig(str_repeat('k', 32), fn (): string => 'short')->encrypt('source');
})->throws(InvalidArgumentException::class);
