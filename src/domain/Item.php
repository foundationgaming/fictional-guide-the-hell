<?php
namespace quotemaker\domain;

class Item
{
    protected $id;
    protected $description;
    protected $instructions;
    protected $unitCost;
    protected $quoteWording;
    protected $type;
    protected $footerText;

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

    /**
     * @param mixed $unitCost
     */
    public function setUnitCost($unitCost)
    {
        $this->unitCost = $unitCost;
    }

    /**
     * @return mixed
     */
    public function getUnitCost()
    {
        return $this->unitCost;
    }

    public function getFormattedUnitCost()
    {
        $formattedCost = "$" . number_format(($this->unitCost/100), 2, '.', ',');
        return $formattedCost;
    }

    /**
     * @return mixed
     */
    public function getQuoteWording()
    {
        return $this->quoteWording;
    }

    /**
     * @param mixed $quoteWording
     */
    public function setQuoteWording($quoteWording)
    {
        $this->quoteWording = $quoteWording;
    }

    /**
     * @return mixed
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * @param mixed $instructions
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFooterText()
    {
        return $this->footerText;
    }

    /**
     * @param mixed $footerText
     */
    public function setFooterText($footerText)
    {
        $this->footerText = $footerText;
    }    
}
