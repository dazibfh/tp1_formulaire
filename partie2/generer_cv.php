<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


/* =========================================
   Vérifier que le formulaire a été envoyé
   ========================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulaire_cv.php');
    exit;
}


/* =========================================
   Informations personnelles
   ========================================= */

$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');


if (
    $nom === '' ||
    $email === '' ||
    $telephone === '' ||
    $adresse === ''
) {
    die('Veuillez remplir tous les champs obligatoires.');
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Adresse email invalide.');
}


/* =========================================
   Formations
   ========================================= */

$formationTitres =
    $_POST['formation_titre'] ?? [];

$formationEtablissements =
    $_POST['formation_etablissement'] ?? [];

$formationDebuts =
    $_POST['formation_debut'] ?? [];

$formationFins =
    $_POST['formation_fin'] ?? [];


/* =========================================
   Stages
   ========================================= */

$stageEntreprises =
    $_POST['stage_entreprise'] ?? [];

$stageLieux =
    $_POST['stage_lieu'] ?? [];

$stageDebuts =
    $_POST['stage_debut'] ?? [];

$stageFins =
    $_POST['stage_fin'] ?? [];

$stageDescriptions =
    $_POST['stage_description'] ?? [];


/* =========================================
   Compétences
   ========================================= */

$competences =
    $_POST['competences'] ?? [];


/* =========================================
   Langues
   ========================================= */

$langues =
    $_POST['langue_nom'] ?? [];

$niveaux =
    $_POST['langue_niveau'] ?? [];


/* =========================================
   Centres d'intérêt
   ========================================= */

$interets =
    $_POST['interets'] ?? [];


/* =========================================
   Gestion de la photo
   ========================================= */

$photoHtml = '';


if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] === UPLOAD_ERR_OK
) {

    $tmp = $_FILES['photo']['tmp_name'];

    $extension = strtolower(
        pathinfo(
            $_FILES['photo']['name'],
            PATHINFO_EXTENSION
        )
    );


    $extensionsAutorisees = [
        'jpg',
        'jpeg',
        'png'
    ];


    if (!in_array($extension, $extensionsAutorisees)) {
        die('La photo doit être au format JPG, JPEG ou PNG.');
    }


    $imageData =
        file_get_contents($tmp);


    if ($extension === 'png') {
        $mime = 'image/png';
    } else {
        $mime = 'image/jpeg';
    }


    $base64 =
        base64_encode($imageData);


    $photoHtml = '

        <img
            src="data:' . $mime . ';base64,' . $base64 . '"
            class="photo"
        >

    ';
}


/* =========================================
   Construction du CV
   ========================================= */

$html = '

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<style>

body {
    font-family: DejaVu Sans, sans-serif;
    margin: 35px;
    color: #222;
}

.header {
    border-bottom: 3px solid #333;
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.photo {
    float: right;
    width: 110px;
    height: 110px;
}

h1 {
    font-size: 28px;
    margin-bottom: 10px;
}

h2 {
    font-size: 18px;
    margin-top: 25px;
    border-bottom: 1px solid #aaa;
    padding-bottom: 5px;
}

.item {
    margin-bottom: 15px;
}

.date {
    font-size: 12px;
    color: #555;
}

ul {
    margin-top: 5px;
}

</style>

</head>

<body>


<div class="header">

' . $photoHtml . '

<h1>
' . e($nom) . '
</h1>


<p>
<strong>Email :</strong>
' . e($email) . '
</p>


<p>
<strong>Téléphone :</strong>
' . e($telephone) . '
</p>


<p>
<strong>Adresse :</strong>
' . e($adresse) . '
</p>

</div>

';


/* =========================================
   Formations
   ========================================= */

$html .= '<h2>Formations</h2>';


$formationExiste = false;


foreach ($formationTitres as $i => $titre) {

    $titre = trim($titre);


    if ($titre === '') {
        continue;
    }


    $formationExiste = true;


    $etablissement =
        $formationEtablissements[$i] ?? '';

    $debut =
        $formationDebuts[$i] ?? '';

    $fin =
        $formationFins[$i] ?? '';


    $html .= '

    <div class="item">

        <strong>
            ' . e($titre) . '
        </strong>

        <br>

        ' . e($etablissement) . '

        <br>

        <span class="date">

            ' . e($debut) . '

            -

            ' . e($fin) . '

        </span>

    </div>

    ';
}


if (!$formationExiste) {

    $html .= '<p>Aucune formation renseignée.</p>';
}


/* =========================================
   Stages
   ========================================= */

$html .= '<h2>Stages</h2>';


$stageExiste = false;


foreach ($stageEntreprises as $i => $entreprise) {

    $entreprise = trim($entreprise);


    if ($entreprise === '') {
        continue;
    }


    $stageExiste = true;


    $lieu =
        $stageLieux[$i] ?? '';

    $debut =
        $stageDebuts[$i] ?? '';

    $fin =
        $stageFins[$i] ?? '';

    $description =
        $stageDescriptions[$i] ?? '';


    $html .= '

    <div class="item">

        <strong>
            ' . e($entreprise) . '
        </strong>

        <br>

        ' . e($lieu) . '

        <br>

        <span class="date">

            ' . e($debut) . '

            -

            ' . e($fin) . '

        </span>

        <p>
            ' . nl2br(e($description)) . '
        </p>

    </div>

    ';
}


if (!$stageExiste) {

    $html .= '<p>Aucun stage renseigné.</p>';
}


/* =========================================
   Compétences
   ========================================= */

$html .= '<h2>Compétences</h2>';

$html .= '<ul>';


foreach ($competences as $competence) {

    $competence = trim($competence);


    if ($competence !== '') {

        $html .=
            '<li>' .
            e($competence) .
            '</li>';
    }
}


$html .= '</ul>';


/* =========================================
   Langues
   ========================================= */

$html .= '<h2>Langues</h2>';

$html .= '<ul>';


foreach ($langues as $i => $langue) {

    $langue = trim($langue);


    if ($langue === '') {
        continue;
    }


    $niveau =
        $niveaux[$i] ?? '';


    $html .= '<li>';

    $html .= e($langue);


    if ($niveau !== '') {

        $html .=
            ' - ' .
            e($niveau);
    }


    $html .= '</li>';
}


$html .= '</ul>';


/* =========================================
   Centres d'intérêt
   ========================================= */

$html .= "

<h2>Centres d'intérêt</h2>

<ul>

";


foreach ($interets as $interet) {

    $interet = trim($interet);


    if ($interet !== '') {

        $html .=
            '<li>' .
            e($interet) .
            '</li>';
    }
}


$html .= '

</ul>

</body>

</html>

';


/* =========================================
   Génération du PDF avec Dompdf
   ========================================= */

$options =
    new Options();


$options->set(
    'isRemoteEnabled',
    true
);


$dompdf =
    new Dompdf($options);


$dompdf->loadHtml(
    $html,
    'UTF-8'
);


$dompdf->setPaper(
    'A4',
    'portrait'
);


$dompdf->render();


/* =========================================
   Nom du PDF
   ========================================= */

$nomPdf =
    'CV_' .
    preg_replace(
        '/[^a-zA-Z0-9_-]/',
        '_',
        $nom
    ) .
    '.pdf';


/* =========================================
   Afficher le PDF
   ========================================= */

$dompdf->stream(
    $nomPdf,
    [
        'Attachment' => false
    ]
);