<?php
declare(strict_types=1);

namespace iutnc\deefy\auth;

use iutnc\deefy\Repository\DeefyRepository;

class AuthnProvider {
    public const ROLE_STANDARD = 1;
    public const ROLE_ADMIN = 100;
    public const MIN_PASSWORD_LENGTH = 10;

    /** Vérifie les credentials et ouvre la session de l'utilisateur. */
    public static function signin(string $email, string $password): void {
        $user = DeefyRepository::getInstance()->findUserByEmail($email);
        // Message volontairement identique dans les deux cas (pas d'énumération de comptes)
        if ($user === null || !password_verify($password, $user['passwd'])) {
            throw new AuthnException('Email ou mot de passe incorrect.');
        }
        session_regenerate_id(true); // contre la fixation de session
        $_SESSION['user'] = ['id' => (int) $user['id'], 'email' => $user['email'], 'role' => (int) $user['role']];
        unset($_SESSION['playlist']);
    }

    /** Crée un compte STANDARD (mot de passe haché avec bcrypt). */
    public static function register(string $email, string $password): void {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new AuthnException('Adresse email invalide.');
        }
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new AuthnException('Le mot de passe doit contenir au moins ' . self::MIN_PASSWORD_LENGTH . ' caractères.');
        }
        $repo = DeefyRepository::getInstance();
        if ($repo->findUserByEmail($email) !== null) {
            throw new AuthnException('Un compte existe déjà avec cet email.');
        }
        $repo->createUser($email, password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), self::ROLE_STANDARD);
    }

    public static function signout(): void {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function getSignedInUser(): ?array {
        return $_SESSION['user'] ?? null;
    }
}
