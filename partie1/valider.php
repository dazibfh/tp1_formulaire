<?php

session_start();

if (empty($_SESSION['form_data'])) {
    header('Location: formulaire.php');
    exit;
}

$data = $_SESSION['form_data'];

$fichier = __DIR__ . '/etudiants.txt';

$modules = !empty($data['modules'])
    ? implode(', ', $data['modules'])
    : 'Aucun';

$texte = "";

$texte .= "========================================\n";
$texte .= "FICHE ETUDIANT\n";
$texte .= "========================================\n";

$texte .= "Nom : " . ($data['nom'] ?? '') . "\n";
$texte .= "Prénom : " . ($data['prenom'] ?? '') . "\n";
$texte .= "Age : " . ($data['age'] ?? '') . "\n";
$texte .= "Téléphone : " . ($data['telephone'] ?? '') . "\n";
$texte .= "Email : " . ($data['email'] ?? '') . "\n";

$texte .= "\n--- Informations académiques ---\n";

$texte .= "Filière : " . ($data['filiere'] ?? '') . "\n";
$texte .= "Année : " . ($data['annee'] ?? '') . "\n";
$texte .= "Modules : " . $modules . "\n";

$texte .= "Nombre de projets : "
    . ($data['nombre_projets'] ?? '')
    . "\n";

$experiences = $data['experiences'] ?? [];
if (empty($experiences) && !empty($data)) {
    $experiences[] = $data;
}

$texte .= "\n--- Projets et stages ---\n";
foreach ($experiences as $index => $experience) {
    if (!is_array($experience)) {
        continue;
    }

    $texte .= "\nExpérience " . ($index + 1) . "\n";
    $texte .= "Type : " . ($experience['type_experience'] ?? '') . "\n";
    $texte .= "Projet / stage : " . ($experience['projet'] ?? '') . "\n";
    $texte .= "Date début : " . ($experience['date_debut'] ?? '') . "\n";
    $texte .= "Date fin : " . ($experience['date_fin'] ?? '') . "\n";

    if (($experience['type_experience'] ?? '') === 'Projet') {
        $texte .= "Type de projet : " . ($experience['type_projet'] ?? '') . "\n";
    } else {
        $texte .= "Lieu : " . ($experience['lieu'] ?? '') . "\n";
    }

    $texte .= "Description : " . ($experience['description'] ?? '') . "\n";
}

$texte .= "Compétences :\n";
if (is_array($data['competences'] ?? null)) {
    foreach ($data['competences'] as $index => $competence) {
        if (trim((string)$competence) !== '') {
            $texte .= "- " . $competence;
            if (!empty($data['competences_niveaux'][$index])) {
                $texte .= " : " . $data['competences_niveaux'][$index];
            }
            $texte .= "\n";
        }
    }
} else {
    $texte .= ($data['competences'] ?? '') . "\n";
}

$texte .= "Langues :\n";
if (is_array($data['langues'] ?? null)) {
    foreach ($data['langues'] as $index => $langue) {
        if (trim((string)$langue) !== '') {
            $texte .= "- " . $langue;
            if (!empty($data['langues_niveaux'][$index])) {
                $texte .= " : " . $data['langues_niveaux'][$index];
            }
            $texte .= "\n";
        }
    }
} else {
    $texte .= ($data['langues'] ?? '') . "\n";
}

$texte .= "Centres d'intérêt : "
    . ($data['interets'] ?? '')
    . "\n";

$texte .= "Remarques : "
    . ($data['remarques'] ?? '')
    . "\n";

$texte .= "Fichier : "
    . ($data['fichier']['original'] ?? 'Aucun')
    . "\n";

$texte .= "\n\n";

if (
    file_put_contents(
        $fichier,
        $texte,
        FILE_APPEND | LOCK_EX
    ) === false
) {
    die("Erreur lors de l'enregistrement.");
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Validation</title>

    <link
        rel="stylesheet"
        href="style.css?v=<?php echo filemtime(__DIR__ . '/style.css') ?>"
    >
</head>

<body>

<div class="container">

    <h1>Enregistrement réussi</h1>

    <p>
        Les informations ont été enregistrées dans
        <strong>etudiants.txt</strong>.
    </p>

    <a href="formulaire.php">
        Retour au formulaire
    </a>

</div>

</body>

</html>