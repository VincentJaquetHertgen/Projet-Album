<?php
namespace iutnc\deefy\audio\lists;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
require_once __DIR__ . '/AudioList.php';

class Album extends AudioList
{
    protected string $artiste = '';
    protected string $dateSortie = '';

    public function __construct(string $nom, array $listePistes)
    {
        parent::__construct($nom, $listePistes);
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setDateSortie(string $dateSortie): void
    {
        $this->dateSortie = $dateSortie;
    }
}