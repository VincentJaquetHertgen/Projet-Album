-- Données de test (à exécuter APRÈS deefy.sql)
-- user1@deefy.fr / testtest12   |   user2@deefy.fr / testtest34
INSERT INTO `user` (email, passwd, role) VALUES
 ('user1@deefy.fr', '$2y$12$b8DrlBxGJx4Ij.o/QeL2HO3mxkbcLomcjjzD3XKYaSSyYTESniG1e', 1),
 ('user2@deefy.fr', '$2y$12$zg3aLJdRf2oZ292GLy8oCel2GYjZB8UJR4YFTAiZq1QcTJ.O3g6DO', 1);

INSERT INTO playlist (nom) VALUES ('Playlist de user1'), ('Playlist de user2');
INSERT INTO user2playlist (id_user, id_pl) VALUES (1, 1), (2, 2);
