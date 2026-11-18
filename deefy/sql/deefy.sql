-- Script de création de la base Deefy (MySQL / MariaDB)
CREATE TABLE IF NOT EXISTS `user` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(256) NOT NULL UNIQUE,
  passwd VARCHAR(256) NOT NULL,
  role INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS playlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS track (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(100) NOT NULL,
  genre VARCHAR(30),
  duree INT NOT NULL DEFAULT 0,
  filename VARCHAR(256) NOT NULL,
  type VARCHAR(30) NOT NULL,            -- 'A' = album, 'P' = podcast
  artiste_album VARCHAR(30),
  titre_album VARCHAR(30),
  annee_album INT,
  numero_album INT,
  auteur_podcast VARCHAR(100),
  date_posdcast DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS playlist2track (
  id_pl INT NOT NULL,
  id_track INT NOT NULL,
  no_piste_dans_liste INT NOT NULL,
  PRIMARY KEY (id_pl, id_track),
  FOREIGN KEY (id_pl) REFERENCES playlist(id) ON DELETE CASCADE,
  FOREIGN KEY (id_track) REFERENCES track(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user2playlist (
  id_user INT NOT NULL,
  id_pl INT NOT NULL,
  PRIMARY KEY (id_user, id_pl),
  FOREIGN KEY (id_user) REFERENCES `user`(id) ON DELETE CASCADE,
  FOREIGN KEY (id_pl) REFERENCES playlist(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
