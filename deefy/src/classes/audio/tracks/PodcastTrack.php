<?php
namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
class PodcastTrack extends AudioTrack{
    private String $auteur = '';
    private String $date = '';
    private String $genre = '';
    private int $duree = 0;
    public function __construct(String $t, String $nF){
        parent::__construct($t, $nF);
    }
    public function get(string $at):mixed {
        if (property_exists ($this, $at)) return $this->$at;
        throw new InvalidPropertyNameException ();
    }
    public function setTitre(String $t){
        $this->titre = $t;
    }
    public function setAuteur(String $a){
        $this->auteur = $a;
    }
    public function setDate(String $d){
        $this->date = $d;
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
    public function __toString(): String {
        return json_encode(get_object_vars($this));
    }
}