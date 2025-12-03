<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Support;

use function Safe\preg_match;
use function sprintf;
use function strtolower;
use function ucfirst;

use InvalidArgumentException;

final readonly class Color
{
    /**
     * @param list<string> $arguments
     */
    private function __construct(private array $arguments)
    {
    }

    /**
     * Create a color from a hex string.
     */
    public static function fromHex(string $hex): self
    {
        if (preg_match('/^[0-9a-fA-F]{6}$/', $hex) !== 1) {
            throw new InvalidArgumentException('Hex color must be a 6-character hexadecimal string.');
        }

        return new self([strtolower($hex)]);
    }

    /**
     * Create a color from RGB components.
     */
    public static function fromRgb(int $red, int $green, int $blue): self
    {
        foreach (['red' => $red, 'green' => $green, 'blue' => $blue] as $channel => $value) {
            if ($value < 0 || $value > 255) {
                throw new InvalidArgumentException(sprintf('%s must be between 0 and 255.', ucfirst($channel)));
            }
        }

        return new self([
            (string) $red,
            (string) $green,
            (string) $blue,
        ]);
    }

    /**
     * Get arguments for URL encoding.
     *
     * @return list<string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
