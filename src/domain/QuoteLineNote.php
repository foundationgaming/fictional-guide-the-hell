<?php

namespace quotemaker\domain;


class QuoteLineNote extends QuoteLine
{

    public function __construct() {}

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if ($this->getCost() == 0 || $this->getCost() == NULL) {
            return "<b>NOTES:</b> {$this->notes}";
        } else {
            return $this->notes;
        }
    }

}