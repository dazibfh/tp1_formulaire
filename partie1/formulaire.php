<?php

session_start();

require_once __DIR__ . '/functions.php';

$data = $_SESSION['form_data'] ?? [];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>Fiche de renseignements</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Fiche de Renseignements</h1>

    <form
        action="recap.php"
        method="POST"
        enctype="multipart/form-data"
    >

        <!-- ================================= -->
        <!-- INFORMATIONS PERSONNELLES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Renseignements Personnels</legend>

            <label>Nom :</label>

            <input
                type="text"
                name="nom"
                value="<?= valeur($data, 'nom') ?>"
                required
            >


            <label>Prénom :</label>

            <input
                type="text"
                name="prenom"
                value="<?= valeur($data, 'prenom') ?>"
                required
            >


            <label>Age :</label>

            <input
                type="number"
                name="age"
                min="15"
                max="100"
                value="<?= valeur($data, 'age') ?>"
            >


            <label>Numéro de téléphone :</label>

            <input
                type="text"
                name="telephone"
                value="<?= valeur($data, 'telephone') ?>"
            >


            <label>Email :</label>

            <input
                type="email"
                name="email"
                value="<?= valeur($data, 'email') ?>"
                required
            >

        </fieldset>


        <!-- ================================= -->
        <!-- INFORMATIONS ACADEMIQUES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Renseignements Académiques</legend>

            <p>Vous êtes en :</p>


            <?php

            $filieres = [
                '2AP',
                'GSTR',
                'GI',
                'SCM',
                'GC',
                'MS'
            ];

            ?>


            <div class="choices">

                <?php foreach ($filieres as $filiere): ?>

                    <label>

                        <input
                            type="radio"
                            name="filiere"
                            value="<?= e($filiere) ?>"
                            <?= checked(
                                $data,
                                'filiere',
                                $filiere
                            ) ?>
                        >

                        <?= e($filiere) ?>

                    </label>

                <?php endforeach; ?>

            </div>


            <!-- ============================= -->
            <!-- ANNEE -->
            <!-- ============================= -->

            <label>Année :</label>

            <select
                name="annee"
                id="annee"
            >

                <option value="">
                    Choisir une année
                </option>


                <option
                    value="1"
                    <?= selected(
                        $data,
                        'annee',
                        '1'
                    ) ?>
                >
                    1ère année
                </option>


                <option
                    value="2"
                    <?= selected(
                        $data,
                        'annee',
                        '2'
                    ) ?>
                >
                    2ème année
                </option>


                <option
                    value="3"
                    id="annee3"
                    <?= selected(
                        $data,
                        'annee',
                        '3'
                    ) ?>
                >
                    3ème année
                </option>

            </select>


            <!-- ============================= -->
            <!-- MODULES -->
            <!-- ============================= -->

            <p>Modules suivis cette année :</p>


            <?php

            $modules = [
                'Pro Av',
                'Compilation',
                'Réseaux Avancés',
                'Web Avancé',
                'POO',
                'BD'
            ];

            ?>


            <div class="choices">

                <?php foreach ($modules as $module): ?>

                    <label>

                        <input
                            type="checkbox"
                            name="modules[]"
                            value="<?= e($module) ?>"
                            <?= checked(
                                $data,
                                'modules',
                                $module
                            ) ?>
                        >

                        <?= e($module) ?>

                    </label>

                <?php endforeach; ?>

            </div>


            <label>
                Nombre de projets réalisés cette année :
            </label>

            <input
                type="number"
                name="nombre_projets"
                min="0"
                value="<?= valeur(
                    $data,
                    'nombre_projets'
                ) ?>"
            >

        </fieldset>


        <!-- ================================= -->
        <!-- PROJET / STAGE -->
        <!-- ================================= -->

        <fieldset>

            <legend>Projet ou Stage</legend>


            <label>Nom du projet / stage :</label>

            <input
                type="text"
                name="projet"
                value="<?= valeur(
                    $data,
                    'projet'
                ) ?>"
            >


            <label>Date de début :</label>

            <input
                type="date"
                name="date_debut"
                value="<?= valeur(
                    $data,
                    'date_debut'
                ) ?>"
            >


            <label>Date de fin :</label>

            <input
                type="date"
                name="date_fin"
                value="<?= valeur(
                    $data,
                    'date_fin'
                ) ?>"
            >


            <label>Lieu :</label>

            <input
                type="text"
                name="lieu"
                value="<?= valeur(
                    $data,
                    'lieu'
                ) ?>"
            >


            <label>Description :</label>

            <textarea
                name="description"
            ><?= valeur(
                $data,
                'description'
            ) ?></textarea>

        </fieldset>


        <!-- ================================= -->
        <!-- COMPETENCES ET LANGUES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Compétences et Langues</legend>


            <label>Compétences :</label>

            <textarea
                name="competences"
                placeholder="PHP, HTML, CSS, Java..."
            ><?= valeur(
                $data,
                'competences'
            ) ?></textarea>


            <label>Langues :</label>

            <textarea
                name="langues"
                placeholder="Arabe, Français, Anglais..."
            ><?= valeur(
                $data,
                'langues'
            ) ?></textarea>

        </fieldset>


        <!-- ================================= -->
        <!-- CENTRES D'INTERET -->
        <!-- ================================= -->

        <fieldset>

            <legend>Centres d'intérêt</legend>

            <textarea
                name="interets"
                placeholder="Sport, informatique, lecture..."
            ><?= valeur(
                $data,
                'interets'
            ) ?></textarea>

        </fieldset>


        <!-- ================================= -->
        <!-- REMARQUES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Vos remarques</legend>

            <textarea
                name="remarques"
            ><?= valeur(
                $data,
                'remarques'
            ) ?></textarea>


            <label>Ajouter un fichier :</label>

            <input
                type="file"
                name="fichier"
            >

        </fieldset>


        <!-- ================================= -->
        <!-- BOUTONS -->
        <!-- ================================= -->

        <div class="buttons">

            <button type="submit">
                Envoyer
            </button>

            <button type="reset">
                Effacer
            </button>

        </div>

    </form>

</div>


<!-- ================================= -->
<!-- JAVASCRIPT : GESTION DE L'ANNEE -->
<!-- ================================= -->

<script>

const filieres =
    document.querySelectorAll(
        'input[name="filiere"]'
    );

const selectAnnee =
    document.getElementById('annee');

const troisiemeAnnee =
    document.getElementById('annee3');


function mettreAJourAnnees() {

    const filiereChoisie =
        document.querySelector(
            'input[name="filiere"]:checked'
        );


    // Si aucune filière n'est choisie
    if (!filiereChoisie) {

        troisiemeAnnee.hidden = false;
        troisiemeAnnee.disabled = false;

        return;
    }


    // Si l'étudiant choisit 2AP
    if (filiereChoisie.value === '2AP') {

        // Cacher la 3ème année
        troisiemeAnnee.hidden = true;

        // Empêcher sa sélection
        troisiemeAnnee.disabled = true;


        // Si la 3ème année était déjà sélectionnée,
        // remettre le select à vide
        if (selectAnnee.value === '3') {

            selectAnnee.value = '';
        }

    } else {

        // Pour toutes les autres filières
        // afficher la 3ème année
        troisiemeAnnee.hidden = false;

        troisiemeAnnee.disabled = false;
    }
}


// Quand on change la filière
filieres.forEach(
    function(filiere) {

        filiere.addEventListener(
            'change',
            mettreAJourAnnees
        );

    }
);


// Exécuter aussi au chargement de la page
// utile après avoir cliqué sur MODIFIER
mettreAJourAnnees();

</script>


</body>

</html>