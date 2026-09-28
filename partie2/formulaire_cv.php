<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>Générateur de CV</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Générateur de CV</h1>

    <form
        action="generer_cv.php"
        method="POST"
        enctype="multipart/form-data"
    >

        <!-- ================================= -->
        <!-- INFORMATIONS PERSONNELLES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Informations personnelles</legend>

            <label>Nom complet :</label>

            <input
                type="text"
                name="nom"
                required
            >


            <label>Email :</label>

            <input
                type="email"
                name="email"
                required
            >


            <label>Téléphone :</label>

            <input
                type="text"
                name="telephone"
                required
            >


            <label>Adresse :</label>

            <input
                type="text"
                name="adresse"
                required
            >


            <label>Photo :</label>

            <input
                type="file"
                name="photo"
                accept="image/jpeg,image/png"
                required
            >

        </fieldset>


        <!-- ================================= -->
        <!-- FORMATIONS -->
        <!-- ================================= -->

        <fieldset>

            <legend>Formations</legend>

            <div id="formations">

                <div class="bloc">

                    <label>Titre de la formation :</label>

                    <input
                        type="text"
                        name="formation_titre[]"
                    >


                    <label>Établissement :</label>

                    <input
                        type="text"
                        name="formation_etablissement[]"
                    >


                    <label>Date de début :</label>

                    <input
                        type="date"
                        name="formation_debut[]"
                    >


                    <label>Date de fin :</label>

                    <input
                        type="date"
                        name="formation_fin[]"
                    >

                </div>

            </div>


            <button
                type="button"
                onclick="ajouterFormation()"
            >
                + Ajouter une formation
            </button>

        </fieldset>


        <!-- ================================= -->
        <!-- STAGES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Stages</legend>

            <div id="stages">

                <div class="bloc">

                    <label>Entreprise :</label>

                    <input
                        type="text"
                        name="stage_entreprise[]"
                    >


                    <label>Lieu :</label>

                    <input
                        type="text"
                        name="stage_lieu[]"
                    >


                    <label>Date de début :</label>

                    <input
                        type="date"
                        name="stage_debut[]"
                    >


                    <label>Date de fin :</label>

                    <input
                        type="date"
                        name="stage_fin[]"
                    >


                    <label>Description :</label>

                    <textarea
                        name="stage_description[]"
                    ></textarea>

                </div>

            </div>


            <button
                type="button"
                onclick="ajouterStage()"
            >
                + Ajouter un stage
            </button>

        </fieldset>


        <!-- ================================= -->
        <!-- COMPETENCES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Compétences</legend>

            <div id="competences">

                <div class="ligne">

                    <input
                        type="text"
                        name="competences[]"
                        placeholder="Exemple : PHP"
                    >

                </div>

            </div>


            <button
                type="button"
                onclick="ajouterCompetence()"
            >
                + Ajouter une compétence
            </button>

        </fieldset>


        <!-- ================================= -->
        <!-- LANGUES -->
        <!-- ================================= -->

        <fieldset>

            <legend>Langues</legend>

            <div id="langues">

                <div class="bloc-langue">

                    <label>Langue :</label>

                    <input
                        type="text"
                        name="langue_nom[]"
                        placeholder="Exemple : Français"
                    >


                    <label>Niveau :</label>

                    <select name="langue_niveau[]">

                        <option value="">
                            Choisir
                        </option>

                        <option value="Débutant">
                            Débutant
                        </option>

                        <option value="Intermédiaire">
                            Intermédiaire
                        </option>

                        <option value="Avancé">
                            Avancé
                        </option>

                        <option value="Courant">
                            Courant
                        </option>

                        <option value="Langue maternelle">
                            Langue maternelle
                        </option>

                    </select>

                </div>

            </div>


            <button
                type="button"
                onclick="ajouterLangue()"
            >
                + Ajouter une langue
            </button>

        </fieldset>


        <!-- ================================= -->
        <!-- CENTRES D'INTERET -->
        <!-- ================================= -->

        <fieldset>

            <legend>Centres d'intérêt</legend>

            <div id="interets">

                <div class="ligne">

                    <input
                        type="text"
                        name="interets[]"
                        placeholder="Exemple : Sport"
                    >

                </div>

            </div>


            <button
                type="button"
                onclick="ajouterInteret()"
            >
                + Ajouter un centre d'intérêt
            </button>

        </fieldset>


        <div class="buttons">

            <button type="submit">
                Générer le CV
            </button>

            <button type="reset">
                Effacer
            </button>

        </div>

    </form>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Ajouter une formation
|--------------------------------------------------------------------------
*/

function ajouterFormation() {

    const zone =
        document.getElementById('formations');

    const bloc =
        document.createElement('div');

    bloc.className = 'bloc';

    bloc.innerHTML = `

        <hr>

        <label>Titre de la formation :</label>

        <input
            type="text"
            name="formation_titre[]"
        >

        <label>Établissement :</label>

        <input
            type="text"
            name="formation_etablissement[]"
        >

        <label>Date de début :</label>

        <input
            type="date"
            name="formation_debut[]"
        >

        <label>Date de fin :</label>

        <input
            type="date"
            name="formation_fin[]"
        >

        <button
            type="button"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>
    `;

    zone.appendChild(bloc);
}


/*
|--------------------------------------------------------------------------
| Ajouter un stage
|--------------------------------------------------------------------------
*/

function ajouterStage() {

    const zone =
        document.getElementById('stages');

    const bloc =
        document.createElement('div');

    bloc.className = 'bloc';

    bloc.innerHTML = `

        <hr>

        <label>Entreprise :</label>

        <input
            type="text"
            name="stage_entreprise[]"
        >

        <label>Lieu :</label>

        <input
            type="text"
            name="stage_lieu[]"
        >

        <label>Date de début :</label>

        <input
            type="date"
            name="stage_debut[]"
        >

        <label>Date de fin :</label>

        <input
            type="date"
            name="stage_fin[]"
        >

        <label>Description :</label>

        <textarea
            name="stage_description[]"
        ></textarea>

        <button
            type="button"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>
    `;

    zone.appendChild(bloc);
}


/*
|--------------------------------------------------------------------------
| Ajouter une compétence
|--------------------------------------------------------------------------
*/

function ajouterCompetence() {

    const zone =
        document.getElementById('competences');

    const ligne =
        document.createElement('div');

    ligne.className = 'ligne';

    ligne.innerHTML = `

        <input
            type="text"
            name="competences[]"
            placeholder="Nouvelle compétence"
        >

        <button
            type="button"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>
    `;

    zone.appendChild(ligne);
}


/*
|--------------------------------------------------------------------------
| Ajouter une langue
|--------------------------------------------------------------------------
*/

function ajouterLangue() {

    const zone =
        document.getElementById('langues');

    const bloc =
        document.createElement('div');

    bloc.className = 'bloc-langue';

    bloc.innerHTML = `

        <hr>

        <label>Langue :</label>

        <input
            type="text"
            name="langue_nom[]"
        >

        <label>Niveau :</label>

        <select name="langue_niveau[]">

            <option value="">
                Choisir
            </option>

            <option value="Débutant">
                Débutant
            </option>

            <option value="Intermédiaire">
                Intermédiaire
            </option>

            <option value="Avancé">
                Avancé
            </option>

            <option value="Courant">
                Courant
            </option>

            <option value="Langue maternelle">
                Langue maternelle
            </option>

        </select>

        <button
            type="button"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>
    `;

    zone.appendChild(bloc);
}


/*
|--------------------------------------------------------------------------
| Ajouter un centre d'intérêt
|--------------------------------------------------------------------------
*/

function ajouterInteret() {

    const zone =
        document.getElementById('interets');

    const ligne =
        document.createElement('div');

    ligne.className = 'ligne';

    ligne.innerHTML = `

        <input
            type="text"
            name="interets[]"
            placeholder="Nouveau centre d'intérêt"
        >

        <button
            type="button"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>
    `;

    zone.appendChild(ligne);
}

</script>

</body>

</html>