<?php

/**
 * Holds information about a donation
 */
class DonationInfo {
	public $dateCreated;
	public $amount;
	public $sponsorMatchingPercentage;
	public $nonProfitCampaignGuid;
	public $publisherCampaignGuid;
	public $sponsorCampaignGuid;
	public $publisherGuid;
	public $donorAccountGuid;
	public $donorGuid;
	public $publisherTransactionId;
	
	/**
	 * Construct a DonationInfo object with a given set of properties
	 * Required properties are amount, nonProfitCampaignGuid, publisherCampaignGuid, donorAccountGuid, donorGuid
	 * 
	 * @params Array $properties optionally takes a hash of donor information
	 */
	public function __construct($properties=array()) {
		$this->dateCreated = gmdate('Y-m-d\\TG:i:s\\Z', time());
		$this->bind($properties);
	}
	
	/**
	 * Set donation information from amount and guids
	 *
	 * @params string amount in dollars "40.00"
	 * @params string nonProfitCampaignGuid guid of the nonprofit campaign chosen
	 * @params string publisherCampaignGuid guid of the publisher campaign associated with donation
	 * @params string donorAccountGuid guid of the donor account
	 * @params string donorGuid guid of the donor itself
	 */
	public function setDonation($amount, $nonProfitCampaignGuid, $publisherCampaignGuid, $donorAccountGuid, $donorGuid) {
		$this->amount = $amount;
		$this->nonProfitCampaignGuid = $nonProfitCampaignGuid;
		$this->publisherCampaignGuid = $publisherCampaignGuid;
		$this->donorAccountGuid = $donorAccountGuid;
		$this->donorGuid = $donorGuid;
	}
	
	/**
	 * Merge in properties from a given array
	 * 
	 * @params Array $properties array to merge properties in from
	 */
	public function bind($properties=array()) {
		foreach ($properties as $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function validate() {
		return !empty($this->amount) &&
			   !empty($this->nonProfitCampaignGuid) &&
			   !empty($this->publisherCampaignGuid) &&
			   !empty($this->donorAccountGuid) &&
			   !empty($this->donorGuid);
	}
}

/**
 * Holds information about a donor
 */
class DonorInfo {
	// REQUIRED: the Donor's username
	public $username;
	// REQUIRED: the Donor's screen name (can be the same as username)
	public $screenName;
	public $sharePersonalInfo;
	public $firstName;
	public $lastName;
	public $billingAddress1;
	public $billingAddress2;
	public $city;
	public $state;
	public $zip;
	public $country;
	public $cellPhoneNumber;
	public $homePhoneNumber;
	public $workPhoneNumber;
	
	/**
	 * Construct a DonorInfo object with a given set of properties
	 * Required properties are username, screenName
	 * 
	 * @params Array $properties optionally takes a hash of donor information
	 */
	public function __construct($properties=array()) {
		$this->bind($properties);
	}
	
	/**
	 * Sets the user information for this DonorInfo object
	 * 
	 * @params string $username the username for this Donor
	 * @params string $screenname The screen name for this Donor, can be the username
	 */
	public function setUser($username, $screenname=null) {
		if ($screenname == null) $screenname = $username;
		
		$this->username = $username;
		$this->screenName = $screenname;
	}
	
	/**
	 * Merge in properties from a given array
	 * 
	 * @params Array $properties array to merge properties in from
	 */
	public function bind($properties=array()) {
		foreach ($properties as $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function validate() {
		return !empty($this->screenName) &&
			   !empty($this->username);
	}
}

/**
 * Holds Account contact information for POST and PUT calls
 */
class AccountContactInfo { 
	public $billingAddress1;
	public $billingAddress2;
	public $billingCity;
	public $billingState;
	public $billingZip;
	public $billingCountry;
	
	/**
	 * Merge in properties from a given array
	 * 
	 * @params Array $properties array to merge properties in from
	 */
	public function bind($properties=array()) {
		foreach ($properties as $key => $value) {
			$this->$key = $value;
		}
	}
}

/**
 * Holds Account information for POST and PUT calls
 */
class AccountInfo {
	public $donorGuid;
	public $paymentMethod;
	public $creditCardNumber;
	public $cwCode;
	public $expirationMonth;
	public $expirationYear;
	public $accountNumber;
	public $routingNumber;
	public $accountHolderName;
	// AccountContactInfo object
	public $contactInfo;
	
	/**
	 * Construct an AccountInfo object with a given set of properties
	 * Required properties are donorGuid, paymentMethod, and either creditCardNumber or accountNumber
	 * 
	 * @params Array $properties optionally takes a hash of account and contact information
	 */
	public function __construct($properties=array()) {
		$this->contactInfo = new AccountContactInfo();
		$this->bind($properties);
		$this->contactInfo->bind($properties);
	}
	
	/**
	 * Sets the donor information for this AccountInfo and any contactInfo supplied
	 * 
	 * @params string $donorGuid guid of an existing donor
	 * @params Array $contactInfo billing information for this account
	 */
	public function setDonorInformation($donorGuid, $contactInfo=array()) {
		$this->donorGuid = $donorGuid;
		$this->contactInfo->bind($contactInfo);
	}
	
	/**
	 * Set this AccountInfo object to point to a credit card
	 * 
	 * @params string $ccnum credit card number
	 * @params string $cwcode CW code
	 * @params string $expirationMonth expiration month
	 * @params string $expirationYear expiration year
	 */
	public function setCreditCard($ccnum, $cwcode, $expirationMonth, $expirationYear) {
		$this->paymentMethod = 'creditCard';
		$this->creditCardNumber = $ccnum;
		$this->cwCode = $cwcode;
		$this->expirationMonth = $expirationMonth;
		$this->expirationYear = $expirationYear;
	}
	
	/**
	 * Set this AccountInfo object to point to ACH
	 * 
	 * @params string $account account number
	 * @params string $routing routing number
	 * @params string $accountName account holder name
	 */
	public function setACH($account, $routing, $accountName) {
		$this->paymentMethod = 'ach';
		$this->accountNumber = $account;
		$this->routingNumber = $routing;
		$this->accountHolderName = $accountName;
	}
	
	/**
	 * Merge in properties from a given array
	 * 
	 * @params Array $properties array to merge properties in from
	 */
	public function bind($properties=array()) {
		foreach ($properties as $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function validate() {
		return !empty($this->donorGuid) &&
			   !empty($this->paymentMethod) &&
			   !empty($this->contactInfo->billingAddress1) &&
			   !empty($this->contactInfo->billingState);
	}
}
?>
