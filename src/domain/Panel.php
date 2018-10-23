<?php
namespace quotemaker\domain;

class Panel
{
    protected $id;
    protected $fenceType;
    protected $description;
    protected $width;
    protected $height;
    protected $postLength;
    protected $installation;
    protected $price;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFenceType()
    {
        return $this->fenceType;
    }

    public function setFenceType($fenceType)
    {
        $this->fenceType = $fenceType;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getPostLength()
    {
        return $this->postLength;
    }

    public function setPostLength($postLength)
    {
        $this->postLength = $postLength;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPRICE($price)
    {
        $this->price = $price;
    }

    public function getInstallation()
    {
        return $this->installation;
    }

    public function setInstallation($installation)
    {
        $this->installation = $installation;
    }

    public function getFormattedPrice()
    {
        $formattedCost = "$" . number_format(($this->getPrice()/100), 2, '.', ',');
        return $formattedCost;
    }

    public function getFormattedInstallation()
    {
        $formattedCost = "$" . number_format(($this->getInstallation()/100), 2, '.', ',');
        return $formattedCost;
    }
}
