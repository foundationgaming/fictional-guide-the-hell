<?php
namespace quotemaker\myob;

class MyobOauth {

    private $apiKey = NULL;
    private $apiSecret = NULL;
    private $redirectUrl = NULL;
    private $service = NULL;
    private $localMode = false;

    function __construct($theService)
    {
        $this->service = $theService;
        $this->apiKey = getenv('myob_apiKey');
        $this->apiSecret = getenv('myob_apiSecret');
        $this->redirectUrl = getenv('myob_redirectUrl');
        $this->localMode = getenv('myob_local_mode') == NULL ?  false : true;
    }

	// public function to get an access token
	public function getAccessToken($access_code) {

		// build up the params
		$params = array(
					'client_id'				=>	$this->apiKey,
					'client_secret'			=>	$this->apiSecret,
					'scope'					=>	'CompanyFile',
					'code'					=>	$access_code,
					'redirect_uri'			=>	$this->redirectUrl,
					'grant_type'			=>	'authorization_code', // authorization_code -> gives you an access token
		); // end params array */

		$params = http_build_query($params); // will urlencode data
		return( $this->getToken($params) );

	}

	// public function to refresh an access token
	public function refreshAccessToken($api_key, $api_secret, $refresh_token) {
		$params = array(
						'client_id'				=>	$api_key,
						'client_secret'			=>	$api_secret,
						'refresh_token'			=>	$refresh_token,
						'grant_type'			=>	'refresh_token', // refresh_token -> refreshes your access token
			); // end params array */
		$params = http_build_query($params);		
		return( $this->getToken($params) );
	}

	// private function for token calls
	private function getToken($params) {
		$url = 'https://secure.myob.com/oauth2/v1/authorize';
		$session = curl_init($url);
	    curl_setopt ($session, CURLOPT_POST, true);
	    curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
	    curl_setopt($session, CURLOPT_HEADER, false);
	    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($session, CURLOPT_VERBOSE, false);
	    $response = curl_exec($session);
	    curl_close($session);
		$response_json = json_decode( $response );
		return($response_json);
	}

	// private function for CURL
	private function doPost($url, $params) {
	    $cftoken = base64_encode('Administrator:');
	    
	    $headers = array(
	        'Authorization: Bearer '.$this->getOrRefreshAccessToken(),
	        'x-myobapi-key: '.$this->apiKey,
	        'x-myobapi-version: v2',
	        'x-myobapi-cftoken: '.$cftoken
	    );
	    
			    
		$session = curl_init($url);
		curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 
	    curl_setopt($session, CURLOPT_POST, true);
	    curl_setopt($session, CURLOPT_POSTFIELDS, json_encode($params));
	    curl_setopt($session, CURLOPT_HEADER, true);
	    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($session, CURLOPT_VERBOSE, false);
	    $response = curl_exec($session);	    
	  	curl_close($session);
	  	return($response);
	}

	// private function for CURL
	private function doGet($url, $filter='') {

	    $cftoken = base64_encode('Administrator:');
	    
	    $headers = array(
			'Authorization: Bearer '.$this->getOrRefreshAccessToken(),
			'x-myobapi-key: '.$this->apiKey,
			'x-myobapi-version: v2',
	        'x-myobapi-cftoken: '.$cftoken
	    );

	    $session = curl_init();
	    // Compose an URL with the escaped string
	    if ($filter != '') {
	        $encodedFilter = curl_escape($session, $filter);
    	    $fullUrl = "$url?\$filter={$encodedFilter}";
    	    curl_setopt($session, CURLOPT_URL, $fullUrl);
	    } else {
	        curl_setopt($session, CURLOPT_URL, $url);
	    }
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($session, CURLOPT_POST, false);
	    curl_setopt($session, CURLOPT_HEADER, false);
	    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	    
	    $response = curl_exec($session);
// 	    $httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE);
// 	    if ($httpcode != 200) {	        
// 	    }	    
	    curl_close($session);
	    return($response);
	}

	public function getContact($uri) {
	    $response = $this->doGet($uri);
	    $response_json = json_decode( $response );
	    return $response_json;
	}
	
	public function getCustomerById($id) {
	    $url = $this->service->getMyobFileUri() . "/Contact/Customer/";
	    $filter = "UID eq guid'$id'";
	    $response = $this->doGet($url, $filter);
	    $response_json = json_decode( $response, true );	   	  	   
	    return $response_json;	    
	}

	public function getAllContacts() {
	    $url = $this->service->getMyobFileUri() . "/Contact/";
	    $filter = "Type eq 'Customer' and IsActive eq true and IsIndividual eq true";
	    $response = $this->doGet($url, $filter);
	    $response_json = json_decode( $response, true );
	    return $response_json;
	}
	
	public function searchCustomers($searchString)
	{
	    // if there's a space search first name and last name
	    // otherwise do either / or
	    $searchTerms = preg_split("/[\s,]+/", $searchString);
	    $url = $this->service->getMyobFileUri() . "/Contact/Customer/";
	    if (count($searchTerms) > 1) {
	       $filter = "(startswith(FirstName, '$searchTerms[0]') and startswith(LastName, '$searchTerms[1]') OR startswith(CompanyName, '$searchString'))";
	    } else {
	       $filter = "startswith(FirstName, '$searchTerms[0]') or startswith(LastName, '$searchTerms[0]') or startswith(CompanyName, '$searchString')";
	    }
	    $response = $this->doGet($url, $filter);
	    $response_json = json_decode( $response, true );
	    return $response_json;	    
	}

	public function getQuote($quoteId) {
	    $url = $this->service->getMyobFileUri() . "/Sale/Quote/Service/";
	    $filter = "UID eq guid'$quoteId'";
	    $response = $this->doGet($url, $filter);
	    $response_json = json_decode( $response );
	    return $response_json;
	}

	public function addQuote($quote) 
	{	    	    
	    $gstCodeUid = $this->getTaxCodeByCode("GST");
	    $accountUid = NULL;
	    
	    $theUrl = $this->service->getMyobFileUri() . "/Sale/Quote/Service";
	    $params = array();
	    $params["Customer"] = array("UID" => $quote->getCustomerId());
	    $params["Lines"] = array();

	    // is this an update or an insert?
	    if ($quote->getMyobQuoteUID() != NULL) {
	        // this will be an update.
	        // get the latest row version - we will overwrite it
	        $latest = $this->getQuote($quote->getMyobQuoteUID());
	        $params["UID"] = $quote->getMyobQuoteUID();
	        $params["RowVersion"] = $latest->Items[0]->RowVersion;
	    }
	    
	    $quoteLines = $quote->getQuoteLines();
	    $hasGates = false;
	    
	    // first pass - see which account this is going into
	    for ($i=0; $i < count($quoteLines); $i++) {
	        $line = $quoteLines[$i]; 
	        if ( $line->getItemType() == 'SG' || $line->getItemType() == 'DG' ) {
	            $hasGates = true;
	        }
	    }
	    
	    if ($hasGates) {
	        $accountUid = $this->getAccountByAccountNumber("3004");
	    } else {
	        $accountUid = $this->getAccountByAccountNumber("1200");
	    }	    
	    
	    // second pass - build up the lines 	    
	    for ($i=0; $i < count($quoteLines); $i++) {
	        $line = $quoteLines[$i]; 
	        $newLine = array();
	        $newLine["TaxCode"]["UID"] = $gstCodeUid;
	        $newLine["Description"] = $line->getFormattedDetail("\r\n");
	        $newLine["Account"]["UID"] = $accountUid;
	        if ($i == 0) {
	            $newLine["Total"] = $quote->getUnformattedCostIncGST();
	        } else {
	            $newLine["Total"] = "0.0";	            
            }
            $params["Lines"][] = $newLine;
	    }	   	    	    	   
       return $this->doPost($theUrl, $params, true);
	}

	public function getAccountByAccountNumber($acctNumber)
	{
	    $url = $this->service->getMyobFileUri() . "/GeneralLedger/Account";
	    $filter = "Number eq $acctNumber and Type eq 'Income'";
	    $response = $this->doGet($url, $filter);
	    $response_json = json_decode( $response );
	    $theUid = $response_json->Items[0]->UID;
	    return $theUid;
	}
		
	public function getTaxCodeByCode($code)
	{
	    $url = $this->service->getMyobFileUri() . "/GeneralLedger/TaxCode";
	    $filter = "Code eq '$code'";
	    $response = $this->doGet($url, $filter);
	    $response_json = json_decode( $response );
	    $theUid = $response_json->Items[0]->UID;
	    return $theUid;
	    //return $response_json;
	}
	
	public function getByUrl($theUrl) 
	{
	    return json_decode($this->doGet($theUrl));
	}
	
	public function getTaxCodes() {
	    $url = $this->service->getMyobFileUri() . "/GeneralLedger/TaxCode";	    
	    $response = $this->doGet($url);
	    $response_json = json_decode( $response, true );
	    return $response_json;
	}
	
	public function getCompanyFiles() {
	    $url = "https://api.myob.com/accountright/";
	    $response = $this->doGet($url);
	    $response_json = json_decode( $response );
	    return $response_json;
	}

	public function refreshOauthAccessToken($refreshToken) {
        return $this->refreshAccessToken($this->apiKey, $this->apiSecret, $refreshToken);
	}
	
	public function addCustomer($customer)
	{
	    $gstCodeUid = $this->getTaxCodeByCode("GST");
	    $theUrl = $this->service->getMyobFileUri() . "/Contact/Customer";
		$params = array();
		
		if ($customer->getId() != null) {
			// this is an update
			$params["UID"] = $customer->getId();
			$params["RowVersion"] = $customer->getRowVersion();
		}
		
	    if ($customer->isCompany()) {
	        $params["CompanyName"] = $customer->getCompanyName();
	        $params["IsIndividual"] = false;
	    } else {
	        $params["LastName"] = $customer->getLastName();
	        $params["FirstName"] = $customer->getFirstName();
	        $params["IsIndividual"] = true;
	    }
	    
	    $params["Addresses"] = array();
	    $params["Addresses"][0] = array(
            "Street" => $customer->getStreet(),
			"City" => $customer->getCity(),
			"State" => $customer->getState(),
			"PostCode" => $customer->getPostcode(),
	        "Email" => $customer->getEmail(),
	        "Phone1" => $customer->getPhone(),
	        "Phone2" => $customer->getMobile(),
	    );
	    
	    $params["SellingDetails"] = array(
	        "TaxCode" => array("UID" => $gstCodeUid), 
	        "FreightTaxCode" => array("UID" => $gstCodeUid)	        
	    );
	    
	    $result = $this->doPost($theUrl, $params, true);
	    return $result;
	}	

	public function getOrRefreshAccessToken() 
	{
	    if ($this->localMode) {
	        return "LOCAL_MODE";
	    }
	    
		$tokens = $this->service->getOauthTokens();		
	    $accessToken = $tokens->accessToken;
	    $refreshToken = $tokens->refreshToken;
	    $tokenExpired = (time() > $tokens->expiresIn) ? true : false;
	    
	    if ($tokenExpired) {
	        $oauthTokens = $this->refreshOauthAccessToken($refreshToken);
	        $this->service->updateOauthTokens($oauthTokens->access_token, $oauthTokens->refresh_token, time() + $oauthTokens->expires_in);
	        $accessToken = $oauthTokens->access_token;
	    }
	    
	    return $accessToken;	    
	}
	
    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }	
    
    public function updateQuoteData($returnVal, $quote)
    {
        $data = explode("\n",$returnVal);
        $theUrl = NULL;
        foreach($data as $part){
            if (substr($part, 0, 9) == "Location:") {
                $theUrl = trim(substr($part, 10));
                break;
            }
        }
        // get the quote ID from the quote, by fetching the returned location
        $fetchedQuote = $this->getByUrl($theUrl);
        $this->service->updateQuoteWithMyobData($quote->getId(), $quote->getQuoteVersion(), $fetchedQuote);        
    }
} // end class
