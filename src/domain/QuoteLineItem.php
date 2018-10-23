<?php

namespace quotemaker\domain;

class QuoteLineItem
{
    protected $id;
    protected $quoteLineId;
    protected $cost;
    protected $notes;
    protected $itemId;
    protected $colourId;

    public function __construct() {}

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
      return $this->cost;
    }

    public function setCost($value)
    {
      $this->cost = $value;
    }

    public function getItemId() {
        return $this->itemId;
    }

    public function setItemId($value) {
        $this->itemId = $value;
    }

    public function getColourId() {
        return $this->colourId;
    }

    public function setColourId($value) {
        $this->colourId = $value;
    }

    public function getNotes()
    {
      return $this->notes;
    }

    public function setNotes($value)
    {
      $this->notes = $value;
    }

}
