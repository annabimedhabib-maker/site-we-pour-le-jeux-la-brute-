-- Jeu de données de démonstration (à importer APRÈS creation.sql)
-- Identifiants : voir docs/identifiants.txt

SET NAMES utf8mb4;

INSERT INTO joueur (id, login, email, mot_de_passe, role, actif, date_inscription) VALUES
(1, 'admin', 'admin@labrute.test', '$2y$10$IFtu4yZGCjq2Xl9q3qQyhuFzLULLbXLRV/BKRcUMtKlbJ22B63KJi', 'admin', 1, NOW() - INTERVAL 29 DAY),
(2, 'joueur_neuf', 'joueur_neuf@labrute.test', '$2y$10$zjOG1r2hDz/phx7tR/Znk.2.YUaq3AJMJvGw8V/86rMc8S02TpKMG', 'joueur', 1, NOW() - INTERVAL 28 DAY),
(3, 'joueur_simple', 'joueur_simple@labrute.test', '$2y$10$sxDzGVqhaB8.jefZNfCJg.vjpnYDyU.n3CMCum4zc9LRDeFtVCXJa', 'joueur', 1, NOW() - INTERVAL 27 DAY),
(4, 'auteur1', 'auteur1@labrute.test', '$2y$10$2HXQ1W231x.WWHOVRRrfl.eju1wRtmCVSJh5XtrtXki1oI4Nw/TdO', 'joueur', 1, NOW() - INTERVAL 26 DAY),
(5, 'candidat1', 'candidat1@labrute.test', '$2y$10$74wWVDkNwTl04/w.nPK/EOF17yO3vhZfiZp0VyhHgaLDKLCBmwXAu', 'joueur', 1, NOW() - INTERVAL 25 DAY),
(6, 'candidat2', 'candidat2@labrute.test', '$2y$10$0BZCXq6LUSwvLWgQQhP5tOt6mah4geUyvZyjGn6NAN3KRiCDSnCCi', 'joueur', 1, NOW() - INTERVAL 24 DAY),
(7, 'champion', 'champion@labrute.test', '$2y$10$VRmfcGxz5joj65Pw4zeqZeAFchGFTafcupYfKAdO/1dqGvRszL0ES', 'joueur', 1, NOW() - INTERVAL 23 DAY),
(8, 'perdant', 'perdant@labrute.test', '$2y$10$Z7NS8AoQLC2KO8IDX2yRFeQ6P5ARQ5HjwOTyR0vbDc6VGPsmK5PJ.', 'joueur', 1, NOW() - INTERVAL 22 DAY),
(9, 'banni', 'banni@labrute.test', '$2y$10$VcHtAoX5t8R4PCH8NogwzO3gcSlKcfJrJs/FD23hQUgU0yPk3OC4a', 'joueur', 0, NOW() - INTERVAL 21 DAY);

INSERT INTO brute (id, joueur_id, nom, description, avatar, pv, force_combat, defense, date_creation) VALUES
(1, 3, 'Brindille', 'Petite mais têtue, elle ne lâche jamais rien.', 'avatar1.png', 30, 5, 5, NOW() - INTERVAL 24 DAY),
(2, 4, 'Gros Marteau', 'Frappe d''abord, réfléchit ensuite. Souvent jamais.', 'avatar2.png', 30, 8, 2, NOW() - INTERVAL 23 DAY),
(3, 5, 'Petite Teigne', 'Rapide, agaçante et redoutable au corps à corps.', 'avatar3.png', 30, 6, 4, NOW() - INTERVAL 22 DAY),
(4, 6, 'Rouleau Compresseur', 'Lent, lourd, imparable.', 'avatar4.png', 30, 7, 3, NOW() - INTERVAL 21 DAY),
(5, 7, 'Le Colosse', 'Invaincu dans les bacs à sable du quartier.', 'avatar2.png', 30, 8, 2, NOW() - INTERVAL 20 DAY),
(6, 8, 'Mollusque', 'Spécialiste de l''esquive… quand il y pense.', 'avatar1.png', 30, 5, 5, NOW() - INTERVAL 19 DAY),
(7, 9, 'Tricheur Fou', 'Compte désactivé pour comportement antisportif.', 'avatar3.png', 30, 6, 4, NOW() - INTERVAL 18 DAY);

INSERT INTO defi (id, brute_id, titre, description, date_creation, etat) VALUES
(1, 2, 'Qui osera affronter le Marteau ?', 'Je cherche un adversaire digne de ce nom. Les faibles s''abstenir !', NOW() - INTERVAL 2 DAY, 'ouvert'),
(2, 5, 'Le Colosse attend son prochain repas', 'Venez tester ma défense légendaire. Une seule règle : pas de pleurs.', NOW() - INTERVAL 10 DAY, 'termine'),
(3, 2, 'Revanche au crépuscule', 'Duel amical pour l''honneur du quartier.', NOW() - INTERVAL 15 DAY, 'termine'),
(4, 4, 'Le rouleau passe, tout s''aplatit', 'Défi ouvert à tous, aucune candidature pour l''instant.', NOW() - INTERVAL 1 DAY, 'ouvert');

INSERT INTO candidature (defi_id, brute_id, message, statut, date_candidature) VALUES
(1, 3, 'Prépare-toi à mordre la poussière !', 'en_attente', NOW() - INTERVAL 1 DAY),
(1, 4, 'Je vais t''aplatir comme une crêpe.', 'en_attente', NOW() - INTERVAL 20 HOUR),
(2, 6, 'Je n''ai peur de rien… ou presque.', 'acceptee', NOW() - INTERVAL 9 DAY),
(2, 3, 'Choisis-moi, je suis plus drôle.', 'non_retenue', NOW() - INTERVAL 9 DAY),
(3, 3, 'Revanche acceptée !', 'acceptee', NOW() - INTERVAL 14 DAY);

INSERT INTO combat (defi_id, brute1_id, brute2_id, vainqueur_id, date_combat, compte_rendu) VALUES
(2, 5, 6, 5, NOW() - INTERVAL 8 DAY, 'Tirage au sort : Le Colosse a l''initiative et attaque en premier !
Tour 1 : Le Colosse attaque Mollusque et lui inflige 3 dégâts. Il reste 27 PV à Mollusque.
Tour 2 : Mollusque attaque Le Colosse et lui inflige 5 dégâts. Il reste 25 PV à Le Colosse.
Tour 3 : Le Colosse attaque Mollusque et lui inflige 4 dégâts. Il reste 23 PV à Mollusque.
Tour 4 : Mollusque attaque Le Colosse et lui inflige 4 dégâts. Il reste 21 PV à Le Colosse.
Tour 5 : Le Colosse attaque Mollusque et lui inflige 5 dégâts. Il reste 18 PV à Mollusque.
Tour 6 : Mollusque attaque Le Colosse et lui inflige 3 dégâts. Il reste 18 PV à Le Colosse.
Tour 7 : Le Colosse attaque Mollusque et lui inflige 5 dégâts. Il reste 13 PV à Mollusque.
Tour 8 : Mollusque attaque Le Colosse et lui inflige 4 dégâts. Il reste 14 PV à Le Colosse.
Tour 9 : Le Colosse attaque Mollusque et lui inflige 3 dégâts. Il reste 10 PV à Mollusque.
Tour 10 : Mollusque attaque Le Colosse et lui inflige 3 dégâts. Il reste 11 PV à Le Colosse.
Tour 11 : Le Colosse attaque Mollusque et lui inflige 4 dégâts. Il reste 6 PV à Mollusque.
Tour 12 : Mollusque attaque Le Colosse et lui inflige 5 dégâts. Il reste 6 PV à Le Colosse.
Tour 13 : Le Colosse attaque Mollusque et lui inflige 4 dégâts. Il reste 2 PV à Mollusque.
Tour 14 : Mollusque attaque Le Colosse et lui inflige 4 dégâts. Il reste 2 PV à Le Colosse.
Tour 15 : Le Colosse attaque Mollusque et lui inflige 5 dégâts. Il reste 0 PV à Mollusque.
Victoire de Le Colosse !'),
(3, 2, 3, 3, NOW() - INTERVAL 13 DAY, 'Tirage au sort : Petite Teigne a l''initiative et attaque en premier !
Tour 1 : Petite Teigne attaque Gros Marteau et lui inflige 6 dégâts. Il reste 24 PV à Gros Marteau.
Tour 2 : Gros Marteau attaque Petite Teigne et lui inflige 4 dégâts. Il reste 26 PV à Petite Teigne.
Tour 3 : Petite Teigne attaque Gros Marteau et lui inflige 5 dégâts. Il reste 19 PV à Gros Marteau.
Tour 4 : Gros Marteau attaque Petite Teigne et lui inflige 5 dégâts. Il reste 21 PV à Petite Teigne.
Tour 5 : Petite Teigne attaque Gros Marteau et lui inflige 6 dégâts. Il reste 13 PV à Gros Marteau.
Tour 6 : Gros Marteau attaque Petite Teigne et lui inflige 4 dégâts. Il reste 17 PV à Petite Teigne.
Tour 7 : Petite Teigne attaque Gros Marteau et lui inflige 6 dégâts. Il reste 7 PV à Gros Marteau.
Tour 8 : Gros Marteau attaque Petite Teigne et lui inflige 4 dégâts. Il reste 13 PV à Petite Teigne.
Tour 9 : Petite Teigne attaque Gros Marteau et lui inflige 5 dégâts. Il reste 2 PV à Gros Marteau.
Tour 10 : Gros Marteau attaque Petite Teigne et lui inflige 6 dégâts. Il reste 7 PV à Petite Teigne.
Tour 11 : Petite Teigne attaque Gros Marteau et lui inflige 6 dégâts. Il reste 0 PV à Gros Marteau.
Victoire de Petite Teigne !');

INSERT INTO question (defi_id, joueur_id, texte_question, texte_reponse, date_question, date_reponse) VALUES
(1, 5, 'Est-ce que les coups de pied sont autorisés ?', 'Tout est permis dans l''arène !', NOW() - INTERVAL 30 HOUR, NOW() - INTERVAL 26 HOUR),
(1, 6, 'Tu choisis quand ton adversaire ?', NULL, NOW() - INTERVAL 10 HOUR, NULL);
