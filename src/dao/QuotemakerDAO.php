<?php

namespace quotemaker\dao;

use \PDO;
use quotemaker\domain\GatePricing;

/**
 * @author AMyers
 *
 */
class QuotemakerDAO {

    private $db = NULL;

    function __construct($theDB)
    {
        $this->db = $theDB;
    }

    // COLOURS
    public function getAllColours()
    {
        $sql =<<<SQL
        SELECT id, hex_code, description
        FROM colours
        WHERE 1 = 1
        ORDER BY description
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, "\\quotemaker\\domain\\Colour");
    }

    public function getColourById($colourId)
    {
        $sql =<<<SQL
        SELECT id,
        description,
        hex_code AS hexCode
        FROM colours
        WHERE id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $colourId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Colour");
        return $result;
    }

    public function addColour($colour)
    {
        $sql = <<<SQL
        INSERT INTO colours(hex_code, description)
        VALUES(:hexCode, :description)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("hexCode", $colour->getHexCode());
        $statement->bindValue("description", $colour->getDescription());
        $statement->execute();
        return $statement->rowCount();
    }

    public function deleteColour($colourId)
    {
        $sql = <<<SQL
        DELETE FROM colours WHERE id = :colourId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("colourId", $colourId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function updateColour($colour)
    {
        $sql = <<<SQL
        UPDATE colours
        SET hex_code = :hexCode,
        description = :description
        WHERE id = :colourId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("colourId", $colour->getId());
        $statement->bindValue("hexCode", $colour->getHexCode());
        $statement->bindValue("description", $colour->getDescription());
        $statement->execute();
        return $statement->rowCount();
    }

    // PANELS
    public function getAllPanels($fenceType = NULL) {
        $typeClause = '';
        if (!is_null($fenceType)) {
            $typeClause = "AND fence_type = :fenceType";
        }

        $sql =<<<SQL
        SELECT p.id, p.width, p.height, p.post_length, p.price, p.installation, p.fence_type, f.description
        FROM panels p, fence_types f
        WHERE 1 = 1
        $typeClause
        AND p.fence_type = f.id
        ORDER BY fence_type, height, width, description
SQL;
        $statement = $this->db->prepare($sql);
        if (!is_null($fenceType)) {
            $statement->bindParam("fenceType", $fenceType);
        }
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, "\\quotemaker\\domain\\Panel");
        return $result;
    }

    public function addPanel($panel)
    {
        $sql = <<<SQL
        INSERT INTO panels(fence_type, width, price, description, height, post_length, installation)
        VALUES(:fenceType, :width, :price, :description, :height, :postLength, :installation)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("fenceType", $panel->getFenceType());
        $statement->bindValue("width", $panel->getWidth());
        $statement->bindValue("price", $panel->getPrice());
        $statement->bindValue("description", $panel->getDescription());
        $statement->bindValue("height", $panel->getHeight());
        $statement->bindValue("postLength", $panel->getPostLength());
        $statement->bindValue("installation", $panel->getInstallation());
        $statement->execute();
        return $statement->rowCount();
    }


    public function updatePanel($panel)
    {
        $sql = <<<SQL
        UPDATE panels
        SET fence_type = :fenceType,
        width = :width,
        price = :price,
        description = :description,
        height = :height,
        post_length = :postLength,
        installation = :installation
        WHERE id = :panelId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("panelId", $panel->getId());
        $statement->bindValue("fenceType", $panel->getFenceType());
        $statement->bindValue("width", $panel->getWidth());
        $statement->bindValue("price", $panel->getPrice());
        $statement->bindValue("description", $panel->getDescription());
        $statement->bindValue("height", $panel->getHeight());
        $statement->bindValue("postLength", $panel->getPostLength());
        $statement->bindValue("installation", $panel->getInstallation());
        $statement->execute();
        return $statement->rowCount();
    }

    // STYLES

    public function getAllStyles() {
        $sql =<<<SQL
        SELECT id, description
        FROM styles
        WHERE 1 = 1
        ORDER BY description
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function addStyle($description)
    {
        $sql = <<<SQL
        INSERT INTO styles(description)
        VALUES(:description)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("description", $description);
        $statement->execute();
        return $statement->rowCount();
    }

    public function getPanelById($panelId)
    {
        $sql =<<<SQL
        SELECT id,
        description,
        height,
        width,
        fence_type AS fenceType,
        post_length AS postLength,
        installation,
        price
        FROM panels
        WHERE id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $panelId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Panel");
        return $result;
    }

    public function getStyleById($styleId)
    {
        $sql =<<<SQL
        SELECT id, description
        FROM styles
        WHERE id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $styleId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function getAllCustomers()
    {
        $sql =<<<SQL
        SELECT id, customer_name, email, phone, mobile
        FROM customers
        WHERE 1 = 1
        ORDER BY customer_name
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePanel($panelId)
    {
        $sql = <<<SQL
        DELETE FROM panels WHERE id = :panelId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("panelId", $panelId);
        $statement->execute();
        return $statement->rowCount();
    }

    // FENCE_TYPES
    public function getAllFenceTypes() {
        $sql =<<<SQL
        SELECT id, description
        FROM fence_types
        WHERE active = 1
        ORDER BY description
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function addFenceType($fenceType)
    {
        $sql = <<<SQL
        INSERT INTO fence_types(description)
        VALUES(:description)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("description", $fenceType->getDescription());
        $statement->execute();
        return $statement->rowCount();
    }


    public function updateFenceType($fenceType)
    {
        $sql = <<<SQL
        UPDATE fence_types
        SET description = :description
        WHERE id = :fenceTypeId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("fenceTypeId", $fenceType->getId());
        $statement->bindValue("description", $fenceType->getDescription());
        $statement->execute();
        return $statement->rowCount();
    }

    public function getFenceTypeById($fenceTypeId)
    {
        $sql =<<<SQL
        SELECT id,
        description
        FROM FENCE_TYPES
        WHERE id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $fenceTypeId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\FenceType");
        return $result;
    }

    public function deleteFenceType($fenceTypeId)
    {
        $sql = <<<SQL
        DELETE FROM FENCE_TYPES WHERE id = :fenceTypeId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("fenceTypeId", $fenceTypeId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function insertOrUpdateCustomer($customer)
    {
        $sql = <<<SQL
        INSERT INTO customers(id, first_name, last_name, street, city, state, postcode, email, phone, mobile, company_name)
        VALUES(:id, :firstName, :lastName, :street, :city, :state, :postcode, :email, :phone, :mobile, :companyName)
        ON DUPLICATE KEY UPDATE
        first_name = :firstName2,
        last_name = :lastName2,
        street = :street2,
        city = :city2,
        state = :state2,
        postcode = :postcode2,
        email = :email2,
        phone = :phone2,
        mobile = :mobile2,
        company_name = :companyName2
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("id", $customer->getId());
        $statement->bindValue("firstName", $customer->getFirstName());
        $statement->bindValue("lastName", $customer->getLastName());
        $statement->bindValue("street", $customer->getStreet());
        $statement->bindValue("city", $customer->getCity());
        $statement->bindValue("state", $customer->getState());
        $statement->bindValue("postcode", $customer->getPostcode());
        $statement->bindValue("email", $customer->getEmail());
        $statement->bindValue("phone", $customer->getPhone());
        $statement->bindValue("mobile", $customer->getMobile());
        $statement->bindValue("companyName", $customer->getCompanyName());
        
        $statement->bindValue("firstName2", $customer->getFirstName());
        $statement->bindValue("lastName2", $customer->getLastName());
        $statement->bindValue("street2", $customer->getStreet());
        $statement->bindValue("city2", $customer->getCity());
        $statement->bindValue("state2", $customer->getState());
        $statement->bindValue("postcode2", $customer->getPostcode());
        $statement->bindValue("email2", $customer->getEmail());
        $statement->bindValue("phone2", $customer->getPhone());
        $statement->bindValue("mobile2", $customer->getMobile());
        $statement->bindValue("companyName2", $customer->getCompanyName());
        
        $statement->execute();
        return $this->db->lastInsertId();
    }

    public function getCustomerById($customerId)
    {
        $sql = <<<SQL
        SELECT c.id,
        c.first_name AS firstName,
        c.last_name AS lastName,
        c.company_name AS companyName,
        c.street AS street,
        c.city AS city,
        c.state AS state,
        c.postcode AS postcode,
        c.email,
        c.phone,
        c.mobile
        FROM customers c
        WHERE c.id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $customerId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Customer");
        return $result;
    }

    public function searchCustomers($searchTerm)
    {
        $sql = <<<SQL
        SELECT c.id, c.customer_name, c.email, c.phone, c.mobile, c.address
        FROM customers c
        WHERE
        MATCH(customer_name)
        AGAINST(:searchTerm IN BOOLEAN MODE)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("searchTerm", $searchTerm."*");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertQuote($quote, $modifiedBy) {
        $sql = <<<SQL
        INSERT INTO quote(fence_type_id, customer_id, quote_uuid, last_modified_by, quote_version)
        VALUES(:fenceTypeId, :customerId, :quoteUUID, :modifiedBy, 1)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("fenceTypeId", $quote->getFenceTypeId());
        $statement->bindValue("customerId", $quote->getCustomerId());
        $statement->bindValue("quoteUUID", $quote->getQuoteUUID());
        $statement->bindValue("modifiedBy", $modifiedBy);
        $statement->execute();
        $newId = $this->db->lastInsertId();
        return $newId;
    }

    public function updateQuotePanelId($quoteId, $panelId) {
        $sql = <<<SQL
        UPDATE quote
        SET panel_id = :panelId
        WHERE id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("panelId", $panelId);
        $statement->bindValue("quoteId", $quoteId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function updateQuoteFinalised($quoteId, $isFinalised) {
        $sql = <<<SQL
        UPDATE quote
        SET finalised = :finalised
        WHERE id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("finalised", $isFinalised);
        $statement->bindValue("quoteId", $quoteId);
        $statement->execute();
        return $statement->rowCount();
    }


    public function updateQuoteCost($quoteId, $theCost) {
        $sql = <<<SQL
        UPDATE quote
        SET cost = :theCost
        WHERE id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("theCost", $theCost);
        $statement->bindValue("quoteId", $quoteId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function insertQuoteLine($quote, $modifiedBy)
    {
        $sql = <<<SQL
        INSERT INTO quote_line(quote_id, notes, cost, colour_id, item_type, length, number_of_panels, panel_id, style_id, sheets, item_id, height, quantity, modified_by)
        VALUES(:quoteId, :notes, :cost, :colourId, :itemType, :length, :numberOfPanels, :panelId, :styleId, :sheets, :itemId, :height, :quantity, :modifiedBy)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quote->getQuoteId());
        $statement->bindValue("notes", $quote->getNotes());
        $statement->bindValue("cost", $quote->getPrice()); // NOTE: getPrice(), not getCost()
        $statement->bindValue("colourId", $quote->getColourId());
        $statement->bindValue("itemType", $quote->getItemType());
        $statement->bindValue("length", $quote->getLength());
        $statement->bindValue("numberOfPanels", $quote->getNumberOfPanels());
        $statement->bindValue("panelId", $quote->getPanelId());
        $statement->bindValue("styleId", $quote->getStyleId());
        $statement->bindValue("sheets", $quote->getSheets());
        $statement->bindValue("itemId", $quote->getItemId());
        $statement->bindValue("height", $quote->getHeight());
        $statement->bindValue("quantity", $quote->getQuantity());
        $statement->bindValue("modifiedBy", $modifiedBy);
//        $statement->bindValue("showInfo", $quote->getShowInfo());
        $statement->execute();
        $newId = $this->db->lastInsertId();
        return $newId;
    }

    public function editQuoteLine($quote, $modifiedBy)
    {
        $sql = <<<SQL
        UPDATE quote_line
        SET
        notes = :notes,
        cost = :cost,
        colour_id = :colourId,
        item_type = :itemType,
        length = :length,
        number_of_panels = :numberOfPanels,
        panel_id = :panelId,
        style_id = :styleId,
        sheets = :sheets,
        item_id = :itemId,
        height = :height,
        quantity = :quantity,
        modified_by = :modifiedBy,
        showInfo = :showInfo
        where id = :quoteLineId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("notes", $quote->getNotes());
        $statement->bindValue("cost", $quote->getPrice()); // NOTE: getPrice(), not getCost()
        $statement->bindValue("colourId", $quote->getColourId());
        $statement->bindValue("itemType", $quote->getItemType());
        $statement->bindValue("length", $quote->getLength());
        $statement->bindValue("numberOfPanels", $quote->getNumberOfPanels());
        $statement->bindValue("panelId", $quote->getPanelId());
        $statement->bindValue("styleId", $quote->getStyleId());
        $statement->bindValue("sheets", $quote->getSheets());
        $statement->bindValue("itemId", $quote->getItemId());
        $statement->bindValue("height", $quote->getHeight());
        $statement->bindValue("quantity", $quote->getQuantity());
        $statement->bindValue("quoteLineId", $quote->getId());
        $statement->bindValue("modifiedBy", $modifiedBy);
        $statement->bindValue("showInfo", $quote->getShowInfo());
        return $statement->execute();
    }

    public function getQuoteLinesByQuoteId($quoteId) {
        $sql = <<<SQL
          SELECT id, quote_id AS quoteId, cost, notes,
          colour_id AS colourId, item_type AS itemType,
          item_id AS itemId,
          length, number_of_panels AS numberOfPanels,
          panel_id AS panelId,
          style_id AS styleId,
          sheets, quantity, height, showInfo
          FROM quote_line WHERE quote_id = :quoteId
          AND is_deleted = 0
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quoteId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, "\\quotemaker\\domain\\QuoteLine");
    }

    public function deleteQuote($quoteId)
    {
        $sql = <<<SQL
        DELETE FROM quote WHERE id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("quoteId", $quoteId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function deleteQuoteLine($quoteLineId, $modifiedBy)
    {
        $sql = <<<SQL
        UPDATE quote_line SET is_deleted = 1, modified_by = :modifiedBy WHERE id = :quoteLineId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("modifiedBy", $modifiedBy);
        $statement->bindParam("quoteLineId", $quoteLineId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function getQuoteLineById($id) {
        $sql = <<<SQL
          SELECT id, quote_id AS quoteId, cost, notes,
          colour_id AS colourId, item_type AS itemType,
          length, number_of_panels AS numberOfPanels,
          panel_id AS panelId, item_id AS itemId,
          style_id AS styleId, quantity,
          sheets, height, showInfo
          FROM quote_line WHERE id = :quoteLineId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("quoteLineId", $id);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\QuoteLine");
        return $result;
    }

    public function getQuotesForCustomer($customerId)
    {
        $sql = <<<SQL
        SELECT id, cost AS totalCost, created_date
        FROM quote
        WHERE customer_id = :customerId
        ORDER BY id DESC
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("customerId", $customerId);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, "\\quotemaker\\domain\\Quote");
        return $result;
    }

    public function getQuoteById($quoteId) {
        $sql = <<<SQL
        SELECT quote.id, 
        quote.fence_type_id AS fenceTypeId, 
        quote.customer_id AS customerId, 
        quote.created_date AS quoteDate, 
        quote.price_adjustment AS priceAdjustment, 
        quote.quote_version AS quoteVersion,
        quote.myob_quote_uid AS myobQuoteUID, 
        h.myob_invoice_number AS myobInvoiceNumber, 
        h.myob_row_version AS myobRowVersion
        FROM quote 
        LEFT OUTER JOIN quote_history h ON (h.quote_id = quote.id AND h.quote_version = quote.quote_version) 
        WHERE quote.id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quoteId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Quote");
        return $result;
    }

    // TODO: BROKEN
    public function getQuoteByQuoteNumber($quoteNumber) {
        $sql = "SELECT id, fence_type_id AS fenceTypeId, customer_id AS customerId, created_date AS quoteDate, price_adjustment AS priceAdjustment, quote_version as quoteVersion, MYOB_QUOTE_UID as myobQuoteUid, MYOB_QUOTE_NUMBER as myobQuoteNumber, MYOB_ROW_VERSION as myobRowVersion FROM quote WHERE MYOB_QUOTE_NUMBER = :quoteNumber";
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteNumber", $quoteNumber);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Quote");
        return $result;
    }    
    
    // NOTES
    public function getAllNotes()
    {
        $sql =<<<SQL
        SELECT id, description
        FROM notes
        WHERE 1 = 1
        ORDER BY description
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNoteById($noteId)
    {
        $sql =<<<SQL
        SELECT id,
        description
        FROM notes
        WHERE id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $noteId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Note");
        return $result;
    }

    public function addNote($note)
    {
        $sql = <<<SQL
        INSERT INTO notes(description)
        VALUES(:description)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("description", $note->getDescription());
        $statement->execute();
        return $statement->rowCount();
    }

    public function deleteNote($noteId)
    {
        $sql = <<<SQL
        DELETE FROM notes WHERE id = :noteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("noteId", $noteId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function updateNote($note)
    {
        $sql = <<<SQL
        UPDATE notes
        SET description = :description
        WHERE id = :noteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("noteId", $note->getId());
        $statement->bindValue("description", $note->getDescription());
        $statement->execute();
        return $statement->rowCount();
    }

    // ITEMS
    public function getAllItems()
    {
        $sql =<<<SQL
        SELECT id, description, unit_cost AS unitCost, quote_wording AS quoteWording
        FROM items
        WHERE 1 = 1
        ORDER BY description
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, "\\quotemaker\\domain\\Item");
        return $result;
    }

    public function getItemById($itemId)
    {
        $sql =<<<SQL
        SELECT id,
        description,
        quote_wording AS quoteWording,
        instructions AS instructions,
        unit_cost AS unitCost,
        footer_text AS footerText,
        type
        FROM items
        WHERE id = :id
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("id", $itemId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Item");
        return $result;
    }

    public function getItemByDescription($description)
    {
        $sql =<<<SQL
        SELECT id,
        description,
        quote_wording AS quoteWording,
        instructions AS instructions,
        unit_cost AS unitCost,
        footer_text AS footerText,
        type
        FROM items
        WHERE description = :description
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("description", $description);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Item");
        return $result;
    }
    
    
    public function updateItem($item)
    {
        $sql = <<<SQL
        UPDATE items
        SET description = :description,
        unit_cost = :unitCost,
        quote_wording = :quoteWording,
        instructions = :instructions,
        footer_text = :footerText,
        type = :type
        WHERE id = :itemId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("itemId", $item->getId());
        $statement->bindValue("description", $item->getDescription());
        $statement->bindValue("unitCost", $item->getUnitCost());
        $statement->bindValue("quoteWording", $item->getQuoteWording());
        $statement->bindValue("instructions", $item->getInstructions());
        $statement->bindValue("footerText", $item->getFooterText());
        $statement->bindValue("type", $item->getType());
        $statement->execute();
        return $statement->rowCount();
    }

    public function deleteItem($itemId)
    {
        $sql = <<<SQL
        DELETE FROM items WHERE id = :itemId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("itemId", $itemId);
        $statement->execute();
        return $statement->rowCount();
    }

    public function addItem($item)
    {
        $sql = <<<SQL
        INSERT INTO items(description, unit_cost, quote_wording, instructions, footer_text)
        VALUES(:description, :unitCost, :quoteWording, :instructions, :footerText)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("description", $item->getDescription());
        $statement->bindValue("unitCost", $item->getUnitCost());
        $statement->bindValue("quoteWording", $item->getQuoteWording());
        $statement->bindValue("instructions", $item->getInstructions());
        $statement->bindValue("footerText", $item->getFooterText());
        $statement->execute();
        return $statement->rowCount();
    }

    function getRecentQuotes()
    {
        $sql = <<<SQL
        SELECT id, cost AS totalCost, created_date
        FROM quote
        ORDER BY id DESC
        limit 5
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, "\\quotemaker\\domain\\Quote");
    }

    function adjustQuote($id, $percentage, $modifiedBy)
    {
        $sql = <<<SQL
        UPDATE quote
        SET price_adjustment = :percentage,
        last_modified_by = :modifiedBy
        WHERE id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $id);
        $statement->bindValue("percentage", $percentage);
        $statement->bindValue("modifiedBy", $modifiedBy);
        $statement->execute();
        return $statement->rowCount();
    }

    /**
     * @return GatePricing
     */
    function getGatePricing()
    {
        $sql =<<<SQL
        SELECT *
        FROM gate_pricing
        WHERE id = 1
SQL;
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\GatePricing");
        return $result;
    }

    /**
     * @param GatePricing $gatePricing
     * @return mixed
     */
    function updateGatePricing($gatePricing)
    {
        $sql =<<<SQL
        UPDATE gate_pricing
        SET rails = :rails,
        rhs35x65 = :rhs35,
        rhs65x65 = :rhs65,
        dLatch = :dLatch,
        hinges = :hinges,
        dropBolt = :dropBolt,
        coverStrip = :coverStrip,
        postCaps = :postCaps,
        labour = :labour,
        powderCoatGate = :powderCoatGate,
        powderCoatRHS65x65 = :powderCoatRHS65x65,
        powderCoatDLatch = :powderCoatDLatch,
        powderCoatHingesCost = :powderCoatHingesCost,
        powderCoatDropBolt = :powderCoatDropBolt,
        powderCoatCaps = :powderCoatCaps,
        sheetCost1200 = :sheetCost1200,
        sheetCost1500 = :sheetCost1500,
        sheetCost1800 = :sheetCost1800,
        sheetCost2100 = :sheetCost2100,
        installSingleCost = :installSingleCost,
        installDoubleCost = :installDoubleCost,
        profitSingleCost = :profitSingleCost,
        profitDoubleCost = :profitDoubleCost
        WHERE 1
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("rails", $gatePricing->getRails());
        $statement->bindValue("rhs35", $gatePricing->getRhs35x65());
        $statement->bindValue("rhs65", $gatePricing->getRhs65x65());
        $statement->bindValue("dLatch", $gatePricing->getDlatch());
        $statement->bindValue("hinges", $gatePricing->getHinges());
        $statement->bindValue("dropBolt", $gatePricing->getDropBolt());
        $statement->bindValue("coverStrip", $gatePricing->getCoverStrip());
        $statement->bindValue("postCaps", $gatePricing->getPostCaps());
        $statement->bindValue("labour", $gatePricing->getLabour());

        $statement->bindValue("powderCoatGate", $gatePricing->getPowderCoatGate());
        $statement->bindValue("powderCoatRHS65x65", $gatePricing->getPowderCoatRHS65x65());
        $statement->bindValue("powderCoatDLatch", $gatePricing->getPowderCoatDLatch());
        $statement->bindValue("powderCoatHingesCost", $gatePricing->getPowderCoatHingesCost());
        $statement->bindValue("powderCoatDropBolt", $gatePricing->getPowderCoatDropBolt());
        $statement->bindValue("powderCoatCaps", $gatePricing->getPowderCoatCaps());

        $statement->bindValue("sheetCost1200", $gatePricing->getSheetCost1200());
        $statement->bindValue("sheetCost1500", $gatePricing->getSheetCost1500());
        $statement->bindValue("sheetCost1800", $gatePricing->getSheetCost1800());
        $statement->bindValue("sheetCost2100", $gatePricing->getSheetCost2100());

        $statement->bindValue("installSingleCost", $gatePricing->getInstallSingleCost());
        $statement->bindValue("installDoubleCost", $gatePricing->getInstallDoubleCost());
        $statement->bindValue("profitSingleCost", $gatePricing->getProfitSingleCost());
        $statement->bindValue("profitDoubleCost", $gatePricing->getProfitDoubleCost());


        $statement->execute();
        return $statement->rowCount();
    }

    function saveQuoteVersion($quoteId, $modifiedBy)
    {
        $sql = <<<SQL
        INSERT INTO quote_history(quote_id, modified_by)
        VALUES(:quoteId, :modifiedBy)
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quoteId);
        $statement->bindValue("modifiedBy", $modifiedBy);
        $statement->execute();
        return $statement->rowCount();
    }

    function pdfCreated($quoteId, $quoteVersion)
    {
        $sql = <<<SQL
        UPDATE quote_history
        set pdf_created = 1
        WHERE quote_id = :quoteId
        AND quote_version = :quoteVersion
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quoteId);
        $statement->bindValue("quoteVersion", $quoteVersion);
        $statement->execute();
        return $statement->rowCount();
    }

    function emailSent($quoteId, $quoteVersion)
    {
        $sql = <<<SQL
        UPDATE quote_history
        set date_emailed = now()
        WHERE quote_id = :quoteId
        AND quote_version = :quoteVersion
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quoteId);
        $statement->bindValue("quoteVersion", $quoteVersion);
        $statement->execute();
        return $statement->rowCount();
    }

    function updateQuoteHistoryWithMyobQuoteNumber($quoteId, $quoteVersion, $myobInvoiceNumber, $myobRowVersion)
    {
        $sql = <<<SQL
        UPDATE quote_history
        set myob_invoice_number = :myobInvoiceNumber,
        myob_row_version = :myobRowVersion
        WHERE quote_id = :quoteId
        AND quote_version = :quoteVersion
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("quoteId", $quoteId);
        $statement->bindValue("quoteVersion", $quoteVersion);
        $statement->bindValue("myobInvoiceNumber", $myobInvoiceNumber);
        $statement->bindValue("myobRowVersion", $myobRowVersion);
        $statement->execute();
        return $statement->rowCount();
    }
        
    function quoteVersionHistory($quoteId)
    {
        $sql =<<<SQL
        SELECT quote_id, quote_version, 
        date_modified, date_emailed, 
        modified_by, pdf_created,
        myob_invoice_number
        FROM  quote_history
        WHERE quote_id = :quoteId
        AND myob_invoice_number is not null
        ORDER BY date_modified DESC LIMIT 10
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("quoteId", $quoteId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getMyobInvoiceNumberForQuote($quoteId, $quoteVersion)
    {
        $sql =<<<SQL
        SELECT 
        myob_invoice_number AS myobInvoiceNumber
        FROM  quote_history
        WHERE quote_id = :quoteId
        AND quote_version = :quoteVersion
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("quoteId", $quoteId);
        $statement->bindParam("quoteVersion", $quoteVersion);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    function getQuoteIdByInvoiceNumber($invoiceNumber)
    {
        $sql =<<<SQL
        SELECT 
        quote_id
        FROM  quote_history
        WHERE myob_invoice_number = :invoiceNumber
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("invoiceNumber", $invoiceNumber);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if ($result) {
            return $result->quote_id;
        } else {
            return 0;
        }        
    }

    function getDefaultPanelByFenceType($fenceTypeId)
    {
        $sql =<<<SQL
        SELECT id,
        description,
        height,
        width,
        fence_type AS fenceType,
        post_length AS postLength,
        installation,
        price
        FROM panels
        WHERE fence_type = :fenceTypeId
        AND is_default = 1
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("fenceTypeId", $fenceTypeId);
        $statement->execute();
        $result = $statement->fetchObject("\\quotemaker\\domain\\Panel");
        return $result;
    }

    function updateApplicationSetting($settingName, $settingValue)
    {
        $sql = <<<SQL
        INSERT INTO application_settings(setting_name, setting_value)
        VALUES(:settingName, :settingValue)
        ON DUPLICATE KEY UPDATE
        setting_name = :settingName2,
        setting_value = :settingValue2
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("settingName", $settingName);
        $statement->bindValue("settingValue", $settingValue);
        $statement->bindValue("settingName2", $settingName);
        $statement->bindValue("settingValue2", $settingValue);
        $statement->execute();
        return $statement->rowCount();
    }

    function updateApplicationSettingJSON($settingName, $settingValue) 
    {
        $jsonString = json_encode($settingValue);
        $sql = <<<SQL
        INSERT INTO application_settings(setting_name, json_value)
        VALUES(:settingName, :settingValue)
        ON DUPLICATE KEY UPDATE
        setting_name = :settingName2,
        json_value = :settingValue2
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("settingName", $settingName);
        $statement->bindValue("settingValue", $jsonString);
        $statement->bindValue("settingName2", $settingName);
        $statement->bindValue("settingValue2", $jsonString);
        $statement->execute();
        return $statement->rowCount();        
    }

    function getApplicationSettingByName($settingName)
    {
        $sql =<<<SQL
        SELECT setting_value
        FROM application_settings
        WHERE setting_name = :settingName
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("settingName", $settingName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if ($result) {
            return $result->setting_value;
        } else {
            return "";
        }
    }

    function getApplicationSettingJSONByName($settingName)
    {
        $sql =<<<SQL
        SELECT json_value
        FROM application_settings
        WHERE setting_name = :settingName
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("settingName", $settingName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return json_decode($result->json_value);
    }
    
    function deleteApplicationSettingByName($settingName)
    {
        $sql = <<<SQL
        DELETE FROM application_settings 
        WHERE setting_name = :settingName
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindParam("settingName", $settingName);
        $statement->execute();
        return $statement->rowCount();        
    }
    
    public function updateQuoteWithMyobData($quoteId, $myobId)
    {
        $sql = <<<SQL
        UPDATE quote
        SET myob_quote_uid = :myobId
        WHERE id = :quoteId
SQL;
        $statement = $this->db->prepare($sql);
        $statement->bindValue("myobId", $myobId);
        $statement->bindValue("quoteId", $quoteId);
        $statement->execute();
        return $statement->rowCount();
    }
}
