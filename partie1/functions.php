<?php

// Fonction de convertion des valeurs en entites HTML
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Fonction de collection de donnees depuis le formulaire
function valeur($data, $key)
{
    return isset($data[$key]) ? e($data[$key]) : '';
}

// Fonction de verification de cochage 
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

// Fonction de selection
function selected($data, $key, $value)
{
    return isset($data[$key]) && $data[$key] === $value
        ? 'selected'
        : '';
}