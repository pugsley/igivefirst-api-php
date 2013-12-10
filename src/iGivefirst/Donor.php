<?php

/**
 * Donor interface for the iGivefirst Donation API
 * Allows the lookup and creation of Donors
 */
class Donor {
	private $api;
	
	public function __construct($api) {
		$this->api = $api;
	}
	
	/**
	 * Create a donor with the given information
	 * 
	 * @param DonorInfo $donor Object containing the donor information
	 *
	 * @throws iGivefirst_DonorInformationIncomplete if the donor information was not complete enough
	 * @throws iGivefirst_DonorNotCreated if the parameters were not valid
	 * @throws iGivefirst_DonorAlreadyExists if the donor already exists
	 * 
	 * @returns Array a hash containing the guid of the newly created donor
	 */
	public function create(DonorInfo $donor) {
		if (!$donor->validate()) {
			throw new iGivefirst_DonorInformationIncomplete();
		}
		
		$req = $this->api->client->post('/donor');
		$req->setBody(json_encode($donor), 'application/json');
		
		try {
			return $this->api->execute($req)->json();
		}
		catch(iGivefirst_ObjectAlreadyExists $e) {
			throw new iGivefirst_DonorAlreadyExists($e);
		}
		catch(iGivefirst_HttpError $e) {
			throw new iGivefirst_DonorNotCreated($e);
		}
	}
	
	/**
	 * Look up a donor by email address
	 *
	 * @params string $email email address of the donor
	 * 
	 * @throws iGivefirst_HttpError if an error occured
	 * 
	 * @returns Array a hash containing all the donor information or null
	 */
	 public function lookup($email) {
		$req = $this->api->client->get('/find-donor?emailAddress=' . $email);

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
	 
	/**
	 * Get a donor by guid
	 * 
	 * @params string $guid guid of the donor to look up
	 * 
	 * @throws iGivefirst_HttpError if an error occured
	 * 
	 * @returns Array a hash containing all the donor information or null
	*/
	public function get($guid) {
		$req = $this->api->client->get('/donor/' . $guid);

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
