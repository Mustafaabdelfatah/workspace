<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('getCodeByName')) {
    function getCodeByName($array, $name) {
        $indexedColors = array_column($array, 'code', 'name');
        return $indexedColors[$name] ?? null; // Return null if the name is not found
    }
}
