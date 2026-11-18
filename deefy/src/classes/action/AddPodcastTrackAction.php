<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRender;
use iutnc\deefy\Repository\DeefyRepository;

class AddPodcastTrackAction extends Action {

    public function execute(): string {
        Authz::checkSignedIn();
        $playlist = $_SESSION['playlist'] ?? null;
        if (!$playlist instanceof Playlist) {
            return '<p>Aucune playlist courante. <a href="?action=my-playlists">Choisissez-en une</a>'
                . ' ou <a href="?action=add-playlist">créez-en une</a>.</p>';
        }
        Authz::checkPlaylistOwner((int) $playlist->get('id'));

        return $this->http_method === 'POST' ? $this->post($playlist) : $this->form();
    }

    private function form(): string {
        return <<<HTML
<h2>Ajouter une piste</h2>
<form method="post" action="?action=add-track" enctype="multipart/form-data">
    <label for="titre">Titre</label>
    <input type="text" id="titre" name="titre" maxlength="100" required>
    <label for="auteur">Auteur</label>
    <input type="text" id="auteur" name="auteur" maxlength="100">
    <label for="genre">Genre</label>
    <input type="text" id="genre" name="genre" maxlength="30">
    <label for="date">Date</label>
    <input type="date" id="date" name="date">
    <label for="duree">Durée (secondes)</label>
    <input type="number" id="duree" name="duree" min="0" value="0">
    <label for="userfile">Fichier audio (.mp3)</label>
    <input type="file" id="userfile" name="userfile" accept=".mp3,audio/mpeg" required>
    <button type="submit">Ajouter</button>
</form>
HTML;
    }

    private function post(Playlist $playlist): string {
        $titre = trim((string) ($_POST['titre'] ?? ''));
        if ($titre === '' || strlen($titre) > 100) {
            return '<p>Titre invalide.</p><p><a href="?action=add-track">Réessayer</a></p>';
        }
        $auteur = trim((string) ($_POST['auteur'] ?? ''));
        $genre = trim((string) ($_POST['genre'] ?? ''));
        if (strlen($auteur) > 100 || strlen($genre) > 30) {
            return '<p>Auteur ou genre trop long.</p><p><a href="?action=add-track">Réessayer</a></p>';
        }
        $date = trim((string) ($_POST['date'] ?? ''));
        if ($date !== '' && \DateTime::createFromFormat('Y-m-d', $date) === false) {
            $date = '';
        }
        $duree = filter_var($_POST['duree'] ?? 0, FILTER_VALIDATE_INT);
        if ($duree === false || $duree < 0) {
            $duree = 0;
        }

        // Contrôle de l'upload : erreur PHP, extension ET vrai type MIME (lu sur le contenu)
        $f = $_FILES['userfile'] ?? null;
        if ($f === null || $f['error'] !== UPLOAD_ERR_OK
            || strtolower(substr($f['name'], -4)) !== '.mp3'
            || !is_uploaded_file($f['tmp_name'])
            || (new \finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']) !== 'audio/mpeg') {
            return '<p>Fichier invalide : seuls les .mp3 sont acceptés.</p>'
                . '<p><a href="?action=add-track">Réessayer</a></p>';
        }

        // Nom aléatoire, extension .mp3 imposée par le serveur
        $chemin = 'audio/' . bin2hex(random_bytes(16)) . '.mp3';
        if (!move_uploaded_file($f['tmp_name'], __DIR__ . '/../../../' . $chemin)) {
            return '<p>Erreur lors de l\'enregistrement du fichier.</p>';
        }

        $track = new PodcastTrack($titre, $chemin);
        $track->setAuteur($auteur);
        $track->setGenre($genre);
        $track->setDate($date);
        $track->setDuree($duree);

        // Persistance en BD puis mise à jour de la playlist courante en session
        $repo = DeefyRepository::getInstance();
        $repo->saveTrack($track);
        $repo->addTrackToPlaylist((int) $playlist->get('id'), (int) $track->get('id'));
        $playlist->ajouterPiste($track);

        return (new AudioListRender($playlist))->render()
            . '<p><a href="?action=add-track" role="button">Ajouter encore une piste</a></p>';
    }
}
