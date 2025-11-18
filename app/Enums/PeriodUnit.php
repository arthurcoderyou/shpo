<?php

namespace App\Enums;

enum PeriodUnit: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';

    public function label(): string
    {
        return match($this) {
            self::DAY => 'Day(s)',
            self::WEEK => 'Week(s)',
            self::MONTH => 'Month(s)',
            self::YEAR => 'Year(s)',
        };
    }
}
