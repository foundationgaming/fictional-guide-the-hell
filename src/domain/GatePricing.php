<?php
/**
 * Created by PhpStorm.
 * User: AMyers
 * Date: 26/01/2018
 * Time: 12:31 PM
 */

namespace quotemaker\domain;

class GatePricing
{
    protected $rails = 0; // cost per rail
    protected $rhs35x65 = 0; // cost per lineal metre
    protected $rhs65x65 = 0; // cost per lineal metre
    protected $dLatch = 0; // cost of D Latch plus handle and rubber
    protected $hinges = 0; // cost per set of hinge
    protected $dropBolt = 0; // cost - 1 needed for double gates only
    protected $coverStrip = 0; // 1 per gate
    protected $postCaps = 0; // cost for 1, require 4 for single, 6 for doubles
    protected $labour = 0; // cost per gate
    
    // Powder coating
    protected $powderCoatGate = 0; // per square metre
    protected $powderCoatRHS65x65 = 0; // per lineal metre
    protected $powderCoatDLatch = 0; // per latch
    protected $powderCoatHingesCost = 0; // per hinge
    protected $powderCoatDropBolt = 0; // per drop bolt
    protected $powderCoatCaps = 0; // per cap
    
    // Panels
    protected $sheetCost1200 = 0;
    protected $sheetCost1500 = 0;
    protected $sheetCost1800 = 0;
    protected $sheetCost2100 = 0;
    
    // Install
    protected $installSingleCost = 0;
    protected $installDoubleCost = 0;
    
    // Profit
    protected $profitSingleCost = 0;
    protected $profitDoubleCost = 0;
    
    public function __construct() {}

    /**
     * @return int
     */
    public function getRails()
    {
        return $this->rails;
    }

    /**
     * @param int $rails
     */
    public function setRails($rails)
    {
        $this->rails = $rails;
    }

    /**
     * @return int
     */
    public function getRhs35x65()
    {
        return $this->rhs35x65;
    }

    /**
     * @param int $rhs35x65
     */
    public function setRhs35x65($rhs35x65)
    {
        $this->rhs35x65 = $rhs35x65;
    }

    /**
     * @return int
     */
    public function getRhs65x65()
    {
        return $this->rhs65x65;
    }

    /**
     * @param int $rhs65x65
     */
    public function setRhs65x65($rhs65x65)
    {
        $this->rhs65x65 = $rhs65x65;
    }

    /**
     * @return int
     */
    public function getDLatch()
    {
        return $this->dLatch;
    }

    /**
     * @param int $dLatch
     */
    public function setDLatch($dLatch)
    {
        $this->dLatch = $dLatch;
    }

    /**
     * @return int
     */
    public function getHinges()
    {
        return $this->hinges;
    }

    /**
     * @param int $hinges
     */
    public function setHinges($hinges)
    {
        $this->hinges = $hinges;
    }

    /**
     * @return int
     */
    public function getDropBolt()
    {
        return $this->dropBolt;
    }

    /**
     * @param int $dropBolt
     */
    public function setDropBolt($dropBolt)
    {
        $this->dropBolt = $dropBolt;
    }

    /**
     * @return int
     */
    public function getCoverStrip()
    {
        return $this->coverStrip;
    }

    /**
     * @param int $coverStrip
     */
    public function setCoverStrip($coverStrip)
    {
        $this->coverStrip = $coverStrip;
    }

    /**
     * @return int
     */
    public function getPostCaps()
    {
        return $this->postCaps;
    }

    /**
     * @param int $postCaps
     */
    public function setPostCaps($postCaps)
    {
        $this->postCaps = $postCaps;
    }

    /**
     * @return int
     */
    public function getLabour()
    {
        return $this->labour;
    }

    /**
     * @param int $labour
     */
    public function setLabour($labour)
    {
        $this->labour = $labour;
    }
    /**
     * @return number
     */
    public function getPowderCoatGate()
    {
        return $this->powderCoatGate;
    }
    

    /**
     * @return number
     */
    public function getPowderCoatRHS65x65()
    {
        return $this->powderCoatRHS65x65;
    }
    

    /**
     * @return number
     */
    public function getPowderCoatDLatch()
    {
        return $this->powderCoatDLatch;
    }
    

    /**
     * @return number
     */
    public function getPowderCoatHingesCost()
    {
        return $this->powderCoatHingesCost;
    }
    

    /**
     * @return number
     */
    public function getPowderCoatDropBolt()
    {
        return $this->powderCoatDropBolt;
    }
    

    /**
     * @return number
     */
    public function getPowderCoatCaps()
    {
        return $this->powderCoatCaps;
    }
    

    /**
     * @param number $powderCoatGate
     */
    public function setPowderCoatGate($powderCoatGate)
    {
        $this->powderCoatGate = $powderCoatGate;
    }
    

    /**
     * @param number $powderCoatRHS65x65
     */
    public function setPowderCoatRHS65x65($powderCoatRHS65x65)
    {
        $this->powderCoatRHS65x65 = $powderCoatRHS65x65;
    }
    

    /**
     * @param number $powderCoatDLatch
     */
    public function setPowderCoatDLatch($powderCoatDLatch)
    {
        $this->powderCoatDLatch = $powderCoatDLatch;
    }
    

    /**
     * @param number $powderCoatHingesCost
     */
    public function setPowderCoatHingesCost($powderCoatHingesCost)
    {
        $this->powderCoatHingesCost = $powderCoatHingesCost;
    }
    

    /**
     * @param number $powderCoatDropBolt
     */
    public function setPowderCoatDropBolt($powderCoatDropBolt)
    {
        $this->powderCoatDropBolt = $powderCoatDropBolt;
    }
    

    /**
     * @param number $powderCoatCaps
     */
    public function setPowderCoatCaps($powderCoatCaps)
    {
        $this->powderCoatCaps = $powderCoatCaps;
    }
    /**
     * @return number
     */
    public function getSheetCost1200()
    {
        return $this->sheetCost1200;
    }
    

    /**
     * @return number
     */
    public function getSheetCost1500()
    {
        return $this->sheetCost1500;
    }
    

    /**
     * @return number
     */
    public function getSheetCost1800()
    {
        return $this->sheetCost1800;
    }
    

    /**
     * @return number
     */
    public function getSheetCost2100()
    {
        return $this->sheetCost2100;
    }
    

    /**
     * @param number $sheetCost1200
     */
    public function setSheetCost1200($sheetCost1200)
    {
        $this->sheetCost1200 = $sheetCost1200;
    }
    

    /**
     * @param number $sheetCost1500
     */
    public function setSheetCost1500($sheetCost1500)
    {
        $this->sheetCost1500 = $sheetCost1500;
    }
    

    /**
     * @param number $sheetCost1800
     */
    public function setSheetCost1800($sheetCost1800)
    {
        $this->sheetCost1800 = $sheetCost1800;
    }
    

    /**
     * @param number $sheetCost2100
     */
    public function setSheetCost2100($sheetCost2100)
    {
        $this->sheetCost2100 = $sheetCost2100;
    }
    
    /**
     * @return number
     */
    public function getInstallSingleCost()
    {
        return $this->installSingleCost;
    }
    

    /**
     * @return number
     */
    public function getInstallDoubleCost()
    {
        return $this->installDoubleCost;
    }
    

    /**
     * @return number
     */
    public function getProfitSingleCost()
    {
        return $this->profitSingleCost;
    }
    

    /**
     * @return number
     */
    public function getProfitDoubleCost()
    {
        return $this->profitDoubleCost;
    }
    

    /**
     * @param number $installSingleCost
     */
    public function setInstallSingleCost($installSingleCost)
    {
        $this->installSingleCost = $installSingleCost;
    }
    

    /**
     * @param number $installDoubleCost
     */
    public function setInstallDoubleCost($installDoubleCost)
    {
        $this->installDoubleCost = $installDoubleCost;
    }
    

    /**
     * @param number $profitSingleCost
     */
    public function setProfitSingleCost($profitSingleCost)
    {
        $this->profitSingleCost = $profitSingleCost;
    }
    

    /**
     * @param number $profitDoubleCost
     */
    public function setProfitDoubleCost($profitDoubleCost)
    {
        $this->profitDoubleCost = $profitDoubleCost;
    }
       
}