<?php

namespace quotemaker\domain;


class QuoteLineQuantity extends QuoteLine
{
    public function getPrice()
    {
        $theItem = $this->service->getItemById($this->getItemId());
        return $theItem->getUnitCost() * $this->quantity;
    }

    public function getFormattedDetail($lineBreak = "<br>")
    {
        $theItem = $this->service->getItemById($this->getItemId());
        $descriptionText = str_replace("{quantity}", $this->quantity, $theItem->getQuoteWording());
        $descriptionText = str_replace("{panel}", $this->getSingularOrPlural("panel", "panels"), $descriptionText);
        $descriptionText = str_replace("{hole}", $this->getSingularOrPlural("hole", "holes"), $descriptionText);
        $descriptionText = str_replace("{has}", $this->getSingularOrPlural("has", "have"), $descriptionText);
        $descriptionText = str_replace("{sleeper}", $this->getSingularOrPlural("sleeper", "sleepers"), $descriptionText);
        return $descriptionText;
    }

}
