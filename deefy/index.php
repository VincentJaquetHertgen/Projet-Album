<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\Repository\DeefyRepository;

session_start();
DeefyRepository::setConfig(__DIR__ . '/db.config.ini');

$dispatcher = new Dispatcher();
$dispatcher
    ->addRoute('default', 'iutnc\deefy\action\DefaultAction')
    ->addRoute('signin', 'iutnc\deefy\action\SigninAction')
    ->addRoute('signout', 'iutnc\deefy\action\SignoutAction')
    ->addRoute('add-user', 'iutnc\deefy\action\AddUserAction')
    ->addRoute('my-playlists', 'iutnc\deefy\action\MyPlaylistsAction')
    ->addRoute('playlist', 'iutnc\deefy\action\DisplayPlaylistAction')
    ->addRoute('add-playlist', 'iutnc\deefy\action\AddPlaylistAction')
    ->addRoute('add-track', 'iutnc\deefy\action\AddPodcastTrackAction');
$dispatcher->run();
