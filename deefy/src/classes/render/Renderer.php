<?php
namespace iutnc\deefy\render;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
interface Renderer {
    public function render(int $selector) : String;
}