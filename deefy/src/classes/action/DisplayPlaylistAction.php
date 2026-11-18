<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRender;
use iutnc\deefy\Repository\DeefyRepository;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        // ?id=N : on charge la playlist depuis la BD et elle devient la playlist courante
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($id === false) {
                return '<p>Identifiant de playlist invalide.</p>';
            }
            Authz::checkPlaylistOwner($id);
            $playlist = DeefyRepository::getInstance()->findPlaylistById($id);
            if ($playlist === null) {
                return '<p>Playlist introuvable.</p>';
            }
            $_SESSION['playlist'] = $playlist;
        } else {
            // sinon : playlist courante en session
            Authz::checkSignedIn();
            $playlist = $_SESSION['playlist'] ?? null;
            if (!$playlist instanceof Playlist) {
                return '<p>Aucune playlist courante. <a href="?action=my-playlists">Choisir une playlist</a>'
                    . ' ou <a href="?action=add-playlist">en créer une</a>.</p>';
            }
            Authz::checkPlaylistOwner((int) $playlist->get('id'));
        }

        return (new AudioListRender($playlist))->render()
            . '<p><a href="?action=add-track" role="button">Ajouter une piste</a></p>';
    }
}
