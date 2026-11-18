<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\render\Html;

class DefaultAction extends Action {

    public function execute(): string {
        $user = AuthnProvider::getSignedInUser();
        if ($user === null) {
            return '<h2>Bienvenue sur Deefy</h2>'
                . '<p>Gérez vos playlists audio. <a href="?action=signin">Connectez-vous</a>'
                . ' ou <a href="?action=add-user">créez un compte</a> pour commencer.</p>';
        }
        return '<h2>Bienvenue ' . Html::e($user['email']) . '</h2>'
            . '<ul><li><a href="?action=my-playlists">Mes playlists</a></li>'
            . '<li><a href="?action=add-playlist">Créer une playlist vide</a></li>'
            . '<li><a href="?action=playlist">Afficher la playlist courante</a></li></ul>';
    }
}
