<?php

/**
 * Donation interface for the iGivefirst Donation API
 * Allows the lookup and creation of Donations
 */
class Donation {
	private $api;
	
	public function __construct($api) {
		$this->api = $api;
	}
	
	/**
	 * Create a donation with the given information
	 * 
	 * @param DonationInfo $donation Object containing the donation information
	 *
	 * @throws iGivefirst_DonationInformationIncomplete if the donation information was not complete enough
	 * @throws iGivefirst_DonationNotCreated if the parameters were not valid
	 * 
	 * @returns Array a hash containing the guid of the newly created donation
	 */
	public function create(DonationInfo $donation) {
		if (!$donation->validate()) {
			throw new iGivefirst_DonationInformationIncomplete();
		}
		
		$req = $this->api->client->post('/donation');
		$req->setBody(json_encode($donation), 'application/json');
		
		try {
			return $this->api->execute($req)->json();
		}
		catch(iGivefirst_HttpError $e) {
			throw new iGivefirst_DonationNotCreated($e);
		}
	}
	 
	/**
	 * Get a donation by guid
	 * 
	 * @params string $guid guid of the donation to look up
	 * 
	 * @throws iGivefirst_HttpError if an error occured
	 * 
	 * @returns Array a hash containing all the donation information or null
	 */
	public function get($guid) {
		$req = $this->api->client->get('/donation/' . $guid);

		try {
			return $this->api->execute($req)->json();
		}
		catch(iGivefirst_ObjectNotFound $e) {
			return null;
		}
		catch(iGivefirst_HttpError $e) {
			throw $e;
		}
	}
}
?>
