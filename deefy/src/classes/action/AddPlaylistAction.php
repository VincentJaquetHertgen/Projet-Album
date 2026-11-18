<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRender;
use iutnc\deefy\Repository\DeefyRepository;

class AddPlaylistAction extends Action {

    public function execute(): string {
        $user = Authz::checkSignedIn();
        return $this->http_method === 'POST' ? $this->post($user['id']) : $this->form();
    }

    private function form(): string {
        return <<<HTML
<h2>Créer une playlist</h2>
<form method="post" action="?action=add-playlist">
    <label for="nom">Nom de la playlist</label>
    <input type="text" id="nom" name="nom" maxlength="128" required>
    <button type="submit">Créer</button>
</form>
HTML;
    }

    private function post(int $idUser): string {
        // Pas de filtre "sanitize" à l'entrée : on stocke la valeur brute
        // (requête préparée = pas d'injection SQL) et on échappe à l'affichage (pas de XSS).
        $nom = trim((string) ($_POST['nom'] ?? ''));
        if ($nom === '' || strlen($nom) > 128) {
            return '<p>Nom invalide.</p><p><a href="?action=add-playlist">Réessayer</a></p>';
        }

        $playlist = new Playlist($nom);
        DeefyRepository::getInstance()->saveEmptyPlaylist($playlist, $idUser);
        $_SESSION['playlist'] = $playlist;

        return (new AudioListRender($playlist))->render()
            . '<p><a href="?action=add-track" role="button">Ajouter une piste</a></p>';
    }
}
