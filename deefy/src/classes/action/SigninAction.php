<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnException;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\render\Html;

class SigninAction extends Action {

    public function execute(): string {
        return $this->http_method === 'POST' ? $this->post() : $this->form();
    }

    private function form(string $erreur = ''): string {
        $msg = $erreur === '' ? '' : '<p role="alert"><strong>' . Html::e($erreur) . '</strong></p>';
        return <<<HTML
<h2>Connexion</h2>
$msg
<form method="post" action="?action=signin">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>
    <label for="passwd">Mot de passe</label>
    <input type="password" id="passwd" name="passwd" required>
    <button type="submit">Se connecter</button>
</form>
HTML;
    }

    private function post(): string {
        try {
            AuthnProvider::signin(trim((string) ($_POST['email'] ?? '')), (string) ($_POST['passwd'] ?? ''));
        } catch (AuthnException $e) {
            return $this->form($e->getMessage());
        }
        return '<p>Connexion réussie.</p><p><a href="?action=my-playlists">Voir mes playlists</a></p>';
    }
}
