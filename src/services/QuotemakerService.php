<?php

namespace quotemaker\services;

use quotemaker\dao\UserDAO;
use quotemaker\domain\AdjustQuote;
use quotemaker\domain\Quote;
use quotemaker\dao\QuotemakerDAO;
use quotemaker\domain\QuoteLine;
use quotemaker\domain\QuoteLineQuantity;
use quotemaker\domain\QuoteLineColorbond;
use quotemaker\domain\QuoteLineNote;
use quotemaker\domain\QuoteLineDimension;
use quotemaker\domain\Customer;

class QuotemakerService
{
    /**
     * @var QuotemakerDAO
     */
    protected $dao;

    /**
     * @var UserDAO
     */
    protected $userDao;
    protected $gateService;

    public function __construct($dao, $userDao, $encoder, $gateService)
    {
        $this->dao = $dao;
        $this->userDao = $userDao;
        $this->encoder = $encoder;
        $this->gateService = $gateService;
    }

    public function getNumberOfPanels($panelArray)
    {
        $numberOfPanels = 0;
        foreach ( $panelArray as $panel ) {
            if ($panel ['type'] == 'p') {
                $numberOfPanels += $panel ['numberOfPanels'];
            }
        }
        return $numberOfPanels;
    }

    public function getInstallationCost($panelCount, $panelType)
    {
        return $panelCount * $panelType->getInstallation ();
    }

    public function getAllColours()
    {
        return $this->dao->getAllColours ();
    }

    public function getColourById($id)
    {
        return $this->dao->getColourById ( $id );
    }

    public function addColour($colour)
    {
        return $this->dao->addColour ( $colour );
    }

    public function deleteColour($id)
    {
        return $this->dao->deleteColour ( $id );
    }

    public function updateColour($colour)
    {
        return $this->dao->updateColour ( $colour );
    }

    public function getAllStyles()
    {
        return $this->dao->getAllStyles ();
    }

    public function getAllPanels($panelType = NULL)
    {
        return $this->dao->getAllPanels ( $panelType );
    }

    public function getPanelById($id)
    {
        return $this->dao->getPanelById ( $id );
    }

    public function addPanel($panel)
    {
        return $this->dao->addPanel ( $panel );
    }

    public function deletePanel($id)
    {
        return $this->dao->deletePanel ( $id );
    }

    public function updatePanel($panel)
    {
        return $this->dao->updatePanel ( $panel );
    }

    public function getAllFenceTypes()
    {
        return $this->dao->getAllFenceTypes();
    }

    public function getFenceTypeById($id)
    {
        return $this->dao->getFenceTypeById ( $id );
    }

    public function addFenceType($fenceType)
    {
        return $this->dao->addFenceType ( $fenceType );
    }

    public function deleteFenceType($id)
    {
        return $this->dao->deleteFenceType ( $id );
    }

    public function updateFenceType($fenceType)
    {
        return $this->dao->updateFenceType ( $fenceType );
    }

    public function getAllCustomers()
    {
        return $this->dao->getAllCustomers ();
    }

    public function getCustomerById($id)
    {
        return $this->dao->getCustomerById ( $id );
    }

    public function getQuotesForCustomer($id)
    {
        $quotes = $this->dao->getQuotesForCustomer ( $id );
        $newQuotes = array();
        foreach($quotes as $quote) {
            $newQuote = $this->getQuoteById($quote->getId());
            array_push($newQuotes, $newQuote);
        }
        return $newQuotes;
    }

    public function insertOrUpdateCustomer($customer)
    {
        //$customer = new Customer();
        //$customer->populateFromJson($customerJson);
        //var_dump($customer);
        //return $customer;
        return $this->dao->insertOrUpdateCustomer( $customer );
    }

    public function getOrInsertQuote($quote)
    {
        // look up the quote uuid,
        // if it exists, return it
        // otherwise insert a new one and return the
        // newly inserted id
        return $quote;
    }

    /*
     * Returns "getAllFenceTypes" in a format suitable for use in a Form's ChoiceArray
     */
    public function getAllFenceTypeAsChoiceArray()
    {
        $allFenceTypes = $this->getAllFenceTypes();
        $wanted = array ();
        foreach ( $allFenceTypes as $item ) {
            $wanted [$item ['description']] = $item ['id'];
        }
        return $wanted;
    }

    public function getNumberOfPanelsForLength($length, $panelId)
    {
        $panel = $this->getPanelById ( $panelId );
        $wholePanels = ceil ( $length / $panel->getWidth () );
        return $wholePanels;
    }

    public function insertQuote($quote, $modifiedBy, $isSiteUnseen)
    {
        $newQuoteId = $this->dao->insertQuote ( $quote, $modifiedBy );
        $boolIsSiteUnseen = (bool) $isSiteUnseen;
        if ($boolIsSiteUnseen) {
            $theItem = $this->getItemByDescription( "Site Unseen" );            
            $quoteLine = new QuoteLineNote();
            $quoteLine->setQuoteId($newQuoteId);
            $quoteLine->setItemId($theItem->getId());
            $quoteLine->setItemType($theItem->getType());                        
            $this->addQuoteLineToQuote($quoteLine, $modifiedBy);
        }
        return $newQuoteId;
    }

    public function insertQuoteLine($quoteLine, $modifiedBy)
    {
        return $this->dao->insertQuoteLine($quoteLine, $modifiedBy);
    }

    public function editQuoteLine($quoteLine, $modifiedBy)
    {
        $q = $this->dao->getQuoteById($quoteLine->getQuoteID());
        return $this->dao->editQuoteLine ($quoteLine, $modifiedBy, $q->getMyobQuoteUID());
    }

    public function getQuoteLinesByQuoteId($quoteId)
    {
        $lines = $this->dao->getQuoteLinesByQuoteId ( $quoteId );
        $newLines = array();
        foreach($lines as $ql) {
            $ql->setService($this);
            $newLine = $this->convertQuoteLine($ql);
            $newLine->populate();
            $newLines[] = $newLine;
        }
        return $newLines;
    }

    public function getQuoteById($quoteId)
    {
        $quote = $this->dao->getQuoteById ($quoteId);
        if ($quote) {
            $lines = $this->getQuoteLinesByQuoteId($quoteId);
            $quote->setQuoteLines($lines);
        }
        return $quote;
    }
    
    public function getMyobInvoiceNumberForQuote($quoteId, $revision=NULL)
    {
        $myobInvoiceNumber = NULL;
        if ($revision == NULL) {
            $quote = $this->getQuoteById($quoteId);
            $myobInvoiceNumber = $quote->getMyobInvoiceNumber();
        } else {
            $result = $this->dao->getMyobInvoiceNumberForQuote($quoteId, $revision);
            $myobInvoiceNumber = $result->myobInvoiceNumber;
        }
        return $myobInvoiceNumber;
    }
    
    public function getQuoteByInvoiceNumber($invoiceNumber)
    {
        $quoteId = $this->dao->getQuoteIdByInvoiceNumber($invoiceNumber);
        return $this->getQuoteById($quoteId);
    }
        
    public function getQuoteLineById($quoteId)
    {
        // get the object as a generic type
        $quoteLine = $this->dao->getQuoteLineById ( $quoteId );
        $newLine = $this->convertQuoteLine($quoteLine);
        return $newLine;
    }

    public function convertQuoteLine($quoteLine)
    {
        $newLine = NULL;
        /*
         * Depending on what type it was, convert to the appropriate type
         * possible types are:
         */
        if ($quoteLine->getItemType() == 'N') {
            $newLine = new QuoteLineNote();
        } else if ($quoteLine->getItemType() == 'C') {
            $newLine = new QuoteLineColorbond();
        } else if ($quoteLine->getItemType() == "SG" || $quoteLine->getItemType() == "DG") {
            // Gates
            $newLine = new QuoteLineDimension($this->gateService);
        } else {
            $newLine = new QuoteLineQuantity();
        }

        $newLine->import($quoteLine);
        return $newLine;
    }

    public function deleteQuote($id)
    {
        return $this->dao->deleteQuote( $id );
    }

    public function deleteQuoteLine($id, $modifiedBy)
    {
        return $this->dao->deleteQuoteLine ($id, $modifiedBy);
    }

    public function updateQuotePanelId($quoteId, $panelId)
    {
        return $this->dao->updateQuotePanelId ( $quoteId, $panelId );
    }

    public function getPanelCost(QuoteLine $quoteLine)
    {
        $numberOfPanels = $quoteLine->getNumberOfPanels();
        $panelObj = $this->getPanelById($quoteLine->getPanelId());
        return $numberOfPanels * ($panelObj->getPrice() + $panelObj->getInstallation());
    }

    public function getQuoteCost(Quote $quote)
    {
        $theCost = 0;
        foreach ($quote->getQuoteLines() as $line) {
            $theCost = $theCost + $this->getLineCost($line);
        }
        $quote->setTotalCost($theCost);
        $this->dao->updateQuoteCost($quote->getId(), $quote->getTotalCost());
        return $theCost;
    }

    public function getAllNotes()
    {
        return $this->dao->getAllNotes();
    }

    public function getNoteById($id)
    {
        return $this->dao->getNoteById ( $id );
    }

    public function addNote($note)
    {
        return $this->dao->addNote($note);
    }

    public function deleteNote($id)
    {
        return $this->dao->deleteNote($id);
    }

    public function updateNote($note)
    {
        return $this->dao->updateNote($note);
    }

    public function updateQuoteFinalised($quoteId, $isFinalised)
    {
        return $this->dao->updateQuoteFinalised($quoteId, $isFinalised);
    }

    public function getStyleById($styleId)
    {
        return $this->dao->getStyleById($styleId);
    }

    public function getAllItems()
    {
        return $this->dao->getAllItems();
    }

    public function getItemById($itemId)
    {
        return $this->dao->getItemById($itemId);
    }

    public function getItemByDescription($description)
    {
        return $this->dao->getItemByDescription($description);
    }    
    
    public function updateItem($item)
    {
        return $this->dao->updateItem($item);
    }

    public function deleteItem($itemId)
    {
        return $this->dao->deleteItem($itemId);
    }

    public function addItem($item)
    {
        return $this->dao->addItem($item);
    }

    public function getRecentCustomers()
    {
        return $this->dao->getAllCustomers();
    }

    public function getAllUsers()
    {
        return $this->userDao->getAllUsers();
    }


    public function getRecentQuotes()
    {
        $quotes = $this->dao->getRecentQuotes();
        $newQuotes = array();
        foreach($quotes as $quote) {
            $newQuote = $this->getQuoteById($quote->getId());
            array_push($newQuotes, $newQuote);
        }
        return $newQuotes;
    }

    public function addQuoteLineToQuote($item, $modifiedBy)
    {
        $item->setService($this);
        $this->insertQuoteLine ($item, $modifiedBy);
    }

    public function getUserById($id, $cargs)
    {
        return $this->userDao->getUserById($id, $cargs);
    }

    public function getUserByUsername($username)
    {
        return $this->userDao->getUserByUsername($username);
    }

    public function adjustQuote($id, $percentage, $modifiedBy)
    {        
        $result = $this->dao->adjustQuote($id, $percentage, $modifiedBy);
        return $result;
    }

    public function addUser($user)
    {
        return $this->userDao->insertUser($user);
    }

    public function updateUser($user)
    {
        $this->userDao->updateUser ($user);
        if ( !empty($user->getPassword()) ) {
            $this->userDao->updatePassword($user);
        }
    }

    public function deleteUser($userId)
    {
        return $this->userDao->deleteUser($userId);
    }

    public function getAdjustment($quoteId)
    {
        $quote = $this->getQuoteById($quoteId);
        $adjustment = new AdjustQuote();
        $adjustment->setPercentage($quote->getPriceAdjustment());
        $adjustment->setQuoteId($quoteId);
        return $adjustment;
    }

    public function getGatePricing()
    {
        return $this->dao->getGatePricing();
    }

    public function updateGatePricing($gatePricing)
    {
        return $this->dao->updateGatePricing($gatePricing);
    }

//     public function saveQuoteVersion($quoteId, $sentBy)
//     {
//         return $this->dao->saveQuoteVersion($quoteId, $sentBy);
//     }

    public function quoteVersionHistory($quoteId)
    {
        return $this->dao->quoteVersionHistory($quoteId);
    }

    public function emailSent($quoteId, $quoteVersion)
    {
        return $this->dao->emailSent($quoteId, $quoteVersion);
    }

    public function pdfCreated($quoteId, $quoteVersion)
    {
        return $this->dao->pdfCreated($quoteId, $quoteVersion);
    }

    /**
     * Gets the default panel type a quote.
     * "Default" is defined as the panel type of the first "Colorbond" line item on a quote.
     * If there is none, sensible defaults are choosen.
     */
    public function getDefaultQuoteSettings($quoteId)
    {
        /* @var $quote Quote */
        $quote = $this->getQuoteById($quoteId);
        $lines = $this->getQuoteLinesByQuoteId ($quoteId);

        $colorbondDefaults = array();
        $colorbondDefaults['panelId'] = $this->dao->getDefaultPanelByFenceType($quote->getFenceTypeId())->getId();
        $colorbondDefaults['sheets'] = "Neeta";
        $colorbondDefaults['colourId'] = 14;

        /* @var $ql QuoteLine */
        foreach($lines as $ql) {
            if ($ql->getItemType() == "C" || $ql->getItemType() == "SG" || $ql->getItemType() == "DG") {
                $colorbondDefaults['panelId'] = $ql->getPanelId();
                $colorbondDefaults['sheets'] = $ql->getSheets();
                $colorbondDefaults['colourId'] = $ql->getColourId();
                break;
            }
        }
        return $colorbondDefaults;
    }
    
    public function updateMyobFileSetting($myob_file_uri) 
    {
        return $this->dao->updateApplicationSetting("myob_company_file", $myob_file_uri);
    }

    public function updateOauthTokens($accessToken, $refreshToken, $expiresIn)
    {
        $tokenArray = array();
        $tokenArray['accessToken'] = $accessToken;
        $tokenArray['refreshToken'] = $refreshToken;
        $tokenArray['expiresIn'] = $expiresIn;

        return $this->dao->updateApplicationSettingJSON('myobOAuthTokens', $tokenArray);
    }
    
    public function getOauthTokens() 
    {
        $tokens = $this->dao->getApplicationSettingJSONByName('myobOAuthTokens');
        return $tokens;

    }

    public function getMyobFileUri()
    {
        return $this->dao->getApplicationSettingByName("myob_company_file");
    }
    
    public function deleteMyobSettings()
    {
        $this->dao->deleteApplicationSettingByName("myobOAuthTokens");
        $this->dao->deleteApplicationSettingByName("myob_company_file");
    }
    
    public function updateQuoteWithMyobData($quoteId, $quoteVersion, $myobData)
    {
        $this->dao->updateQuoteWithMyobData($quoteId, $myobData->UID);
        $this->dao->updateQuoteHistoryWithMyobQuoteNumber($quoteId, $quoteVersion, $myobData->Number, $myobData->RowVersion);
    }
    
    public function getEmailBodyText() 
    {
        return $this->dao->getApplicationSettingByName("emailBody");
    }

    public function setEmailBodyText($newText)
    {
        return $this->dao->updateApplicationSetting("emailBody", $newText);
    }

    public function getEmailFooterText() 
    {
        return $this->dao->getApplicationSettingByName("emailFooter");
    }

    public function setEmailFooterText($newText)
    {
        return $this->dao->updateApplicationSetting("emailFooter", $newText);
    }
    
    public function searchCustomers($searchTerm, $myobOAuth)
    {
        $jsonData = $myobOAuth->searchCustomers($searchTerm);
        // now process the results into an array of Customer options
        $objectArray = [];
        
        foreach ($jsonData["Items"] as $customerJson) {
            $customer = new Customer();
            $customer->populateFromJson($customerJson);
            $objectArray[] = $customer;
        }
        
        return $objectArray;
    }

}
