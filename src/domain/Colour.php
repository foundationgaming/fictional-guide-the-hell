<?php
namespace quotemaker\domain;

class Colour
{
    protected $id;
    protected $description;
    protected $hexCode;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getHexCode()
    {
        return $this->hexCode;
    }

    public function setHexCode($hexCode)
    {
        $this->hexCode = $hexCode;
    }    
}
