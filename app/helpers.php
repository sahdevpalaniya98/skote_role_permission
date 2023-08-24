<?php

if (!function_exists('base64Encode')) {
    function base64Encode($id)
    {
        if ($id) {
            $base = base64_encode($id);
            $base = str_replace('=', '__', $base);
            $base = str_replace('+', '-', $base);
            $base = str_replace('/', '--', $base);
            $base = '__' . $base;
            return $base;
        } else {
            return '';
        }
    }
}

if (!function_exists('base64Decode')) {
    function base64Decode($id)
    {
        if ($id) {
            $id = ltrim($id, '__');
            $id = str_replace('__', '=', $id);
            $id = str_replace('--', '/', $id);
            $id = str_replace('-', '+', $id);
            $id = base64_decode($id);
            return $id;
        } else {
            return '';
        }
    }
}

if (!function_exists('generateFileName')) {
    function generateFileName($extension)
    {
        return date("YmdHis") . md5(time() . rand(1111, 9999)) . '.' . $extension;
    }
}

if (!function_exists('spaceOutString')) {
    function spaceOutString($str)
    {
        return str_replace(' ', '', trim($str));
    }
}

if (!function_exists('priceFormat')) {
    function priceFormat($price)
    {
        return number_format((float)$price, 2, ".", "");
    }
}
