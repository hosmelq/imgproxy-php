<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use HosmelQ\Imgproxy\Concerns\BuildsUrls;
use HosmelQ\Imgproxy\Concerns\Effects;
use HosmelQ\Imgproxy\Concerns\EncodingOptions;
use HosmelQ\Imgproxy\Concerns\FormatOptions;
use HosmelQ\Imgproxy\Concerns\MiscOptions;
use HosmelQ\Imgproxy\Concerns\ResizingOptions;
use HosmelQ\Imgproxy\Concerns\VideoOptions;
use HosmelQ\Imgproxy\Concerns\WatermarkOptions;
use InvalidArgumentException;

final readonly class UrlBuilder
{
    use BuildsUrls;
    use Effects;
    use EncodingOptions;
    use FormatOptions;
    use MiscOptions;
    use ResizingOptions;
    use VideoOptions;
    use WatermarkOptions;

    private const string OPTION_ENUM = OptionName::class;

    /**
     * Build a processing URL, with an optional final output extension.
     */
    public function build(
        string $sourceUrl,
        null|string $extension = null,
        null|SourceEncoding $encoding = null,
    ): string {
        return $this->buildUrl($sourceUrl, $extension, $encoding);
    }

    /**
     * Start another processing pipeline using its own subsequent options.
     */
    public function nextPipeline(): self
    {
        if ($this->options === [] || is_null($this->options[array_key_last($this->options)])) {
            throw new InvalidArgumentException(
                'A pipeline must contain options before starting the next pipeline.',
            );
        }

        return $this->copy(options: [...$this->options, null]);
    }
}
