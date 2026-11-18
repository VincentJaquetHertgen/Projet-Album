<?php
namespace iutnc\deefy\audio\lists;

use Iterator;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList implements Iterator
{
    protected int $id = 0;
    protected string $nom;
    protected int $nbPistes;
    protected int $dureeTotale;
    protected array $listePistes;
    private int $position = 0;

    public function __construct(string $nom, array $listePistes = [])
    {
        $this->nom = $nom;
        $this->listePistes = $listePistes;
        $this->nbPistes = count($listePistes);
        $this->dureeTotale = 0;
        foreach ($listePistes as $piste) {
            $this->dureeTotale += $piste->get('duree');
        }
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException("Propriété inexistante : $name");
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->listePistes[$this->position]);
    }

    public function current(): AudioTrack
    {
        return $this->listePistes[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }
}