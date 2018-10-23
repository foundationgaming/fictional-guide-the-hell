<?php

namespace quotemaker\domain;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class QuoteLineDimension extends QuoteLineColorbond
{

    protected $gateService;

    public function __construct($gateService)
    {
        $this->gateService = $gateService;
    }


    public function getGateCostBreakdown() {
        if ($this->getItemType() == "SG") {
            // single gates
            $this->gateService->getGateCost($this->getHeight(), $this->getLength(), false);
            return $this->gateService->getGateCostBreakdown();
        }

        if ($this->getItemType() == "DG") {
            // double gates
            $this->gateService->getGateCost($this->getHeight(), $this->getLength(), true);
            return $this->gateService->getGateCostBreakdown();
        } else {
            return array();
        }
    }

    public function getPrice()
    {
        if ($this->getItemType() == "SG") {
            // single gates
            return $this->gateService->getGateCost($this->getHeight(), $this->getLength(), false) * $this->quantity;
        }

        if ($this->getItemType() == "DG") {
            // double gates
            return $this->gateService->getGateCost($this->getHeight(), $this->getLength(), true) * $this->quantity;
        } else {
            $theItem = $this->service->getItemById($this->getItemId());
            return $theItem->getUnitCost() * $this->quantity;
        }
    }

    public function getFormattedDetail($lineBreak = "<br>")
    {
        $theItem = $this->service->getItemById($this->getItemId());
        $theDescription = "";
        if ($this->getItemId() == 3) {
            // SINGLE GATES
            $gateWidth = $this->getLength() - 30;
            $theDescription = str_replace("{quantity}", $this->quantity, $theItem->getQuoteWording());
            $theDescription = str_replace("{set}", $this->getSingularOrPlural("set", "sets"), $theDescription);
            $theDescription = str_replace("{gate}", $this->getSingularOrPlural("gate", "gates"), $theDescription);
            $theDescription = str_replace("{width}", "{$gateWidth}mm", $theDescription);
            $theDescription = str_replace("{height}","{$this->getHeight()}mm", $theDescription);
            if ($this->getShowInfo() == 'YES') {
                $theDescription = $theDescription . "{$lineBreak}Colour - {$this->getColour()->getDescription()}";
                $theDescription = $theDescription . "{$lineBreak}Sheets - {$this->getSheets()}";
            }

        } else if ($this->getItemId() == 4) {
            // DOUBLE GATES
            $gateWidth = ($this->getLength() - 45) / 2;
            $theDescription = str_replace("{quantity}", $this->quantity, $theItem->getQuoteWording());
            $theDescription = str_replace("{set}", $this->getSingularOrPlural("set", "sets"), $theDescription);
            $theDescription = str_replace("{gate}", $this->getSingularOrPlural("gate", "gates"), $theDescription);
	        $theDescription = str_replace("{width}","{$gateWidth}mm each leaf", $theDescription);
	        $theDescription = str_replace("{height}","{$this->getHeight()}mm", $theDescription);
            if ($this->getShowInfo() == 'YES') {
                $theDescription = $theDescription . "{$lineBreak}Colour - {$this->getColour()->getDescription()}";
                $theDescription = $theDescription . "{$lineBreak}Sheets - {$this->getSheets()}";
            }
        } else {
            $theItem = $this->service->getItemById($this->getItemId());
            $theDescription = "{$theItem->getQuoteWording()}";
        }

        return $theDescription;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        
        $metadata->addPropertyConstraint('length', new Assert\GreaterThan(array(
            'value' => 0,
        )));
        
        $metadata->addPropertyConstraint('length', new Assert\GreaterThan(array(
            'value' => 30,
            'groups' => array('single', 'double')
        )));

        $metadata->addPropertyConstraint('length', new Assert\LessThanOrEqual(array(
            'value' => 2400,
            'groups' => array('single')
        )));

        $metadata->addPropertyConstraint('length', new Assert\LessThanOrEqual(array(
            'value' => 4800,
            'groups' => array('double')
        )));
    }

}
