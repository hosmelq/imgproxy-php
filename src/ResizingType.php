<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

enum ResizingType: string
{
    case Auto = 'auto';
    case Fill = 'fill';
    case FillDown = 'fill-down';
    case Fit = 'fit';
    case Force = 'force';
}
