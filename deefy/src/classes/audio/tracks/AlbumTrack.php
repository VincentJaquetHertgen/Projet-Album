<?php
namespace iutnc\deefy\audio\tracks;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
class AlbumTrack extends AudioTrack{
    private String $artiste = '';
    private String $album;
    private int $annee = 0;
    private int $numeroPiste;
    private String $genre = '';
    private int $duree = 0;
    public function __construct(String $t, String $nF, String $a, int $nP){
        parent::__construct($t, $nF);
        $this->album = $a;
        $this->numeroPiste = $nP;
    }
    public function get(string $at):mixed {
        if (property_exists ($this, $at)) return $this->$at;
        throw new InvalidPropertyNameException();
    }
    public function setTitre(String $t){
        $this->titre = $t;
    }
    public function setArtiste(String $a){
        $this->artiste = $a;
    }
    public function setAnnee(int $ann){
        $this->annee = $ann;
    }
    public function setNumeroPiste(int $nP){
        $this->numeroPiste = $nP;
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