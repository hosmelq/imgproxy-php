<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

enum ResizingAlgorithm: string
{
    case Cubic = 'cubic';
    case Lanczos2 = 'lanczos2';
    case Lanczos3 = 'lanczos3';
    case Linear = 'linear';
    case Nearest = 'nearest';
}
