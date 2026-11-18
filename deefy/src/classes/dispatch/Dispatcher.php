<?php
declare(strict_types=1);

namespace iutnc\deefy\dispatch;

use iutnc\deefy\auth\AuthnException;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\AuthzException;
use iutnc\deefy\render\Html;

class Dispatcher {

    private string $action;
    private array $routes = [];

    public function __construct() {
        $this->action = (string) ($_GET['action'] ?? 'default');
    }

    public function addRoute(string $action, string $class): self {
        $this->routes[$action] = $class;
        return $this;
    }

    public function run(): void {
        // action inconnue -> page d'accueil
        $class = $this->routes[$this->action] ?? $this->routes['default'];
        try {
            $html = (new $class())->execute();
        } catch (AuthnException $e) {
            http_response_code(401);
            $html = '<p><strong>' . Html::e($e->getMessage()) . '</strong></p>'
                . '<p><a href="?action=signin">Se connecter</a> ou <a href="?action=add-user">créer un compte</a></p>';
        } catch (AuthzException $e) {
            http_response_code(403);
            $html = '<p><strong>' . Html::e($e->getMessage()) . '</strong></p>'
                . '<p><a href="?action=my-playlists">Retour à mes playlists</a></p>';
        } catch (\Throwable $e) {
            http_response_code(500);
            error_log((string) $e);
            $html = '<p>Une erreur est survenue. <a href="?action=default">Retour à l\'accueil</a></p>';
        }
        $this->renderPage($html);
    }

    private function nav(): string {
        $user = AuthnProvider::getSignedInUser();
        if ($user === null) {
            return '<li><a href="?action=default">Accueil</a></li>'
                . '<li><a href="?action=signin">Se connecter</a></li>'
                . '<li><a href="?action=add-user">S\'inscrire</a></li>';
        }
        return '<li><a href="?action=default">Accueil</a></li>'
            . '<li><a href="?action=my-playlists">Mes playlists</a></li>'
            . '<li><a href="?action=add-playlist">Créer une playlist</a></li>'
            . '<li><a href="?action=playlist">Playlist courante</a></li>'
            . '<li><a href="?action=signout">Déconnexion (' . Html::e($user['email']) . ')</a></li>';
    }

    private function renderPage(string $html): void {
        $nav = $this->nav();
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DeefyApp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.min.css">
</head>
<body>
    <header class="container">
        <h1>DeefyApp</h1>
        <nav>
            <ul>$nav</ul>
        </nav>
    </header>
    <main class="container">
        $html
    </main>
</body>
</html>
HTML;
    }
}
