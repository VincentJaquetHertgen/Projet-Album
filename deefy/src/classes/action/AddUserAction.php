<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnException;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\render\Html;

class AddUserAction extends Action {

    public function execute(): string {
        return $this->http_method === 'POST' ? $this->post() : $this->form();
    }

    private function form(string $erreur = ''): string {
        $msg = $erreur === '' ? '' : '<p role="alert"><strong>' . Html::e($erreur) . '</strong></p>';
        $min = AuthnProvider::MIN_PASSWORD_LENGTH;
        return <<<HTML
<h2>Inscription</h2>
$msg
<form method="post" action="?action=add-user">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>
    <label for="passwd">Mot de passe ($min caractères minimum)</label>
    <input type="password" id="passwd" name="passwd" minlength="$min" required>
    <label for="passwd2">Confirmer le mot de passe</label>
    <input type="password" id="passwd2" name="passwd2" minlength="$min" required>
    <button type="submit">S'inscrire</button>
</form>
HTML;
    }

    private function post(): string {
        $email = trim((string) ($_POST['email'] ?? ''));
        $pass = (string) ($_POST['passwd'] ?? '');
        if ($pass !== (string) ($_POST['passwd2'] ?? '')) {
            return $this->form('Les deux mots de passe sont différents.');
        }
        try {
            AuthnProvider::register($email, $pass);
        } catch (AuthnException $e) {
            return $this->form($e->getMessage());
        }
        return '<p>Compte créé. <a href="?action=signin">Vous pouvez maintenant vous connecter.</a></p>';
    }
}
