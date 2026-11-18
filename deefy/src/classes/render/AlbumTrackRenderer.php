<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer implements Renderer {
    const COMPACT = 1;
    const LONG = 2;
    public AlbumTrack $piste;

    public function __construct(AlbumTrack $p) {
        $this->piste = $p;
    }

    public function render(int $selector): string {
        return match ($selector) {
            self::COMPACT => $this->renderCompact(),
            self::LONG => $this->renderLong(),
            default => '',
        };
    }

    private function audio(): string {
        return '<audio controls src="' . Html::e($this->piste->get('nomFichier')) . '"></audio>';
    }

    private function renderCompact(): string {
        return '<article><strong>' . Html::e($this->piste->get('titre')) . '</strong> '
            . '(' . Html::e($this->piste->get('album')) . ')<br>' . $this->audio() . '</article>';
    }

    private function renderLong(): string {
        return '<article><h3>' . Html::e($this->piste->get('titre')) . '</h3>'
            . '<h4>Album : ' . Html::e($this->piste->get('album')) . '</h4>'
            . '<h4>N° de piste : ' . Html::e($this->piste->get('numeroPiste')) . '</h4>'
            . $this->audio() . '</article>';
    }
}
