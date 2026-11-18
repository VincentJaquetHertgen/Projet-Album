<?php

namespace loader;

class Psr4ClassLoader
{
    private string $prefix;
    private string $baseDir;

    public function __construct(string $prefix, string $baseDir)
    {
        // on normalise : le préfixe finit par \ et le répertoire par /
        $this->prefix = rtrim($prefix, '\\') . '\\';
        $this->baseDir = rtrim($baseDir, '/\\') . '/';
    }

    public function loadClass(string $className): void
    {
        // la classe n'appartient pas à notre préfixe : on ne fait rien
        if (strpos($className, $this->prefix) !== 0) {
            return;
        }

        // on retire le préfixe : iutnc\deefy\audio\lists\Playlist -> audio\lists\Playlist
        $relative = substr($className, strlen($this->prefix));

        // \ devient / et on ajoute l'extension
        $file = $this->baseDir . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }
}