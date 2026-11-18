<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

/** Échappement systématique de tout ce qui est affiché (parade XSS). */
class Html {
    public static function e(mixed $s): string {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
