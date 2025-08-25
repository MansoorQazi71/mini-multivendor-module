<?php

if (! function_exists('generateProductCode')) {
    function generateProductCode(): string
    {
        $year = date('Y');
        $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        return sprintf('PRD-%s-%s', $year, $random);
    }
}
