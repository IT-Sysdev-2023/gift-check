<?php

namespace App\Helpers;

use NumberFormatter;
use Illuminate\Support\Number;

// use PhpOffice\PhpSpreadsheet\Style\NumberFormat\NumberFormatter;

class NumberHelper
{
    public static function float(string $number): float|int
    {
        return (float) str_replace(['₱', ','], '', $number);
    }

    public static function format(mixed $number): string
    {
        return number_format($number, 2);
    }
    public static function toLocaleString(mixed $number): string
    {
        return number_format($number);
    }

    public static function formatterFloat($number)
    {
        return (float) number_format($number, 2);
    }
    public static function currency(float $amount, $locale = 'en_PH', string $currency = 'PHP')
    {
        // $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        // return $numberFormatter->formatCurrency($amount, $currency);
        return Number::currency($amount, in: $currency, locale: $locale);
    }

    public static function percentage($current, $total)
    {
        if ($total == 0) {
            return 0;
        }
        $percent = ($current / $total) * 100;
        return (float) self::format($percent);
    }

    public static function leadingZero(int $num, string $format = "%04d")
    {
        return sprintf($format, $num);
    }

}
