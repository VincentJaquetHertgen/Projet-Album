<?php
declare(strict_types=1);

namespace iutnc\deefy\Repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyRepository {

    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file): void {
        $conf = @parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur de lecture du fichier de configuration");
        }
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8mb4",
            'user' => $conf['username'],
            'pass' => $conf['password'],
        ];
    }

    /* ---------- Utilisateurs ---------- */

    public function findUserByEmail(string $email): ?array {
        $st = $this->pdo->prepare('SELECT id, email, passwd, role FROM `user` WHERE email = :e');
        $st->execute(['e' => $email]);
        return $st->fetch() ?: null;
    }

    public function createUser(string $email, string $hash, int $role): int {
        $st = $this->pdo->prepare('INSERT INTO `user` (email, passwd, role) VALUES (:e, :p, :r)');
        $st->execute(['e' => $email, 'p' => $hash, 'r' => $role]);
        return (int) $this->pdo->lastInsertId();
    }

    /* ---------- Playlists ---------- */

    /** Playlists (sans pistes) appartenant à un utilisateur. */
    public function findPlaylistsByUser(int $idUser): array {
        $st = $this->pdo->prepare(
            'SELECT p.id, p.nom FROM playlist p
             INNER JOIN user2playlist u ON u.id_pl = p.id
             WHERE u.id_user = :u ORDER BY p.id');
        $st->execute(['u' => $idUser]);
        $res = [];
        foreach ($st->fetchAll() as $row) {
            $pl = new Playlist($row['nom']);
            $pl->setId((int) $row['id']);
            $res[] = $pl;
        }
        return $res;
    }

    public function isPlaylistOwner(int $idUser, int $idPl): bool {
        $st = $this->pdo->prepare('SELECT 1 FROM user2playlist WHERE id_user = :u AND id_pl = :p');
        $st->execute(['u' => $idUser, 'p' => $idPl]);
        return (bool) $st->fetchColumn();
    }

    /** Charge une playlist avec ses pistes (ordre de la liste). */
    public function findPlaylistById(int $id): ?Playlist {
        $st = $this->pdo->prepare('SELECT id, nom FROM playlist WHERE id = :id');
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        if (!$row) {
            return null;
        }
        $pl = new Playlist($row['nom']);
        $pl->setId((int) $row['id']);

        $st = $this->pdo->prepare(
            'SELECT t.* FROM track t
             INNER JOIN playlist2track pt ON pt.id_track = t.id
             WHERE pt.id_pl = :id ORDER BY pt.no_piste_dans_liste');
        $st->execute(['id' => $id]);
        foreach ($st->fetchAll() as $t) {
            $pl->ajouterPiste($this->buildTrack($t));
        }
        return $pl;
    }

    private function buildTrack(array $t): AudioTrack {
        if ($t['type'] === 'A') {
            $track = new AlbumTrack($t['titre'], $t['filename'], (string) $t['titre_album'], (int) $t['numero_album']);
            $track->setArtiste((string) $t['artiste_album']);
            $track->setAnnee((int) $t['annee_album']);
        } else {
            $track = new PodcastTrack($t['titre'], $t['filename']);
            $track->setAuteur((string) $t['auteur_podcast']);
            $track->setDate((string) $t['date_posdcast']);
        }
        $track->setId((int) $t['id']);
        $track->setGenre((string) $t['genre']);
        $track->setDuree((int) $t['duree']);
        return $track;
    }

    /** Crée la playlist et la rattache à son propriétaire. */
    public function saveEmptyPlaylist(Playlist $pl, int $idUser): Playlist {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
            $st->execute(['nom' => $pl->get('nom')]);
            $pl->setId((int) $this->pdo->lastInsertId());
            $st = $this->pdo->prepare('INSERT INTO user2playlist (id_user, id_pl) VALUES (:u, :p)');
            $st->execute(['u' => $idUser, 'p' => $pl->get('id')]);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return $pl;
    }

    /* ---------- Pistes ---------- */

    public function saveTrack(AudioTrack $track): AudioTrack {
        $isAlbum = $track instanceof AlbumTrack;
        $sql = 'INSERT INTO track
                    (titre, genre, duree, filename, type,
                     artiste_album, titre_album, annee_album, numero_album,
                     auteur_podcast, date_posdcast)
                VALUES
                    (:titre, :genre, :duree, :filename, :type,
                     :artiste, :album, :annee, :numero,
                     :auteur, :date)';
        $date = $isAlbum ? null : $track->get('date');
        $params = [
            'titre' => $track->get('titre'),
            'genre' => $track->get('genre'),
            'duree' => $track->get('duree'),
            'filename' => $track->get('nomFichier'),
            'type' => $isAlbum ? 'A' : 'P',
            'artiste' => $isAlbum ? $track->get('artiste') : null,
            'album' => $isAlbum ? $track->get('album') : null,
            'annee' => $isAlbum ? $track->get('annee') : null,
            'numero' => $isAlbum ? $track->get('numeroPiste') : null,
            'auteur' => $isAlbum ? null : $track->get('auteur'),
            'date' => ($date === '' || $date === null) ? null : $date,
        ];
        $this->pdo->prepare($sql)->execute($params);
        $track->setId((int) $this->pdo->lastInsertId());
        return $track;
    }

    public function addTrackToPlaylist(int $idPlaylist, int $idTrack): void {
        $st = $this->pdo->prepare(
            'SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 FROM playlist2track WHERE id_pl = :pl');
        $st->execute(['pl' => $idPlaylist]);
        $no = (int) $st->fetchColumn();

        $ins = $this->pdo->prepare(
            'INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:pl, :track, :no)');
        $ins->execute(['pl' => $idPlaylist, 'track' => $idTrack, 'no' => $no]);
    }
}
