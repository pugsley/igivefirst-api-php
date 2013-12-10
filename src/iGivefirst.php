<?php
use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequest;

require_once 'iGivefirst/Exceptions.php';
require_once 'iGivefirst/Common.php';
require_once 'iGivefirst/Donor.php';
require_once 'iGivefirst/Account.php';
require_once 'iGivefirst/Donation.php';

/**
 * Main interface for the iGivefirst Donation API
 */
class iGivefirst {

	// Donor interface
	public $donor;
	// Account interface
	public $account;
	// Donation interface
	public $donation;
	
	public $client;
	private $apikey, $apisecret;

	private $url_root = 'https://api.igivefirst.com';
	private $url_root_sandbox = 'https://api.igivefirst.mobi';
	
	/**
	 * Construct the interface class
	 * @param string $apikey Publisher API key
	 * @param string $apisecret Publisher API secret
	 * @param boolean $sandbox Indicates if the sandbox should be used. Defaults to true
	 */
	public function __construct($apikey, $apisecret, $sandbox=true) {
		$this->apikey = $apikey;
		$this->apisecret = $apisecret;
		
		$this->client = new Client($sandbox?$this->url_root_sandbox:$this->url_root);
		$this->client->setUserAgent('iGivefirst-SDK-PHP/1.0.0');
		
		$this->client->getEventDispatcher()->addListener('request.before_send', function (Event $event) {
			$this->signRequest($event['request']);
		}, -1);
		
		$this->donor = new Donor($this);
		$this->account = new Account($this);
		$this->donation = new Donation($this);
	}
	
	public function execute($request) {
		try {
			return $request->send();
		}
		catch(Guzzle\Http\Exception\CurlException $e) {
			throw new iGivefirst_HttpError($e->getError());
		}
		catch(Guzzle\Http\Exception\ClientErrorResponseException $e) {
			$r = $e->getResponse();

			switch($r->getStatusCode()) {
				case 401:
					throw new iGivefirst_AuthenticationError();
					break;
				case 404:
					throw new iGivefirst_ObjectNotFound($r);
					break;
				case 405:
					throw new iGivefirst_ObjectAlreadyExists($r);
					break;
				default:
					throw new iGivefirst_HttpError($r->getMessage());
			}
		}
		catch(Guzzle\Http\Exception\ServerErrorResponseException $e) {
			$r = $e->getResponse();
			throw new iGivefirst_HttpError($r->getStatusCode());
		}
	}
	
	private function signRequest($request) {
		$date = gmdate("D, d M Y H:i:s O", time());
		$method = $request->getMethod();
		$body = '';
		$content_type = $request->getHeader('Content-Type');
		$u = $request->getUrl(true)->normalizePath()->getPath();
		
		if ($request instanceof EntityEnclosingRequest) {
			$request_body = $request->getBody();
			if ($request_body)
				$body = $request_body->getContentMd5();
		}

		$hmac = base64_encode(hash_hmac('sha1', "$method\n$body\n$content_type\n$date\n$u", $this->apisecret, true));
		$auth = "{$this->apikey}:$hmac";
		
		$request->setHeader('Date', $date);
		$request->setHeader('Authorization', 'IGF_HMAC_SHA1 ' . $auth);
	}
}
?>
