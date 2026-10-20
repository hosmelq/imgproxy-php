<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use function array_map;
use function array_reverse;
use function array_values;

use InvalidArgumentException;

final readonly class Option
{
    /**
     * @var list<string>
     */
    private array $arguments;

    /**
     * Create a new option.
     *
     * @param list<null|string> $arguments
     */
    public function __construct(public InfoOptionName|OptionName $name, array $arguments)
    {
        $this->arguments = $this->trimTrailingNulls($arguments);
    }

    /**
     * Reject argument data that would change the URL grammar.
     */
    public static function validateArgument(string $argument, string $separator): void
    {
        if (
            strpbrk($argument, "/?# \t\r\n") !== false
            || str_contains($argument, '%3A')
            || str_contains($argument, $separator)
        ) {
            throw new InvalidArgumentException(
                'Option arguments must not contain URL or argument separators.',
            );
        }
    }

    /**
     * Encode an option, preserving the separator for empty arguments.
     */
    public function encode(
        bool $useShortName,
        string $separator = ':',
        null|string $alias = null,
    ): string {
        if ($separator === '' || strpbrk($separator, "/?#% \t\r\n\\") !== false) {
            throw new InvalidArgumentException(
                'Argument separator must be nonempty and safe in a URL path.',
            );
        }

        $name = $alias ?? $this->name->name($useShortName);

        if (str_contains($name, $separator)) {
            throw new InvalidArgumentException(
                'Argument separator collides with the selected option name.',
            );
        }

        if (! in_array($name, $this->name->aliases(), true)) {
            throw new InvalidArgumentException('Unsupported option alias: '.$name);
        }

        foreach ($this->arguments as $argument) {
            self::validateArgument($argument, $separator);
        }

        return $name.$separator.implode($separator, $this->arguments);
    }

    /**
     * Return the serialized argument values.
     *
     * @return list<string>
     */
    public function values(): array
    {
        return $this->arguments;
    }

    /**
     * Remove trailing null arguments.
     *
     * @param list<null|string> $arguments
     *
     * @return list<string>
     */
    private function trimTrailingNulls(array $arguments): array
    {
        $reversed = array_reverse($arguments);

        foreach ($reversed as $index => $argument) {
            if (! is_null($argument)) {
                $values = array_values(array_reverse($reversed, preserve_keys: true));

                return array_map(
                    fn (null|string $argument): string => $argument ?? '',
                    $values,
                );
            }

            unset($reversed[$index]);
        }

        return [];
    }
}
