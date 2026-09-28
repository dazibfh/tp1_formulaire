<?php

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function lignes($texte)
{
    $resultat = [];

    $liste = preg_split(
        '/\r\n|\r|\n/',
        trim($texte)
    );


    foreach ($liste as $ligne) {

        $ligne = trim($ligne);

        if ($ligne !== '') {

            $resultat[] = $ligne;
        }
    }


    return $resultat;
}


function separer($ligne)
{
    return array_map(
        'trim',
        explode('|', $ligne)
    );
}