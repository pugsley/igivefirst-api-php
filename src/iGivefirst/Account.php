<?php

/**
 * Account interface for the iGivefirst Donation API
 * Allows creating and updating of account objects on a Donor
 */
class Account {
	private $api;
	
	public function __construct($api) {
		$this->api = $api;
	}
	
	/**
	 * Create an account with the given information
	 * 
	 * @params AccountInfo $account Object containing the account information
	 *
	 * @throws iGivefirst_AccountInformationIncomplete if the account information was not complete enough
	 * @throws iGivefirst_AccountNotCreated if the parameters were not valid
	 * 
	 * @returns Array a hash containing the guid of the newly created account
	 */
	public function create(AccountInfo $account) {
		if (!$account->validate()) {
			throw new iGivefirst_AccountInformationIncomplete();
		}
		
		$req = $this->api->client->post('/account');
		$req->setBody(json_encode($account), 'application/json');
		
		try {
			return $this->api->execute($req)->json();
		}
		catch(iGivefirst_HttpError $e) {
			throw new iGivefirst_AccountNotCreated($e);
		}
	}
	
	/**
	 * Update an existing account with the given information. You must provide all account information.
	 * 
	 * @params string $accountid guid of the account to update
	 * @params AccountInfo $account Object containing the account information
	 *
	 * @throws iGivefirst_AccountInformationIncomplete if the account information was not complete enough
	 * @throws iGivefirst_AccountNotUpdated if the parameters were not valid
	 * 
	 * @returns void
	 */
	public function update($accountid, AccountInfo $account) {
		if (!$account->validate()) {
			throw new iGivefirst_AccountInformationIncomplete();
		}
		
		$req = $this->api->client->put('/account/' . $accountid);
		$req->setBody(json_encode($account), 'application/json');
		
		try {
			$this->api->execute($req);
		}
		catch(iGivefirst_HttpError $e) {
			throw new iGivefirst_AccountNotUpdated($e);
		}
	}
	
	/**
	 * Disable an eixsting account
	 * 
	 * @params string $accountid guid of the account to disable
	 * 
	 * @throws iGivefirst_AccountNotUpdated if the parameters were not valid
	 */
	public function disable($accountid) {
		$req = $this->api->client->delete('/account/' . $accountid);
		
		try {
			$this->api->execute($req);
		}
		catch(iGivefirst_HttpError $e) {
			throw new iGivefirst_AccountNotUpdated($e);
		}
	}
}

?>
