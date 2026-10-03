<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function afficherErreur(string $message, int $code = 400): void
{
    http_response_code($code);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="fr"><meta charset="UTF-8"><title>Erreur</title>';
    echo '<body><h1>Impossible de générer le CV</h1><p>' . e($message) . '</p>';
    echo '<p><a href="formulaire_cv.php">Retour au formulaire</a></p></body></html>';
    exit;
}

function valeurListe(array $liste, int|string $index): string
{
    $valeur = $liste[$index] ?? '';

    return is_scalar($valeur) ? trim((string)$valeur) : '';
}

function verifierLongueur(string $valeur, int $maximum, string $champ): void
{
    if (mb_strlen($valeur, 'UTF-8') > $maximum) {
        throw new InvalidArgumentException(
            'Le champ « ' . $champ . ' » dépasse ' . $maximum . ' caractères.'
        );
    }
}

function dateOuNull(string $valeur, string $champ): ?string
{
    if ($valeur === '') {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $valeur);

    if (!$date || $date->format('Y-m-d') !== $valeur) {
        throw new InvalidArgumentException(
            'La date saisie dans le champ « ' . $champ . ' » est invalide.'
        );
    }

    return $valeur;
}

function executerRequete(
    mysqli $conn,
    string $sql,
    string $types,
    array $parametres
): mysqli_stmt {
    $requete = $conn->prepare($sql);
    $liaisons = [$types];

    foreach ($parametres as &$parametre) {
        $liaisons[] = &$parametre;
    }
    unset($parametre);

    $requete->bind_param(...$liaisons);
    $requete->execute();

    return $requete;
}

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

$nom = is_scalar($_POST['nom'] ?? null)
    ? trim((string)$_POST['nom'])
    : '';
$email = is_scalar($_POST['email'] ?? null)
    ? trim((string)$_POST['email'])
    : '';
$telephone = is_scalar($_POST['telephone'] ?? null)
    ? trim((string)$_POST['telephone'])
    : '';
$adresse = is_scalar($_POST['adresse'] ?? null)
    ? trim((string)$_POST['adresse'])
    : '';


if (
    $nom === '' ||
    $email === '' ||
    $telephone === '' ||
    $adresse === ''
) {
    afficherErreur('Veuillez remplir tous les champs obligatoires.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    afficherErreur('Adresse email invalide.');
}

try {
    verifierLongueur($email, 254, 'email');
    verifierLongueur($nom, 150, 'nom complet');
    verifierLongueur($telephone, 40, 'téléphone');
    verifierLongueur($adresse, 255, 'adresse');
} catch (InvalidArgumentException $exception) {
    afficherErreur($exception->getMessage());
}

$email = strtolower($email);

$listePostee = static function (string $nom): array {
    $valeur = $_POST[$nom] ?? [];

    return is_array($valeur) ? $valeur : [];
};


/* =========================================
   Formations
   ========================================= */

$formationTitres =
    $listePostee('formation_titre');

$formationEtablissements =
    $listePostee('formation_etablissement');

$formationDebuts =
    $listePostee('formation_debut');

$formationFins =
    $listePostee('formation_fin');


/* =========================================
   Stages
   ========================================= */

$stageEntreprises =
    $listePostee('stage_entreprise');

$stageLieux =
    $listePostee('stage_lieu');

$stageDebuts =
    $listePostee('stage_debut');

$stageFins =
    $listePostee('stage_fin');

$stageDescriptions =
    $listePostee('stage_description');


/* =========================================
   Compétences
   ========================================= */

$competences =
    $listePostee('competences');


/* =========================================
   Langues
   ========================================= */

$langues =
    $listePostee('langue_nom');

$niveaux =
    $listePostee('langue_niveau');


/* =========================================
   Centres d'intérêt
   ========================================= */

$interets =
    $listePostee('interets');


/* =========================================
   Valider et enregistrer la photo
   ========================================= */

$fichierPhoto = $_FILES['photo'] ?? null;

if (!is_array($fichierPhoto) || ($fichierPhoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    afficherErreur('Veuillez sélectionner une photo JPG ou PNG valide.');
}

$tmp = $fichierPhoto['tmp_name'] ?? '';
$taillePhoto = $fichierPhoto['size'] ?? 0;

if (!is_uploaded_file($tmp)) {
    afficherErreur('Le fichier photo reçu est invalide.');
}

if ($taillePhoto > 5 * 1024 * 1024) {
    afficherErreur('La photo ne doit pas dépasser 5 Mo.');
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmp);
$extensionsPhoto = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
];

if (!isset($extensionsPhoto[$mime])) {
    afficherErreur('La photo doit être une véritable image JPG ou PNG.');
}

$imageData = file_get_contents($tmp);

if ($imageData === false) {
    afficherErreur('La photo n’a pas pu être lue.');
}

$photoHtml = '
    <img
        src="data:' . $mime . ';base64,' . base64_encode($imageData) . '"
        class="photo"
    >
';

$conn = require __DIR__ . '/includes/database.php';

$dossierPhotos = __DIR__ . '/uploads';

if (!is_dir($dossierPhotos) && !mkdir($dossierPhotos, 0755, true) && !is_dir($dossierPhotos)) {
    afficherErreur('Le dossier de stockage des photos n’a pas pu être créé.', 500);
}

$nomPhoto = bin2hex(random_bytes(16)) . '.' . $extensionsPhoto[$mime];
$cheminPhoto = $dossierPhotos . DIRECTORY_SEPARATOR . $nomPhoto;
$cheminPhotoEnBase = 'uploads/' . $nomPhoto;

if (!move_uploaded_file($tmp, $cheminPhoto)) {
    afficherErreur('La photo n’a pas pu être enregistrée sur le serveur.', 500);
}

$transactionActive = false;

try {
    $conn->begin_transaction();
    $transactionActive = true;

    executerRequete(
        $conn,
        'INSERT INTO utilisateurs
            (email, nom_complet, telephone, adresse, photo_chemin)
         VALUES
            (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            nom_complet = VALUES(nom_complet),
            telephone = VALUES(telephone),
            adresse = VALUES(adresse),
            photo_chemin = VALUES(photo_chemin)',
        'sssss',
        [$email, $nom, $telephone, $adresse, $cheminPhotoEnBase]
    );

    foreach (['formations', 'stages', 'competences', 'langues', 'centres_interet'] as $table) {
        executerRequete(
            $conn,
            "DELETE FROM {$table} WHERE email_utilisateur = ?",
            's',
            [$email]
        );
    }

    $sqlFormation =
        'INSERT INTO formations
            (email_utilisateur, titre, etablissement, date_debut, date_fin, ordre)
         VALUES
            (?, ?, ?, ?, ?, ?)';

    foreach ($formationTitres as $i => $titreSaisi) {
        $titre = valeurListe($formationTitres, $i);

        if ($titre === '') {
            continue;
        }

        verifierLongueur($titre, 180, 'titre de formation');
        verifierLongueur(
            valeurListe($formationEtablissements, $i),
            180,
            'établissement'
        );

        $debut = dateOuNull(valeurListe($formationDebuts, $i), 'début de formation');
        $fin = dateOuNull(valeurListe($formationFins, $i), 'fin de formation');

        executerRequete(
            $conn,
            $sqlFormation,
            'sssssi',
            [
                $email,
                $titre,
                valeurListe($formationEtablissements, $i) ?: null,
                $debut,
                $fin,
                (int)$i,
            ]
        );
    }

    $sqlStage =
        'INSERT INTO stages
            (email_utilisateur, entreprise, lieu, date_debut, date_fin, description, ordre)
         VALUES
            (?, ?, ?, ?, ?, ?, ?)';

    foreach ($stageEntreprises as $i => $entrepriseSaisie) {
        $entreprise = valeurListe($stageEntreprises, $i);

        if ($entreprise === '') {
            continue;
        }

        verifierLongueur($entreprise, 180, 'entreprise');
        verifierLongueur(valeurListe($stageLieux, $i), 180, 'lieu du stage');

        $debut = dateOuNull(valeurListe($stageDebuts, $i), 'début de stage');
        $fin = dateOuNull(valeurListe($stageFins, $i), 'fin de stage');

        executerRequete(
            $conn,
            $sqlStage,
            'ssssssi',
            [
                $email,
                $entreprise,
                valeurListe($stageLieux, $i) ?: null,
                $debut,
                $fin,
                valeurListe($stageDescriptions, $i) ?: null,
                (int)$i,
            ]
        );
    }

    $sqlCompetence =
        'INSERT INTO competences (email_utilisateur, nom, ordre)
         VALUES (?, ?, ?)';

    foreach ($competences as $i => $competenceSaisie) {
        $competence = valeurListe($competences, $i);

        if ($competence !== '') {
            verifierLongueur($competence, 150, 'compétence');

            executerRequete(
                $conn,
                $sqlCompetence,
                'ssi',
                [$email, $competence, (int)$i]
            );
        }
    }

    $sqlLangue =
        'INSERT INTO langues (email_utilisateur, nom, niveau, ordre)
         VALUES (?, ?, ?, ?)';

    foreach ($langues as $i => $langueSaisie) {
        $langue = valeurListe($langues, $i);

        if ($langue !== '') {
            verifierLongueur($langue, 100, 'langue');
            verifierLongueur(valeurListe($niveaux, $i), 50, 'niveau de langue');

            executerRequete(
                $conn,
                $sqlLangue,
                'sssi',
                [
                    $email,
                    $langue,
                    valeurListe($niveaux, $i) ?: null,
                    (int)$i,
                ]
            );
        }
    }

    $sqlInteret =
        'INSERT INTO centres_interet (email_utilisateur, nom, ordre)
         VALUES (?, ?, ?)';

    foreach ($interets as $i => $interetSaisi) {
        $interet = valeurListe($interets, $i);

        if ($interet !== '') {
            verifierLongueur($interet, 150, 'centre d’intérêt');

            executerRequete(
                $conn,
                $sqlInteret,
                'ssi',
                [$email, $interet, (int)$i]
            );
        }
    }

    $conn->commit();
    $transactionActive = false;
} catch (InvalidArgumentException $exception) {
    if ($transactionActive) {
        $conn->rollback();
    }

    if (is_file($cheminPhoto)) {
        unlink($cheminPhoto);
    }

    afficherErreur($exception->getMessage());
} catch (mysqli_sql_exception $exception) {
    if ($transactionActive) {
        $conn->rollback();
    }

    error_log('Enregistrement du CV impossible : ' . $exception->getMessage());

    if (is_file($cheminPhoto)) {
        unlink($cheminPhoto);
    }

    afficherErreur('Les informations n’ont pas pu être enregistrées. Vérifiez la structure de la base de données.', 500);
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


foreach ($formationTitres as $i => $titreSaisi) {

    $titre = valeurListe($formationTitres, $i);


    if ($titre === '') {
        continue;
    }


    $formationExiste = true;


    $etablissement =
        valeurListe($formationEtablissements, $i);

    $debut =
        valeurListe($formationDebuts, $i);

    $fin =
        valeurListe($formationFins, $i);


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


foreach ($stageEntreprises as $i => $entrepriseSaisie) {

    $entreprise = valeurListe($stageEntreprises, $i);


    if ($entreprise === '') {
        continue;
    }


    $stageExiste = true;


    $lieu =
        valeurListe($stageLieux, $i);

    $debut =
        valeurListe($stageDebuts, $i);

    $fin =
        valeurListe($stageFins, $i);

    $description =
        valeurListe($stageDescriptions, $i);


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


foreach ($competences as $i => $competenceSaisie) {

    $competence = valeurListe($competences, $i);


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


foreach ($langues as $i => $langueSaisie) {

    $langue = valeurListe($langues, $i);


    if ($langue === '') {
        continue;
    }


    $niveau =
        valeurListe($niveaux, $i);


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


foreach ($interets as $i => $interetSaisi) {

    $interet = valeurListe($interets, $i);


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