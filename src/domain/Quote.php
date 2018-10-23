<?php

namespace quotemaker\domain;

class Quote
{
    protected $id;
    protected $customerId;
    protected $fenceTypeId;
    protected $finalised;
    protected $quoteDate;
    protected $quoteLines;
    protected $quoteUUID;
    protected $totalCost;
    protected $priceAdjustment;
    protected $quoteVersion;
    protected $myobQuoteUID;
    protected $myobInvoiceNumber;
    protected $myobRowVersion;
    private $itemTypes = array();

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

    public function getFenceTypeId()
    {
        return $this->fenceTypeId;
    }

    public function setFenceTypeId($value)
    {
        $this->fenceTypeId = $value;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($value)
    {
        $this->customerId = $value;
    }

    public function getTotalCost()
    {
        $tc = 0;
        foreach ($this->quoteLines as $ql) {
            $tc += $ql->getCost();
        }

        if ($this->getPriceAdjustment() != null)
        {
            $tc = $tc + ($tc * $this->getPriceAdjustment() / 100);
        }
        
        if ($this->isLessThanMinimumPanels()) {
            $tc += 30000; // add $300 if less tha min number of panels
        }
        
        return $tc;
    }

    public function setTotalCost($value)
    {
        $this->totalCost = $value;
    }

    public function getQuoteUUID()
    {
        return $this->quoteUUID;
    }

    public function setQuoteUUID($value)
    {
        $this->quoteUUID = $value;
    }

    public function getQuoteLines()
    {
        return $this->quoteLines;
    }

    public function getFormattedQuoteLines()
    {        
        $defaultColorbond = $this->getDefaultColorbondSettings();
        foreach($this->quoteLines as $ql) {
            if (!$this->isDefaultColourbond($ql, $defaultColorbond)) {
                $ql->setShowInfo(true);
            }
        }
        
        // sort the quote lines
        usort($this->quoteLines, array($this, 'compareQuoteItem'));
        
        return $this->quoteLines;
    }
    
    public function setQuoteLines($value)
    {
        $this->quoteLines = $value;
    }

    public function getTotalLength()
    {
        $theLength = 0;
        $arr_length = count ( $this->quoteLines );
        for($i = 0; $i < $arr_length; $i ++) {
            $theLength += $this->quoteLines [$i]->getLength ();
        }
        return $theLength;
    }
    
    public function getFormattedCost() 
    {
        $formattedCost = "$" . number_format(($this->getTotalCost()/100), 2, '.', ',');
        return $formattedCost;
    }

    public function getUnformattedCostIncGST()
    {
        $formattedCost = number_format(($this->getTotalCost() + $this->getGSTAmount())/100, 2, ".", "");
        return $formattedCost;
    }    
    
    public function getGSTAmount() {
        return $this->getTotalCost() * 0.1;
    }

    public function getFormattedGSTAmount()
    {
        return "$" . number_format($this->getGSTAmount()/100, 2, ".", ",");
    }

    public function getTotalIncGST()
    {
        return "$" . number_format(($this->getTotalCost() + $this->getGSTAmount())/100, 2, ".", ",");
    }

    public function getQuoteNumber() 
    {
        return "A-" . $this->getMyobInvoiceNumber();
    }

    public function getFinalised()
    {
        return $this->finalised;
    }

    public function setFinalised($finalised)
    {
        $this->finalised = $finalised;
    }

    /**
     * @return mixed
     */
    public function getQuoteDate()
    {
        return $this->quoteDate;
    }

    /**
     * @param mixed $quoteDate
     */
    public function setQuoteDate($quoteDate)
    {
        $this->quoteDate = $quoteDate;
    }

    /**
     * @param mixed $priceAdjustment
     */
    public function setPriceAdjustment($priceAdjustment)
    {
        $this->priceAdjustment = $priceAdjustment;
    }

    /**
     * @return mixed
     */
    public function getPriceAdjustment()
    {
        return $this->priceAdjustment;
    }
    
    public function getQuoteVersion() 
    {
      return $this->quoteVersion;
    }
    
    public function setQuoteVersion($value) 
    {
        $this->quoteVersion = $value;
    }
    
    /**
     * @return mixed
     */
    public function getMyobQuoteUID()
    {
        return $this->myobQuoteUID;
    }

    /**
     * @param mixed $myobQuoteUID
     */
    public function setMyobQuoteUID($myobQuoteUID)
    {
        $this->myobQuoteUID = $myobQuoteUID;
    }
    
    /**
     * @return mixed
     */
    public function getMyobInvoiceNumber()
    {
        return $this->myobInvoiceNumber;
    }

    /**
     * @param mixed $myobQuoteNumber
     */
    public function setMyobInvoiceNumber($myobInvoiceNumber)
    {
        $this->myobInvoiceNumber = $myobInvoiceNumber;
    }
    
    /**
     * @return mixed
     */
    public function getMyobRowVersion()
    {
        return $this->myobRowVersion;
    }

    /**
     * @param mixed $myobRowVersion
     */
    public function setMyobRowVersion($myobRowVersion)
    {
        $this->myobRowVersion = $myobRowVersion;
    }
    
    /**
     * Returns whether the quote is less than the minimum number of panels (8)
     * @return boolean
     */
    public function isLessThanMinimumPanels()
    {
        $hasGates = false;
        $totalPanels = 0;
        
        /* @var $ql QuoteLine */
        foreach($this->getQuoteLines() as $ql) {
            if ($ql->getItemType() == "SG" || $ql->getItemType() == "DG") {
                $hasGates = true;
            } else if ($ql->getItemType() == "C") {
                $totalPanels = $totalPanels + $ql->getNumberOfPanels();
            }
        }
        return (!$hasGates && $totalPanels < 8);
    }    

    public function getDefaultColorbondSettings()
    {
        $colorbondDefaults = [];
        $colorbondDefaults['panelId'] = 0;
        $colorbondDefaults['sheets'] = 0;
        $colorbondDefaults['colourId'] = 0;
        
        foreach($this->quoteLines as $ql) {
            if ($ql->getItemType() == "C") {
                $colorbondDefaults['panelId'] = $ql->getPanelId();
                $colorbondDefaults['sheets'] = $ql->getSheets();
                $colorbondDefaults['colourId'] = $ql->getColourId();
                break;
            }
        }
        return $colorbondDefaults;        
    }
    
    public function isDefaultColourbond($quoteLine, $colorbondDefaults)
    {
        return ($quoteLine->getPanelId() == $colorbondDefaults['panelId']) && 
            ($quoteLine->getSheets() == $colorbondDefaults['sheets']) && 
            ($quoteLine->getColourId() == $colorbondDefaults['colourId']);
    }

    public function hasSingleGate()
    {
        return array_key_exists("SG", $this->itemTypes);
    }

    public function hasDoubleGate()
    {
        return array_key_exists("DG", $this->itemTypes);
    }

    public function populateItemTypes()
    {      
        foreach($this->quoteLines as $ql) {
            if (!array_key_exists($ql->getItemType(), $this->itemTypes)) {
                $itemType = $ql->getService()->getItemById($ql->getItemId());
                $this->itemTypes["{$ql->getItemType()}"] = $itemType; // item types will always refer to the last first of the type 
            }
        }
    }

    public function getFooterText()
    {
        $footerText = "";
        $this->populateItemTypes();
        if (array_key_exists("DG", $this->itemTypes)) {
            $footerText = $this->itemTypes["DG"]->getFooterText();
        } else if (array_key_exists("SG", $this->itemTypes)) {
            $footerText = $this->itemTypes["SG"]->getFooterText();
        }

        return $footerText;
    }

    private static function compareQuoteItem($itemA, $itemB)
    {
        if ($itemA->getItemType() == "C" && $itemB->getItemType() != "C") {
            return -1;
        } else if ($itemA->getItemType() == "PT" && $itemB->getItemType() != "PT") {
            return 1;
        } else {
            return ($itemA->getId() < $itemB->getId()) ? -1 : 1;
        }
    }
    
}
