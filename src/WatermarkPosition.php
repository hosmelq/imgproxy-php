<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

enum WatermarkPosition: string
{
    case Center = 'ce';
    case Chessboard = 'ch';
    case East = 'ea';
    case North = 'no';
    case NorthEast = 'noea';
    case NorthWest = 'nowe';
    case Repeat = 're';
    case South = 'so';
    case SouthEast = 'soea';
    case SouthWest = 'sowe';
    case West = 'we';
}
