<?php

namespace quotemaker\forms;

use Symfony\Component\Form\DataTransformerInterface;

class DollarsAndCentsTransformer implements DataTransformerInterface
{

    public function __construct()
    {
    }
    
    // transform the price in cents into a formatted string (dollars and cents)    
    public function transform($priceInCents)
    {
        return number_format(($priceInCents / 100), 2, '.', '');        
    }
    
    // transform the string (dollars and cents) back to cents
    public function reverseTransform($priceAsString)
    {
        
        return $priceAsString * 100;
    }
}

