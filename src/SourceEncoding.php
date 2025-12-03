<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

enum SourceEncoding
{
    case Base64;
    case Encrypted;
    case Plain;
}
