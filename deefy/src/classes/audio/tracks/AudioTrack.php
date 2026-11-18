<?php
namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
class AudioTrack {
    protected int $id = 0;
    protected String $titre;
    private String $auteur = '';
    private String $genre = '';
    private int $duree = 0;
    protected String $nomFichier;
    public function __construct(String $t, String $nF){
        $this->titre = $t;
        $this->nomFichier = $nF;
    }
    public function get(string $at):mixed {
        if (property_exists ($this, $at)) return $this->$at;
        throw new InvalidPropertyNameException();
    }
    public function setId(int $id){
        $this->id = $id;
    }
    public function setTitre(String $t){
        $this->titre = $t;
    }
    public function setAuteur(String $a){
        $this->auteur = $a;
    }
    public function setGenre(String $g){
        $this->genre = $g;
    }
    public function setDuree(int $du){
        if ($du < 0) throw new InvalidPropertyValueException("duree: invalid value");
        $this->duree = $du;
    }
    public function setNomFichier(String $nF){
        $this->nomFichier = $nF;
    }

}