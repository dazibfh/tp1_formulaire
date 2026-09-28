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

$texte .= "\n--- Projet / Stage ---\n";

$texte .= "Projet : "
    . ($data['projet'] ?? '')
    . "\n";

$texte .= "Date début : "
    . ($data['date_debut'] ?? '')
    . "\n";

$texte .= "Date fin : "
    . ($data['date_fin'] ?? '')
    . "\n";

$texte .= "Lieu : "
    . ($data['lieu'] ?? '')
    . "\n";

$texte .= "Description : "
    . ($data['description'] ?? '')
    . "\n";

$texte .= "Compétences : "
    . ($data['competences'] ?? '')
    . "\n";

$texte .= "Langues : "
    . ($data['langues'] ?? '')
    . "\n";

$texte .= "Centres d'intérêt : "
    . ($data['interets'] ?? '')
    . "\n";

$texte .= "Remarques : "
    . ($data['remarques'] ?? '')
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

    <link rel="stylesheet" href="style.css">
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