<?php

session_start();

require_once __DIR__ . '/functions.php';


/*
|--------------------------------------------------------------------------
| Récupération des données du formulaire
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // On garde une trace de l'ancien fichier AVANT d'ecraser
    // form_data avec $_POST, car $_POST ne contient jamais les
    // fichiers (ils arrivent dans $_FILES, pas dans $_POST)
    $ancienFichier = $_SESSION['form_data']['fichier'] ?? null;

    // Sauvegarder toutes les informations dans la session
    $_SESSION['form_data'] = $_POST;

    // Par defaut on remet l'ancien fichier : s'il n'y a pas de
    // nouvel envoi plus bas, l'information n'est pas perdue
    if ($ancienFichier) {
        $_SESSION['form_data']['fichier'] = $ancienFichier;
    }


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

            // On garde le nom original (pour l'affichage) et le
            // nom stocke sur le disque (pour le lien / l'apercu)
            $_SESSION['form_data']['fichier'] = [
                'original' => $nomOriginal,
                'stocke'   => $nomFichier,
            ];
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
        href="style.css?v=<?php echo filemtime(__DIR__ . '/style.css') ?>"
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

        <?php echo valeur($data, 'nom') ?>
    </p>


    <p>
        <strong>Prénom :</strong>

        <?php echo valeur($data, 'prenom') ?>
    </p>


    <p>
        <strong>Age :</strong>

        <?php echo valeur($data, 'age') ?>
    </p>


    <p>
        <strong>Téléphone :</strong>

        <?php echo valeur($data, 'telephone') ?>
    </p>


    <p>
        <strong>Email :</strong>

        <?php echo valeur($data, 'email') ?>
    </p>



    <!-- ============================== -->
    <!-- INFORMATIONS ACADEMIQUES -->
    <!-- ============================== -->

    <h2>Informations académiques</h2>


    <p>
        <strong>Filière :</strong>

        <?php echo valeur($data, 'filiere') ?>
    </p>


    <p>
        <strong>Année :</strong>

        <?php echo valeur($data, 'annee') ?>
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

        <?php echo valeur($data, 'nombre_projets') ?>
    </p>



    <!-- ============================== -->
    <!-- PROJET / STAGE -->
    <!-- ============================== -->

    <h2>Projets et stages</h2>

    <?php
    $experiences = $data['experiences'] ?? [];
    if (empty($experiences) && !empty($data)) {
        $experiences[] = $data;
    }
    ?>

    <?php foreach ($experiences as $index => $experience): ?>
        <?php $experience = is_array($experience) ? $experience : []; ?>
        <h3>Expérience <?php echo (int)$index + 1 ?></h3>
        <p><strong>Type :</strong> <?php echo valeur($experience, 'type_experience') ?></p>
        <p><strong>Nom du projet / stage :</strong> <?php echo valeur($experience, 'projet') ?></p>
        <p><strong>Date de début :</strong> <?php echo valeur($experience, 'date_debut') ?></p>
        <p><strong>Date de fin :</strong> <?php echo valeur($experience, 'date_fin') ?></p>

        <?php if (($experience['type_experience'] ?? '') === 'Projet'): ?>
            <p><strong>Type de projet :</strong> <?php echo valeur($experience, 'type_projet') ?></p>
        <?php else: ?>
            <p><strong>Lieu :</strong> <?php echo valeur($experience, 'lieu') ?></p>
        <?php endif; ?>

        <p>
            <strong>Description :</strong>
            <?php echo nl2br(valeur($experience, 'description')) ?>
        </p>
    <?php endforeach; ?>



    <!-- ============================== -->
    <!-- COMPETENCES -->
    <!-- ============================== -->

    <h2>Compétences</h2>

    <?php if (!empty($data['competences']) && is_array($data['competences'])): ?>
        <ul>
            <?php foreach ($data['competences'] as $index => $competence): ?>
                <?php if (trim((string)$competence) !== ''): ?>
                    <li>
                        <?php echo e($competence) ?>
                        <?php if (!empty($data['competences_niveaux'][$index])): ?>
                            — <?php echo e($data['competences_niveaux'][$index]) ?>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php elseif (!empty($data['competences'])): ?>
        <p><?php echo nl2br(valeur($data, 'competences')) ?></p>
    <?php endif; ?>



    <!-- ============================== -->
    <!-- LANGUES -->
    <!-- ============================== -->

    <h2>Langues</h2>

    <?php if (!empty($data['langues']) && is_array($data['langues'])): ?>
        <ul>
            <?php foreach ($data['langues'] as $index => $langue): ?>
                <?php if (trim((string)$langue) !== ''): ?>
                    <li>
                        <?php echo e($langue) ?>
                        <?php if (!empty($data['langues_niveaux'][$index])): ?>
                            — <?php echo e($data['langues_niveaux'][$index]) ?>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php elseif (!empty($data['langues'])): ?>
        <p><?php echo nl2br(valeur($data, 'langues')) ?></p>
    <?php endif; ?>



    <!-- ============================== -->
    <!-- CENTRES D'INTERET -->
    <!-- ============================== -->

    <h2>Centres d'intérêt</h2>


    <p>

        <?php echo nl2br(
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

        <?php echo nl2br(
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
            <?php echo e($data['fichier']['original']) ?>
        </p>

        <?php

        $cheminFichier =
            'uploads/' . $data['fichier']['stocke'];

        ?>
       

            <p>
                <a
                    href="<?php echo e($cheminFichier) ?>"
                    target="_blank"
                    class="button-link"
                >
                    Voir le fichier
                </a>
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