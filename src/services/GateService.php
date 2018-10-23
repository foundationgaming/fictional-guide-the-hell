<?php

namespace quotemaker\services;


use quotemaker\domain\GatePricing;

class GateService
{

    /**
     * @var GatePricing
     */
    protected $costs;    
    protected $costBreakdown = array();
    
    public function __construct($dao)
    {
        $this->costs = $dao->getGatePricing();
    }

    public function getGateCost($height, $width, $isDouble)
    {
        $numberOfGates = ($isDouble) ? 2 : 1;

        $widthOfEachLeaf = $width;

        if ($isDouble) {
            $widthOfEachLeaf = ($width - 45) / 2;
        } else {
            $widthOfEachLeaf = $width - 30;
        }

        // work out number sheets
        $numberOfSheets = $this->calculateNumberOfSheets($widthOfEachLeaf) * $numberOfGates;
        $numberOfRails = $this->calculateNumberOfRails($widthOfEachLeaf) * $numberOfGates;
        $rhs65x65Length = $this->calculatePostLength($height); // will be 2 of these per single gate, and 2 per double gate
        $rhs35x65Length = $height;
        $numberOfHinges = ($isDouble) ? 4 : 2;
        $numberOfDropbolts = ($isDouble) ? 1 : 0;
        $numberOfPostCaps = ($isDouble) ? 6 : 4;

        // work out cost
        $railsCost = $this->costs->getRails() * $numberOfRails;
        $rhs35x65Cost = $rhs35x65Length / 1000 * 2 * $numberOfGates * $this->costs->getRhs35x65();
        $rhs65x65Cost = $rhs65x65Length / 1000 * 2 * $this->costs->getRhs65x65();
        
        $this->costBreakdown['railsCost'] = $railsCost;
        $this->costBreakdown['rhs35x65Cost'] = $rhs35x65Cost;
        $this->costBreakdown['rhs65x65Cost'] = $rhs65x65Cost;
        
        $dLatchCost = $this->costs->getDLatch();
        
        $this->costBreakdown['dLatchCost'] = $dLatchCost;
        
        $dropBoltCost = $numberOfDropbolts * $this->costs->getDropBolt();
        $hingesCost = $this->costs->getHinges() * $numberOfGates;
        $coverStripCost = $this->costs->getCoverStrip() * $numberOfGates;
        $postCapsCost = $this->costs->getPostCaps() * $numberOfPostCaps;
        $sheetsCost = $this->calculateSheetsCost($numberOfSheets, $height);
        $powderCoatCost = $this->calculatePowderCoatCost($height, $widthOfEachLeaf, $rhs65x65Length, $numberOfHinges, $numberOfDropbolts, $numberOfPostCaps, $numberOfGates);
        $labourCost = ($isDouble) ? 2 * $this->costs->getLabour() : $this->costs->getLabour();
        
        $profit = ( ($isDouble) ? $this->costs->getProfitDoubleCost() : $this->costs->getProfitSingleCost() );
        $installation = ( ($isDouble) ? $this->costs->getInstallDoubleCost() : $this->costs->getInstallSingleCost() );
        
        $this->costBreakdown['dropBoltCost'] = $dropBoltCost;
        $this->costBreakdown['hingesCost'] = $hingesCost;
        $this->costBreakdown['coverStripCost'] = $coverStripCost;
        $this->costBreakdown['postCapsCost'] = $postCapsCost;
        $this->costBreakdown['sheetsCost'] = $sheetsCost;
        $this->costBreakdown['powderCoatCost'] = $powderCoatCost;
        $this->costBreakdown['labourCost'] = $labourCost;
        $this->costBreakdown['profit'] = $profit;
        $this->costBreakdown['installation'] = $installation;

        return ($railsCost + $rhs35x65Cost + $rhs65x65Cost + $dLatchCost + $dropBoltCost + $hingesCost + $coverStripCost + $postCapsCost + $sheetsCost + $powderCoatCost + $labourCost + $profit + $installation);
    }

    public function calculatePowderCoatCost($height, $width, $rhs65x65Length, $numberOfHinges, $numberOfDropbolts, $numberOfPostCaps, $numberOfGates)
    {        

        $powderCoatGateCost = $height / 1000 * $width / 1000 * $this->costs->getPowderCoatGate() * $numberOfGates;
        $powderCoatRHS65x65Cost = ($rhs65x65Length / 1000 * $this->costs->getPowderCoatRHS65x65() * 2);
        $powderCoatDLatchCost = $this->costs->getPowderCoatDLatch();
        $powderCoatHingesCost  = ($numberOfHinges * $this->costs->getPowderCoatHingesCost());;
        $powderCoatDropBoltCost = ($numberOfDropbolts * $this->costs->getPowderCoatDropBolt());;
        $powderCoatCapsCost = ($numberOfPostCaps* $this->costs->getPowderCoatCaps());

        $powderCoatCost = $powderCoatGateCost;        
        $powderCoatCost = $powderCoatCost + $powderCoatRHS65x65Cost;        
        $powderCoatCost = $powderCoatCost + $powderCoatDLatchCost;        
        $powderCoatCost = $powderCoatCost + $powderCoatHingesCost;        
        $powderCoatCost = $powderCoatCost + $powderCoatDropBoltCost;        
        $powderCoatCost = $powderCoatCost + $powderCoatCapsCost;
        
        
        $this->costBreakdown['getPowderCoatGate'] = $powderCoatGateCost;
        $this->costBreakdown['getPowderCoatRHS65x65'] = $powderCoatRHS65x65Cost;
        $this->costBreakdown['getPowderCoatDLatch'] = $powderCoatDLatchCost;
        $this->costBreakdown['getPowderCoatHingesCost'] = $powderCoatHingesCost;
        $this->costBreakdown['getPowderCoatDropBolt'] = $powderCoatDropBoltCost;
        $this->costBreakdown['getPowderCoatCaps'] = $powderCoatCapsCost;

        return $powderCoatCost;
    }

    public function calculateNumberOfSheets($width)
    {
        if ($width <= 870) {
            return 1;
        } else if ($width <= 1500) {
            return 2;
        } else {
            return 3;
        }
    }

    public function calculateNumberOfRails($width)
    {
        if ($width <= 1100) {
            return 1;
        } else {
            return 2;
        }
    }

    public function calculatePostLength($height)
    {
        return 600 + $height;
    }

    // cost per sheet (cost depends on height of sheet)
    public function calculateSheetsCost($numberOfSheets, $height)
    {        
        $sheetCost = 0.0;
        if ($height <= 1200) { 
            $sheetCost = $numberOfSheets * $this->costs->getSheetCost1200();            
        } else if ($height <= 1500) {
            $sheetCost = $numberOfSheets * $this->costs->getSheetCost1500();
        } else if ($height <= 1800) {
            $sheetCost = $numberOfSheets * $this->costs->getSheetCost1800();
        } else {
            $sheetCost = $numberOfSheets * $this->costs->getSheetCost2100();
        }
        
        $this->costBreakdown['sheetCost'] = $sheetCost;
        return $sheetCost;
    }
    
    public function getGateCostBreakdown() {
        return $this->costBreakdown;
    }

}