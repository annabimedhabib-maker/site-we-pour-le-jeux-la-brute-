-- Script de création des tables du projet La Brute
-- Import : phpMyAdmin > choisir la base (labrute en local, bdd_6_3 sur le serveur) > onglet Importer
-- Puis importer bdd/donnees_test.sql pour avoir un jeu de données de démonstration.

SET NAMES utf8mb4;

DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS combat;
DROP TABLE IF EXISTS candidature;
DROP TABLE IF EXISTS defi;
DROP TABLE IF EXISTS brute;
DROP TABLE IF EXISTS joueur;

CREATE TABLE joueur (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  login            VARCHAR(50)  NOT NULL UNIQUE,
  email            VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe     VARCHAR(255) NOT NULL,          -- hash password_hash() (bcrypt / blowfish)
  role             ENUM('joueur','admin') NOT NULL DEFAULT 'joueur',
  actif            TINYINT(1) NOT NULL DEFAULT 1,  -- 0 = compte désactivé par un admin
  date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE brute (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  joueur_id     INT NOT NULL UNIQUE,                -- R1 : 1 brute par compte
  nom           VARCHAR(50) NOT NULL UNIQUE,
  description   VARCHAR(255) NOT NULL DEFAULT '',
  avatar        VARCHAR(100) NOT NULL DEFAULT 'defaut.png',
  pv            INT NOT NULL DEFAULT 30,
  force_combat  INT NOT NULL,                       -- 5 à 8 (FORCE est un mot réservé)
  defense       INT NOT NULL,                       -- 10 - force
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (joueur_id) REFERENCES joueur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE defi (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  brute_id      INT NOT NULL,                       -- brute de l'auteur
  titre         VARCHAR(100) NOT NULL,
  description   TEXT NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  etat          ENUM('ouvert','termine') NOT NULL DEFAULT 'ouvert',
  FOREIGN KEY (brute_id) REFERENCES brute(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE candidature (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  defi_id          INT NOT NULL,
  brute_id         INT NOT NULL,                    -- brute du candidat
  message          VARCHAR(255) NOT NULL DEFAULT '',
  statut           ENUM('en_attente','acceptee','non_retenue') NOT NULL DEFAULT 'en_attente',
  date_candidature DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (defi_id, brute_id),                       -- R5 : une seule candidature par défi
  FOREIGN KEY (defi_id)  REFERENCES defi(id)  ON DELETE CASCADE,
  FOREIGN KEY (brute_id) REFERENCES brute(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE combat (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  defi_id      INT NOT NULL UNIQUE,                 -- R8 : un seul combat par défi
  brute1_id    INT NOT NULL,                        -- auteur
  brute2_id    INT NOT NULL,                        -- candidat accepté
  vainqueur_id INT NOT NULL,
  date_combat  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  compte_rendu TEXT NOT NULL,
  FOREIGN KEY (defi_id)      REFERENCES defi(id)  ON DELETE CASCADE,
  FOREIGN KEY (brute1_id)    REFERENCES brute(id) ON DELETE CASCADE,
  FOREIGN KEY (brute2_id)    REFERENCES brute(id) ON DELETE CASCADE,
  FOREIGN KEY (vainqueur_id) REFERENCES brute(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- BONUS : questions des candidats à l'auteur d'un défi
CREATE TABLE question (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  defi_id        INT NOT NULL,
  joueur_id      INT NOT NULL,                      -- celui qui pose la question
  texte_question VARCHAR(500) NOT NULL,
  texte_reponse  VARCHAR(1000) NULL,                -- NULL tant que l'auteur n'a pas répondu
  date_question  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_reponse   DATETIME NULL,
  FOREIGN KEY (defi_id)   REFERENCES defi(id)   ON DELETE CASCADE,
  FOREIGN KEY (joueur_id) REFERENCES joueur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
