<?php

session_start();

require_once __DIR__ . '/functions.php';

// Clic sur "Effacer" : on vide les donnees memorisees en session,
// puis on revient sur cette meme page sans le parametre reset
// (pour qu'un rafraichissement F5 ne reefface pas a nouveau)
if (isset($_GET['reset'])) {

    unset($_SESSION['form_data']);

    header('Location: formulaire.php');
    exit;
}

$data = $_SESSION['form_data'] ?? [];
$experiences = is_array($data['experiences'] ?? null)
    ? $data['experiences']
    : [];

if (empty($experiences) && !empty($data)) {
    $experiences[] = [
        'type_experience' => $data['type_experience'] ?? '',
        'projet' => $data['projet'] ?? '',
        'date_debut' => $data['date_debut'] ?? '',
        'date_fin' => $data['date_fin'] ?? '',
        'type_projet' => $data['type_projet'] ?? '',
        'lieu' => $data['lieu'] ?? '',
        'description' => $data['description'] ?? '',
    ];
}

if (empty($experiences)) {
    $experiences[] = [];
}

$nextExperienceIndex = max(array_map('intval', array_keys($experiences))) + 1;

$competences = is_array($data['competences'] ?? null)
    ? $data['competences']
    : (empty($data['competences']) ? [] : explode(',', $data['competences']));
$competencesNiveaux = is_array($data['competences_niveaux'] ?? null)
    ? $data['competences_niveaux']
    : [];
$langues = is_array($data['langues'] ?? null)
    ? $data['langues']
    : (empty($data['langues']) ? [] : explode(',', $data['langues']));
$languesNiveaux = is_array($data['langues_niveaux'] ?? null)
    ? $data['langues_niveaux']
    : [];

if (empty($competences)) {
    $competences = [''];
}

if (empty($langues)) {
    $langues = [''];
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>Fiche de renseignements</title>

    <link
        rel="stylesheet"
        href="style.css?v=<?php echo filemtime(__DIR__ . '/style.css') ?>"
    >
</head>

<body>

<div class="container">

    <header class="page-header">
        <span class="eyebrow">Votre parcours, en un seul endroit</span>
        <h1>Fiche de renseignements</h1>
        <p>Renseignez vos informations personnelles, votre formation et vos expériences.</p>
    </header>

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
                value="<?php echo valeur($data, 'nom') ?>"
                required
            >


            <label>Prénom :</label>

            <input
                type="text"
                name="prenom"
                value="<?php echo valeur($data, 'prenom') ?>"
                required
            >


            <label for="age">Age :</label>

            <input
                type="number"
                name="age"
                id="age"
                min="15"
                max="100"
                value="<?php echo valeur($data, 'age') ?>"
            >


            <label for="num">Numéro de téléphone :</label>

            <input
                type="text"
                name="telephone"
                value="<?php echo valeur($data, 'telephone') ?>"
            >


            <label>Email :</label>

            <input
                type="email"
                name="email"
                value="<?php echo valeur($data, 'email') ?>"
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
                            value="<?php echo e($filiere) ?>"
                            <?php echo checked(
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
                    <?php echo selected(
                        $data,
                        'annee',
                        '1'
                    ) ?>
                >
                    1ère année
                </option>


                <option
                    value="2"
                    <?php echo selected(
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
                    <?php echo selected(
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
                            value="<?php echo e($module) ?>"
                            <?php echo checked(
                                $data,
                                'modules',
                                $module
                            ) ?>
                        >

                        <?php echo e($module) ?>

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
                value="<?php echo valeur(
                    $data,
                    'nombre_projets'
                ) ?>"
            >

        </fieldset>


        <!-- ================================= -->
        <!-- PROJET / STAGE -->
        <!-- ================================= -->

        <fieldset>

            <legend>Projets et stages</legend>

            <div id="experiences-list">
                <?php foreach ($experiences as $index => $experience): ?>
                    <?php $experience = is_array($experience) ? $experience : []; ?>
                    <?php $isProject = ($experience['type_experience'] ?? '') === 'Projet'; ?>
                    <div class="experience-entry">
                        <h3>Expérience</h3>

                        <p>Type :</p>
                        <div class="choices">
                            <label>
                                <input
                                    type="radio"
                                    name="experiences[<?php echo (int)$index ?>][type_experience]"
                                    value="Stage"
                                    <?php echo checked($experience, 'type_experience', 'Stage') ?>
                                >
                                Stage
                            </label>
                            <label>
                                <input
                                    type="radio"
                                    name="experiences[<?php echo (int)$index ?>][type_experience]"
                                    value="Projet"
                                    <?php echo checked($experience, 'type_experience', 'Projet') ?>
                                >
                                Projet
                            </label>
                        </div>

                        <label>Nom du projet / stage :</label>
                        <input
                            type="text"
                            name="experiences[<?php echo (int)$index ?>][projet]"
                            value="<?php echo valeur($experience, 'projet') ?>"
                        >

                        <label>Date de début :</label>
                        <input
                            type="date"
                            name="experiences[<?php echo (int)$index ?>][date_debut]"
                            value="<?php echo valeur($experience, 'date_debut') ?>"
                        >

                        <label>Date de fin :</label>
                        <input
                            type="date"
                            name="experiences[<?php echo (int)$index ?>][date_fin]"
                            value="<?php echo valeur($experience, 'date_fin') ?>"
                        >

                        <div class="type-projet-fields" <?php echo $isProject ? '' : 'hidden' ?>>
                            <p>Type de projet :</p>
                            <div class="choices">
                                <label>
                                    <input
                                        type="radio"
                                        name="experiences[<?php echo (int)$index ?>][type_projet]"
                                        value="Projet Personnel"
                                        <?php echo checked($experience, 'type_projet', 'Projet Personnel') ?>
                                        <?php echo $isProject ? '' : 'disabled' ?>
                                    >
                                    Projet Personnel
                                </label>
                                <label>
                                    <input
                                        type="radio"
                                        name="experiences[<?php echo (int)$index ?>][type_projet]"
                                        value="Projet académique"
                                        <?php echo checked($experience, 'type_projet', 'Projet académique') ?>
                                        <?php echo $isProject ? '' : 'disabled' ?>
                                    >
                                    Projet académique
                                </label>
                            </div>
                        </div>

                        <div class="lieu-field" <?php echo $isProject ? 'hidden' : '' ?>>
                            <label>Lieu :</label>
                            <input
                                type="text"
                                name="experiences[<?php echo (int)$index ?>][lieu]"
                                value="<?php echo valeur($experience, 'lieu') ?>"
                                <?php echo $isProject ? 'disabled' : '' ?>
                            >
                        </div>

                        <label>Description :</label>
                        <textarea name="experiences[<?php echo (int)$index ?>][description]"><?php echo valeur($experience, 'description') ?></textarea>

                        <button type="button" class="remove-experience">Supprimer cette expérience</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add-experience">Ajouter un autre projet ou stage</button>

            <template id="experience-template">
                <div class="experience-entry">
                    <h3>Expérience</h3>
                    <p>Type :</p>
                    <div class="choices">
                        <label>
                            <input type="radio" name="experiences[__INDEX__][type_experience]" value="Stage">
                            Stage
                        </label>
                        <label>
                            <input type="radio" name="experiences[__INDEX__][type_experience]" value="Projet">
                            Projet
                        </label>
                    </div>
                    <label>Nom du projet / stage :</label>
                    <input type="text" name="experiences[__INDEX__][projet]">
                    <label>Date de début :</label>
                    <input type="date" name="experiences[__INDEX__][date_debut]">
                    <label>Date de fin :</label>
                    <input type="date" name="experiences[__INDEX__][date_fin]">
                    <div class="type-projet-fields" hidden>
                        <p>Type de projet :</p>
                        <div class="choices">
                            <label>
                                <input type="radio" name="experiences[__INDEX__][type_projet]" value="Projet Personnel" disabled>
                                Projet Personnel
                            </label>
                            <label>
                                <input type="radio" name="experiences[__INDEX__][type_projet]" value="Projet académique" disabled>
                                Projet académique
                            </label>
                        </div>
                    </div>
                    <div class="lieu-field">
                        <label>Lieu :</label>
                        <input type="text" name="experiences[__INDEX__][lieu]">
                    </div>
                    <label>Description :</label>
                    <textarea name="experiences[__INDEX__][description]"></textarea>
                    <button type="button" class="remove-experience">Supprimer cette expérience</button>
                </div>
            </template>

        </fieldset>


        <!-- ================================= -->
        <!-- COMPETENCES ET LANGUES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Compétences et Langues</legend>


            <p>Compétences :</p>

            <div id="competences-list">
                <?php foreach ($competences as $index => $competence): ?>
                    <div class="skill-row">
                        <input
                            type="text"
                            name="competences[]"
                            aria-label="Compétence"
                            placeholder="Ex. PHP"
                            value="<?php echo e($competence) ?>"
                        >

                        <select
                            name="competences_niveaux[]"
                            aria-label="Niveau de maîtrise de la compétence"
                        >
                            <option value="">Choisir un niveau</option>
                            <?php foreach (['Débutant', 'Intermédiaire', 'Avancé', 'Expert'] as $niveau): ?>
                                <option
                                    value="<?php echo e($niveau) ?>"
                                    <?php echo selected(
                                        ['niveau' => $competencesNiveaux[$index] ?? ''],
                                        'niveau',
                                        $niveau
                                    ) ?>
                                >
                                    <?php echo e($niveau) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" class="remove-row">Supprimer</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="add-row" data-list="competences-list" data-name="competences">
                Ajouter une compétence
            </button>


            <p>Langues :</p>

            <div id="langues-list">
                <?php foreach ($langues as $index => $langue): ?>
                    <div class="skill-row">
                        <input
                            type="text"
                            name="langues[]"
                            aria-label="Langue"
                            placeholder="Ex. Français"
                            value="<?php echo e($langue) ?>"
                        >

                        <select
                            name="langues_niveaux[]"
                            aria-label="Niveau de maîtrise de la langue"
                        >
                            <option value="">Choisir un niveau</option>
                            <?php foreach (['Basique', 'Conversationnel', 'Courant', 'Bilingue'] as $niveau): ?>
                                <option
                                    value="<?php echo e($niveau) ?>"
                                    <?php echo selected(
                                        ['niveau' => $languesNiveaux[$index] ?? ''],
                                        'niveau',
                                        $niveau
                                    ) ?>
                                >
                                    <?php echo e($niveau) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" class="remove-row">Supprimer</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="add-row" data-list="langues-list" data-name="langues">
                Ajouter une langue
            </button>

        </fieldset>


        <!-- ================================= -->
        <!-- CENTRES D'INTERET -->
        <!-- ================================= -->

        <fieldset>

            <legend>Centres d'intérêt</legend>

            <textarea
                name="interets"
                placeholder="Sport, informatique, lecture..."
            ><?php echo valeur(
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
            ><?php echo valeur(
                $data,
                'remarques'
            ) ?></textarea>


            <label>Ajouter un fichier :</label>

            <input
                type="file"
                name="fichier"
            >

            <?php if (!empty($data['fichier'])): ?>

                <p class="info-fichier">
                    Fichier deja envoye :
                    <strong><?php echo e($data['fichier']['original']) ?></strong>
                    (il sera conserve si vous n'en choisissez pas un autre)
                </p>

            <?php endif; ?>

        </fieldset>


        <!-- ================================= -->
        <!-- BOUTONS -->
        <!-- ================================= -->

        <div class="buttons">

            <button type="submit">
                Envoyer
            </button>

            <a
                href="formulaire.php?reset=1"
                class="button-link"
            >
                Effacer
            </a>

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

const experiencesList = document.getElementById('experiences-list');
const experienceTemplate = document.getElementById('experience-template');
let nextExperienceIndex = <?php echo $nextExperienceIndex ?>;

const niveauxMaitrise = [
    'Débutant',
    'Intermédiaire',
    'Avancé',
    'Expert'
];

function ajouterLigne(listeId, nom, libelle) {

    const liste = document.getElementById(listeId);
    const ligne = document.createElement('div');
    ligne.className = 'skill-row';

    const champ = document.createElement('input');
    champ.type = 'text';
    champ.name = nom + '[]';
    champ.setAttribute('aria-label', libelle);
    champ.placeholder = nom === 'competences'
        ? 'Ex. PHP'
        : 'Ex. Français';

    const niveau = document.createElement('select');
    niveau.name = nom + '_niveaux[]';
    niveau.setAttribute('aria-label', 'Niveau de maîtrise');

    const choixInitial = document.createElement('option');
    choixInitial.value = '';
    choixInitial.textContent = 'Choisir un niveau';
    niveau.appendChild(choixInitial);

    niveauxMaitrise.forEach(function(valeur) {
        const option = document.createElement('option');
        option.value = valeur;
        option.textContent = valeur;
        niveau.appendChild(option);
    });

    const supprimer = document.createElement('button');
    supprimer.type = 'button';
    supprimer.className = 'remove-row';
    supprimer.textContent = 'Supprimer';

    ligne.append(champ, niveau, supprimer);
    liste.appendChild(ligne);
}

document.querySelectorAll('.add-row').forEach(function(bouton) {
    bouton.addEventListener('click', function() {
        const nom = bouton.dataset.name;
        const libelle = nom === 'competences' ? 'Compétence' : 'Langue';
        ajouterLigne(bouton.dataset.list, nom, libelle);
    });
});

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-row')) {
        event.target.closest('.skill-row').remove();
    }
});


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

function mettreAJourTypeExperience() {
    experiencesList.querySelectorAll('.experience-entry').forEach(function(entry) {
        const typeChoisi = entry.querySelector(
            'input[name$="[type_experience]"]:checked'
        );
        const estProjet = typeChoisi && typeChoisi.value === 'Projet';
        const typeProjetFields = entry.querySelector('.type-projet-fields');
        const lieuField = entry.querySelector('.lieu-field');

        typeProjetFields.hidden = !estProjet;
        lieuField.hidden = !!estProjet;
        typeProjetFields.querySelectorAll('input').forEach(function(input) {
            input.disabled = !estProjet;
        });
        lieuField.querySelector('input').disabled = !!estProjet;
    });
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

experiencesList.addEventListener('change', function(event) {
    if (event.target.name && event.target.name.endsWith('[type_experience]')) {
        mettreAJourTypeExperience();
    }
});

document.getElementById('add-experience').addEventListener('click', function() {
    const html = experienceTemplate.innerHTML.replace(
        /__INDEX__/g,
        String(nextExperienceIndex++)
    );
    experiencesList.insertAdjacentHTML('beforeend', html);
});

experiencesList.addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-experience')) {
        const entry = event.target.closest('.experience-entry');
        if (experiencesList.querySelectorAll('.experience-entry').length > 1) {
            entry.remove();
        }
    }
});


// Exécuter aussi au chargement de la page
// utile après avoir cliqué sur MODIFIER
mettreAJourAnnees();
mettreAJourTypeExperience();

</script>


</body>

</html>