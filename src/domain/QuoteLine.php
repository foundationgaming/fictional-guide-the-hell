<?php

namespace quotemaker\domain;

use quotemaker\services\QuotemakerService;

class QuoteLine
{
    protected $id;
    protected $quoteId;
    protected $cost = 0;
    protected $notes;
    protected $colourId;
    protected $itemType;
    protected $itemId;
    protected $length;
    protected $numberOfPanels;
    protected $panelId;
    protected $sheets;
    protected $styleId;
    protected $lineOrder;
    protected $height;
    protected $description;
    protected $quantity = 1;
    protected $showInfo = "NO";

    /**
     * @var QuotemakerService
     */
    protected $service;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getQuoteId()
    {
        return $this->quoteId;
    }

    public function setQuoteId($value)
    {
        $this->quoteId = $value;
    }

    public function getCost()
    {
        /*
         * NOTE- the "cost" value is the value stored in the database.  To calculate the cost, use $this->calculateCost()
         */
        if ($this->cost < 0) {
            return $this->cost / 11 * 10; // add GST
        } else {
            return $this->cost;
        }
    }

    public function setCost($value)
    {
        $this->cost = $value;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($value)
    {
        $this->notes = $value;
    }

    public function getColourId()
    {
        return $this->colourId;
    }

    public function setColourId($value)
    {
        $this->colourId = $value;
    }

    public function getItemType()
    {
        return $this->itemType;
    }

    public function setItemType($value)
    {
        $this->itemType = $value;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        if (is_numeric ( $length )) {
            $this->length = floor ( $length );
        }
    }

    public function getNumberOfPanels()
    {
        return $this->numberOfPanels;
    }

    public function setNumberOfPanels($numPanels)
    {
        if (is_numeric ( $numPanels )) {
            $this->numberOfPanels = floor ( $numPanels );
        }
    }

    public function getPanelId()
    {
        return $this->panelId;
    }

    public function setPanelId($panelId)
    {
        $this->panelId = $panelId;
    }

    public function getLineOrder() {
      return $this->lineOrder;
    }

    public function setLineOrder($value) {
      $this->lineOrder = $value;
    }

    public function getFormattedDescription()
    {
        if ($this->getItemType () == 'C') {
            return "Colourbond Fencing";
        } else if ($this->getItemType () == 'D') {
            return "Colourbond Fencing (Defaults)";
        } else if ($this->getItemType () == 'SG') {
            return "Single Gate";
        } else if ($this->getItemType () == 'DG') {
            return "Double Gate";
        } else if ($this->getItemType() == 'N') {
            return "Notes";
        } else {
            $item = $this->service->getItemById($this->getItemId());
            return $item->getDescription();
        }
    }

    public function getFormattedDetail($lineBreak = "<br>")
    {
        if ($this->getItemType () == 'C') {
            $panelDetails = $this->service->getPanelById($this->getPanelId());
            $colourDetails = $this->service->getColourById($this->getColourId());
            $styleDetails = $this->service->getStyleById($this->getStyleId());
            $resultString = "{$this->getLengthInMetres()}m ({$this->getNumberOfPanels()} {$this->getPanelSingularOrPlural()}) of colorbond fencing on {$this->getBoundaryNote()}";
            $resultString = $resultString . "{$lineBreak}Height - {$panelDetails->getHeight()}mm";
            $resultString = $resultString . "{$lineBreak}Colour - {$colourDetails->getDescription()}";
            $resultString = $resultString . "{$lineBreak}Sheets - {$this->getSheets()}";
            $resultString = $resultString . "{$lineBreak}Style - {$styleDetails->description}";
            return $resultString;
        } else if ($this->getItemType() == 'N' && $this->getCost() == 0) {
            return "NOTES:{$lineBreak}" . $this->getNotes();
        } else if ($this->getItemType() == 'N' && $this->getCost() < 0) {
            return "";
        } else {
            return $this->getNotes();
        }
    }

    public function getPanelSingularOrPlural()
    {
        if ($this->getNumberOfPanels () == 1) {
            return "panel";
        } else {
            return "panels";
        }
    }

    public function getBoundaryNote()
    {
        $returnVal = '';
        switch ($this->notes) {
            case 'L':
                $returnVal = "left side boundary";
                break;
            case 'R':
                $returnVal = "right side boundary";
                break;
            case 'LF':
                $returnVal = "left front";
                break;
            case 'RF':
                $returnVal = "right front";
                break;
            case 'B':
                $returnVal = "back boundary";
                break;
        }

        return $returnVal;
    }

    public function getStyleId()
    {
        return $this->styleId;
    }

    public function setStyleId($styleId)
    {
        $this->styleId = $styleId;
    }

    public function getSheets()
    {
        return $this->sheets;
    }

    public function setSheets($sheets)
    {
        $this->sheets = $sheets;
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    public function getLengthInMetres()
    {
        return $this->length / 1000;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getDescription()
    {
      return $this->description;
    }

    public function setDescription($value)
    {
      $this->description = $value;
    }

    public function getQuantity()
    {
      return $this->quantity;
    }

    public function setQuantity($value)
    {
      $this->quantity = $value;
    }

    public function getService()
    {
      return $this->service;
    }

    public function setService($value)
    {
      $this->service = $value;
    }

    public function getSingularOrPlural($singlar, $plural)
    {
        if ($this->quantity == 1) {
            return $singlar;
        } else {
            return $plural;
        }
    }

    /**
     * Imports all properties from the supplied object into the instance of this object
     * @param $object
     */
    public function import($object)
    {
        $vars=is_object($object)?get_object_vars($object):$object;
        if(!is_array($vars)) throw Exception('no props to import into the object!');
        foreach ($vars as $key => $value) {
            $this->$key = $value;
        }
    }

    public function populate()
    {
        // default implementation doesn't do anything
    }

    public function getPrice()
    {
        return $this->cost;
    }

    /**
     * @return boolean
     */
    public function getShowInfo()
    {
        return $this->showInfo;
    }


    /**
     * @param boolean $showInfo
     */
    public function setShowInfo($showInfo)
    {
        $this->showInfo = $showInfo;
    }

}
