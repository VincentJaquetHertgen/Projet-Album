<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;

class SignoutAction extends Action {

    public function execute(): string {
        AuthnProvider::signout();
        return '<p>Vous êtes déconnecté.</p><p><a href="?action=default">Retour à l\'accueil</a></p>';
    }
}
