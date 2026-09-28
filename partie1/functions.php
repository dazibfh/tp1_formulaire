<?php

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function valeur($data, $key)
{
    return isset($data[$key]) ? e($data[$key]) : '';
}

function checked($data, $key, $value)
{
    if (!isset($data[$key])) {
        return '';
    }

    if (is_array($data[$key])) {
        return in_array($value, $data[$key]) ? 'checked' : '';
    }

    return $data[$key] === $value ? 'checked' : '';
}

function selected($data, $key, $value)
{
    return isset($data[$key]) && $data[$key] === $value
        ? 'selected'
        : '';
}