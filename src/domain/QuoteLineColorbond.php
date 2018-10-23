<?php
namespace quotemaker\domain;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class QuoteLineColorbond extends QuoteLine
{
    protected $colour;
    protected $style;
    protected $panel;
    protected $colourbondDefaults;

    public function populate()
    {
        $this->setColour($this->service->getColourById($this->colourId));
        $this->setStyle($this->service->getStyleById($this->styleId));
        $this->setPanel($this->service->getPanelById($this->panelId));
    }

    public function getHeight()
    {
        return $this->getPanel()->getHeight();
    }

    public function getColour()
    {
        if (!$this->colour) {
            return $this->service->getColourById($this->colourbondDefaults['colourId']);
        } else {
            return $this->colour;
        }
    }

    public function setColour($value)
    {
      $this->colour = $value;
    }

    public function getStyle()
    {
      return $this->style;
    }

    public function setStyle($value)
    {
      $this->style = $value;
    }

    public function getPanel()
    {
      return $this->panel;
    }

    public function setPanel($value)
    {
      $this->panel = $value;
    }

    public function getColourId() {
        return $this->colour->getId();
    }

    public function getStyleId()
    {
        if ($this->style) {
            return $this->style->id;
        } else {
            return $this->styleId;
        }
    }

    public function getPanelId()
    {
        if (!$this->panel) {
            return $this->colourbondDefaults['panelId'];
        } else {
            return $this->panel->getId();
        }
    }

    public function getNumberOfPanels()
    {
        return $this->service->getNumberOfPanelsForLength($this->getLength(), $this->getPanelId());
    }

    public function getPrice()
    {
        return $this->service->getPanelCost($this);
    }

    public function setDefaults($colourbondDefaults)
    {
        $this->colourbondDefaults = $colourbondDefaults;
        $this->colourId = $colourbondDefaults['colourId'];
        $this->panelId = $colourbondDefaults['panelId'];
        $this->sheets = $colourbondDefaults['sheets'];
        $this->populate();
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {        
        $metadata->addPropertyConstraint('length', new Assert\GreaterThan(array(
            'value' => 0,
        )));        
        
        $metadata->addPropertyConstraint('length', new Assert\GreaterThan(array(
            'value' => 0,
//             'groups' => array('fence'),
//                 'message' => 'Length must be more than 8 panels'
        ))); 
    }
    
}
