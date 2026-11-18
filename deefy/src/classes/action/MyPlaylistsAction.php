<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authz;
use iutnc\deefy\Repository\DeefyRepository;
use iutnc\deefy\render\Html;

class MyPlaylistsAction extends Action {

    public function execute(): string {
        $user = Authz::checkSignedIn();
        $playlists = DeefyRepository::getInstance()->findPlaylistsByUser($user['id']);

        $html = '<h2>Mes playlists</h2>';
        if (count($playlists) === 0) {
            return $html . '<p>Vous n\'avez pas encore de playlist. <a href="?action=add-playlist">Créer une playlist</a></p>';
        }
        $html .= '<ul>';
        foreach ($playlists as $pl) {
            $html .= '<li><a href="?action=playlist&amp;id=' . (int) $pl->get('id') . '">'
                . Html::e($pl->get('nom')) . '</a></li>';
        }
        return $html . '</ul>';
    }
}
