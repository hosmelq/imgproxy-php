<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

/**
 * Hashsum types supported by imgproxy.
 */
enum HashsumType: string
{
    case Md5 = 'md5';
    case None = 'none';
    case Sha1 = 'sha1';
    case Sha256 = 'sha256';
    case Sha512 = 'sha512';
}
