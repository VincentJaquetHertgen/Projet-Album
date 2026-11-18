<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioListRender {
    private AudioList $audioList;

    public function __construct(AudioList $al) {
        $this->audioList = $al;
    }

    public function render(): string {
        $res = '<h2>Playlist : ' . Html::e($this->audioList->get('nom')) . '</h2>';
        $res .= '<p>Nombre de pistes : ' . Html::e($this->audioList->get('nbPistes'))
            . ' — Durée totale : ' . Html::e($this->audioList->get('dureeTotale')) . ' s</p>';
        foreach ($this->audioList->get('listePistes') as $piste) {
            if ($piste instanceof AlbumTrack) {
                $renderer = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof PodcastTrack) {
                $renderer = new PodcastTrackRenderer($piste);
            } else {
                throw new InvalidPropertyNameException('Type de piste inconnu');
            }
            $res .= $renderer->render(1);
        }
        return $res;
    }
}
