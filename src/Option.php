<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use function array_map;
use function array_reverse;
use function array_values;

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
    public function __construct(public OptionName $name, array $arguments)
    {
        $this->arguments = $this->trimTrailingNulls($arguments);
    }

    /**
     * Encode option for URL usage.
     */
    public function encode(bool $useShortName): string
    {
        $name = $this->name->name($useShortName);

        if ($this->arguments === []) {
            return $name;
        }

        return $name.':'.implode(':', $this->arguments);
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
                    $values
                );
            }

            unset($reversed[$index]);
        }

        return [];
    }
}
