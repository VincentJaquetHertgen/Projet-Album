<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastTrackRenderer implements Renderer {
    const COMPACT = 1;
    const LONG = 2;
    public PodcastTrack $podcast;

    public function __construct(PodcastTrack $pod) {
        $this->podcast = $pod;
    }

    public function render(int $selector): string {
        return match ($selector) {
            self::COMPACT => $this->renderCompact(),
            self::LONG => $this->renderLong(),
            default => '',
        };
    }

    private function audio(): string {
        return '<audio controls src="' . Html::e($this->podcast->get('nomFichier')) . '"></audio>';
    }

    private function renderCompact(): string {
        return '<article><strong>' . Html::e($this->podcast->get('titre')) . '</strong> — '
            . Html::e($this->podcast->get('auteur')) . '<br>' . $this->audio() . '</article>';
    }

    private function renderLong(): string {
        return '<article><h3>' . Html::e($this->podcast->get('titre')) . '</h3>'
            . '<h4>Auteur : ' . Html::e($this->podcast->get('auteur')) . '</h4>'
            . '<p>Genre : ' . Html::e($this->podcast->get('genre'))
            . ' — Date : ' . Html::e($this->podcast->get('date')) . '</p>'
            . $this->audio() . '</article>';
    }
}
