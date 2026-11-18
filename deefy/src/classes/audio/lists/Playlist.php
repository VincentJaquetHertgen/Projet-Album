<?php

namespace iutnc\deefy\audio\lists;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\audio\tracks\AudioTrack;
require_once __DIR__ . '/AudioList.php';

class Playlist extends AudioList
{
    public function ajouterPiste(AudioTrack $piste): void
    {
        $this->listePistes[] = $piste;
        $this->nbPistes++;
        $this->dureeTotale += $piste->get('duree');
    }

    public function supprimerPiste(int $indice): void
    {
        if (!isset($this->listePistes[$indice])) {
            throw new InvalidPropertyValueException();
        }
        $this->dureeTotale -= $this->listePistes[$indice]->get('duree');
        // array_splice supprime ET réindexe le tableau
        array_splice($this->listePistes, $indice, 1);
        $this->nbPistes--;
    }

    public function ajouterPistes(array $pistes): void
    {
        foreach ($pistes as $piste) {
            // Doublons : on ignore une piste (même objet) déjà présente
            if (!in_array($piste, $this->listePistes, true)) {
                $this->ajouterPiste($piste);
            }
        }
    }
        public function get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException("Propriété inexistante : $name");
    }
}