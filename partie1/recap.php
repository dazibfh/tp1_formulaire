<?php

session_start();

require_once __DIR__ . '/functions.php';


/*
|--------------------------------------------------------------------------
| Récupération des données du formulaire
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sauvegarder toutes les informations dans la session
    $_SESSION['form_data'] = $_POST;


    /*
    |--------------------------------------------------------------------------
    | Gestion du fichier envoyé
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES['fichier']) &&
        $_FILES['fichier']['error'] === UPLOAD_ERR_OK
    ) {

        // Dossier uploads situé dans partie1/
        $uploadDir = __DIR__ . '/uploads/';


        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }


        // Nom original du fichier
        $nomOriginal = basename(
            $_FILES['fichier']['name']
        );


        // Nettoyer le nom
        $nomOriginal = preg_replace(
            '/[^a-zA-Z0-9._-]/',
            '_',
            $nomOriginal
        );


        // Ajouter un identifiant pour éviter d'écraser
        // un ancien fichier ayant le même nom
        $nomFichier =
            uniqid() .
            '_' .
            $nomOriginal;


        $destination =
            $uploadDir .
            $nomFichier;


        // Déplacer le fichier
        if (
            move_uploaded_file(
                $_FILES['fichier']['tmp_name'],
                $destination
            )
        ) {

            $_SESSION['form_data']['fichier'] =
                $nomFichier;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Données à afficher
|--------------------------------------------------------------------------
*/

$data = $_SESSION['form_data'] ?? [];


/*
|--------------------------------------------------------------------------
| Si aucune donnée, retour au formulaire
|--------------------------------------------------------------------------
*/

if (empty($data)) {

    header('Location: formulaire.php');
    exit;
}

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Récapitulatif</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>

<div class="container">

    <h1>Récapitulatif des informations</h1>


    <!-- ============================== -->
    <!-- INFORMATIONS PERSONNELLES -->
    <!-- ============================== -->

    <h2>Informations personnelles</h2>


    <p>
        <strong>Nom :</strong>

        <?= valeur($data, 'nom') ?>
    </p>


    <p>
        <strong>Prénom :</strong>

        <?= valeur($data, 'prenom') ?>
    </p>


    <p>
        <strong>Age :</strong>

        <?= valeur($data, 'age') ?>
    </p>


    <p>
        <strong>Téléphone :</strong>

        <?= valeur($data, 'telephone') ?>
    </p>


    <p>
        <strong>Email :</strong>

        <?= valeur($data, 'email') ?>
    </p>



    <!-- ============================== -->
    <!-- INFORMATIONS ACADEMIQUES -->
    <!-- ============================== -->

    <h2>Informations académiques</h2>


    <p>
        <strong>Filière :</strong>

        <?= valeur($data, 'filiere') ?>
    </p>


    <p>
        <strong>Année :</strong>

        <?= valeur($data, 'annee') ?>
    </p>


    <p>

        <strong>Modules suivis :</strong>

        <?php

        if (!empty($data['modules'])) {

            echo e(
                implode(
                    ', ',
                    $data['modules']
                )
            );

        } else {

            echo 'Aucun module sélectionné';
        }

        ?>

    </p>


    <p>
        <strong>Nombre de projets :</strong>

        <?= valeur($data, 'nombre_projets') ?>
    </p>



    <!-- ============================== -->
    <!-- PROJET / STAGE -->
    <!-- ============================== -->

    <h2>Projet / Stage</h2>


    <p>
        <strong>Nom du projet / stage :</strong>

        <?= valeur($data, 'projet') ?>
    </p>


    <p>
        <strong>Date de début :</strong>

        <?= valeur($data, 'date_debut') ?>
    </p>


    <p>
        <strong>Date de fin :</strong>

        <?= valeur($data, 'date_fin') ?>
    </p>


    <p>
        <strong>Lieu :</strong>

        <?= valeur($data, 'lieu') ?>
    </p>


    <p>
        <strong>Description :</strong>

        <?= nl2br(
            valeur(
                $data,
                'description'
            )
        ) ?>
    </p>



    <!-- ============================== -->
    <!-- COMPETENCES -->
    <!-- ============================== -->

    <h2>Compétences</h2>


    <p>

        <?= nl2br(
            valeur(
                $data,
                'competences'
            )
        ) ?>

    </p>



    <!-- ============================== -->
    <!-- LANGUES -->
    <!-- ============================== -->

    <h2>Langues</h2>


    <p>

        <?= nl2br(
            valeur(
                $data,
                'langues'
            )
        ) ?>

    </p>



    <!-- ============================== -->
    <!-- CENTRES D'INTERET -->
    <!-- ============================== -->

    <h2>Centres d'intérêt</h2>


    <p>

        <?= nl2br(
            valeur(
                $data,
                'interets'
            )
        ) ?>

    </p>



    <!-- ============================== -->
    <!-- REMARQUES -->
    <!-- ============================== -->

    <h2>Remarques</h2>


    <p>

        <?= nl2br(
            valeur(
                $data,
                'remarques'
            )
        ) ?>

    </p>



    <!-- ============================== -->
    <!-- FICHIER -->
    <!-- ============================== -->

    <?php if (!empty($data['fichier'])): ?>

        <h2>Fichier envoyé</h2>

        <p>

            <strong>Nom du fichier :</strong>

            <?= e($data['fichier']) ?>

        </p>

    <?php endif; ?>



    <!-- ============================== -->
    <!-- BOUTONS -->
    <!-- ============================== -->

    <div class="buttons">


        <form
            action="valider.php"
            method="POST"
        >

            <button type="submit">
                VALIDER
            </button>

        </form>


        <form
            action="modifier.php"
            method="GET"
        >

            <button type="submit">
                MODIFIER
            </button>

        </form>


    </div>

</div>

</body>

</html>