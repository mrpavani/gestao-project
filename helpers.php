<?php

/**
 * Helpers utilities
 */

if (!function_exists('format_currency')) {
    /**
     * Formata um valor numérico no padrão brasileiro (vírgula como decimal, ponto como milhar)
     * Garante que null/valores não numéricos viram 0.00
     *
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    function format_currency($value, $decimals = 2)
    {
        if (is_numeric($value)) {
            $num = (float)$value;
        } else {
            $num = 0.0;
        }
        return number_format($num, $decimals, ',', '.');
    }
}
