<?php
declare(strict_types=1);

namespace iutnc\deefy\auth;

use iutnc\deefy\Repository\DeefyRepository;

class Authz {
    /** Fonctionnalités 1 et 4 : utilisateur authentifié. */
    public static function checkSignedIn(): array {
        $user = AuthnProvider::getSignedInUser();
        if ($user === null) {
            throw new AuthnException('Vous devez être connecté pour accéder à cette page.');
        }
        return $user;
    }

    /** Fonctionnalités 2, 3 et 5 : utilisateur authentifié ET propriétaire de la playlist. */
    public static function checkPlaylistOwner(int $idPlaylist): array {
        $user = self::checkSignedIn();
        if (!DeefyRepository::getInstance()->isPlaylistOwner($user['id'], $idPlaylist)) {
            throw new AuthzException("Accès refusé : cette playlist ne vous appartient pas.");
        }
        return $user;
    }
}
