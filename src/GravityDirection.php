<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

/**
 * Gravity directions for cropping and extending.
 */
enum GravityDirection: string
{
    case Center = 'ce';
    case East = 'ea';
    case North = 'no';
    case NorthEast = 'noea';
    case NorthWest = 'nowe';
    case South = 'so';
    case SouthEast = 'soea';
    case SouthWest = 'sowe';
    case West = 'we';
}
