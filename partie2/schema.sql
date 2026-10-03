CREATE DATABASE IF NOT EXISTS CV_DB
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE CV_DB;

CREATE TABLE utilisateurs (
    email VARCHAR(254) NOT NULL,
    nom_complet VARCHAR(150) NOT NULL,
    telephone VARCHAR(40) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    photo_chemin VARCHAR(255) NOT NULL,
    cree_le TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (email)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE formations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email_utilisateur VARCHAR(254) NOT NULL,
    titre VARCHAR(180) NOT NULL,
    etablissement VARCHAR(180) NULL,
    date_debut DATE NULL,
    date_fin DATE NULL,
    ordre INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_formations_utilisateur (email_utilisateur),
    CONSTRAINT fk_formations_utilisateur
        FOREIGN KEY (email_utilisateur)
        REFERENCES utilisateurs (email)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE stages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email_utilisateur VARCHAR(254) NOT NULL,
    entreprise VARCHAR(180) NOT NULL,
    lieu VARCHAR(180) NULL,
    date_debut DATE NULL,
    date_fin DATE NULL,
    description TEXT NULL,
    ordre INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_stages_utilisateur (email_utilisateur),
    CONSTRAINT fk_stages_utilisateur
        FOREIGN KEY (email_utilisateur)
        REFERENCES utilisateurs (email)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE competences (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email_utilisateur VARCHAR(254) NOT NULL,
    nom VARCHAR(150) NOT NULL,
    ordre INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_competences_utilisateur (email_utilisateur),
    CONSTRAINT fk_competences_utilisateur
        FOREIGN KEY (email_utilisateur)
        REFERENCES utilisateurs (email)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE langues (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email_utilisateur VARCHAR(254) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    niveau VARCHAR(50) NULL,
    ordre INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_langues_utilisateur (email_utilisateur),
    CONSTRAINT fk_langues_utilisateur
        FOREIGN KEY (email_utilisateur)
        REFERENCES utilisateurs (email)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE centres_interet (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email_utilisateur VARCHAR(254) NOT NULL,
    nom VARCHAR(150) NOT NULL,
    ordre INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_interets_utilisateur (email_utilisateur),
    CONSTRAINT fk_interets_utilisateur
        FOREIGN KEY (email_utilisateur)
        REFERENCES utilisateurs (email)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
