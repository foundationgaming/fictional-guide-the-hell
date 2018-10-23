<?php
namespace quotemaker\domain;

class Customer
{
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $companyName;
    protected $customerName;
    protected $street;
    protected $city;
    protected $state;
    protected $postcode;
    protected $email;
    protected $phone;
    protected $mobile;   
    protected $rowVersion;  

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCustomerName() {
        return $this->customerName;
    }

    public function setCustomerName($value) {
        $this->customerName = $value;
    }

    public function getEmail()
    {
      return $this->email;
    }

    public function setEmail($value)
    {
      $this->email = $value;
    }

    public function getPhone()
    {
      return $this->phone;
    }

    public function setPhone($value)
    {
      $this->phone = $value;
    }

    public function getMobile()
    {
      return $this->mobile;
    }

    public function setMobile($value)
    {
      $this->mobile = $value;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }       

    /**
     * @return mixed
     */
    public function getTaxCode()
    {
        return $this->taxCode;
    }

    /**
     * @return mixed
     */
    public function getFreightTaxCode()
    {
        return $this->freightTaxCode;
    }

    /**
     * @param mixed $taxCode
     */
    public function setTaxCode($taxCode)
    {
        $this->taxCode = $taxCode;
    }

    /**
     * @param mixed $freightTaxCode
     */
    public function setFreightTaxCode($freightTaxCode)
    {
        $this->freightTaxCode = $freightTaxCode;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @param mixed $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }    
    
    /**
     * @return mixed
     */
    public function getRowVersion()
    {
        return $this->rowVersion;
    }

    /**
     * @param mixed $rowVersion
     */
    public function setRowVersion($rowVersion)
    {
        $this->rowVersion = $rowVersion;
    }    

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     *
     * Returns the address formatted for HTML (essentially justs puts a <br> after each comma)
     *
     */
    public function getFormattedAddress($separator="<br>") 
    {
        return $this->street . $separator . $this->city . " " . $this->state . " " . $this->postcode; 
    }
    
    public function getFormattedName() 
    {
        if (empty($this->getCompanyName())) {
            return $this->getFirstName() . " " . $this->getLastName();
        } else {
            return $this->getCompanyName();
        }
    }
   
    public function isCompany()
    {
        return !empty($this->getCompanyName());
    }
    
    public function populateFromJson($json) 
    {
        $this->setId($json["UID"]);
        if ($json["IsIndividual"] == true) {
            $this->setFirstName($json["FirstName"]);
            $this->setLastName($json["LastName"]);
        } else {
            $this->setCompanyName($json["CompanyName"]);
        }
        $this->setStreet($json["Addresses"][0]["Street"]);
        $this->setCity($json["Addresses"][0]["City"]);
        $this->setState($json["Addresses"][0]["State"]);
        $this->setPostcode($json["Addresses"][0]["PostCode"]);
        $this->setEmail($json["Addresses"][0]["Email"]);
        $this->setPhone($json["Addresses"][0]["Phone1"]);
        $this->setMobile($json["Addresses"][0]["Phone2"]); 
        $this->setRowVersion($json["RowVersion"]);       
    }

}
