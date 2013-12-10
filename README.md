iGivefirst PHP SDK
==================

This project contains the PHP SDK for the iGivefirst Donation API. It is distributed as a Composer package for easy access.

## Sample Usage

```php
<?php

require 'vendor/autoload.php';

$nonprofit_campaign = '1e9a8366-1060-4b45-9874-95039ec217c6';
$publisher_campaign = 'b93babc1-9557-4271-a065-43b29f04c2fb';

$igf = new iGivefirst(API_KEY, API_SECRET);

// Create a new Donor
$donor = new DonorInfo();
$donor->setUser(time() . '@igivefirst.mobi');
$donor->bind(array('firstName'=>'John', 'lastName'=>'Doe', 'billingAddress1' => '123 Candy Lane', 'city' => 'Imaginary', 'state' => 'CA', 'country' => 'usa', 'zip' => '11111'));

$donor_data = $igf->donor->create($donor);

// Create a new Account for the Donor we just created
$account = new AccountInfo();
$account->setCreditCard('4111111111111111', '124', '02', '2023');
$account->setDonorInformation($donor_data['guid'], array('billingAddress1' => '123 Candy Lane', 'billingCity' => 'Imaginary', 'billingState' => 'CA', 'billingZip' => '11111'));

$account_data = $igf->account->create($account);

// Now create a donation. We've already picked out our nonprofit and publisher campaigns.
$donation = new DonationInfo();
$donation->setDonation('40.11', $nonprofit_campaign, $publisher_campaign, $account_data['guid'], $donor_data['guid']);

$donation_data = $igf->donation->create($donation);

// Finally, we can inspect the donation we just posted
$donation = $igf->donation->get($donation_data['guid']);
print_r($donation);

?>
```

# iGivefirst REST Donation API

**In order to use the iGivefirst Donation API, you must be PCI level 1 compliant**

## Getting Started

In order to use the iGivefirst Donation API, you will first need to register as a publisher on our website.

1. ***SDK prequesites*** - in order to use the PHP SDK you will need the cURL extension compiled with OpenSSL support
1. ***Register as a publisher*** - sign up at [https://www.igivefirst.mobi/join/publisher]()
1. ***Request access*** - send an email to support@igivefirst.com with your account information and we'll get you set up for access
1. ***Configure API keys*** - visit the API page [https://www.igivefirst.mobi/publisher/donation-api]() to get your API keys and configure whitelisted IP addresses.


## Guids

Our API utizes business keys in the form of standard 36 character guids.  These guids are created with-in our system and are unique.  The main guids that are utilized:

- Nonprofit Campaign Guid - This is the guid representation of a campaign setup by a nonprofit.  This guid is accessible through the ad serving api, and is used by virtually all api calls.
- Publisher Campaign Guid - This is the guid that is created when a publisher campaign is created.  Again this guid is used with virtually every call.
- Donor Guid - This is the unique id for a donor
- Donor Account Guid  - In order to call our API to process a donor donation a donor account guid is utilized.

## Secure Rest Donation API

### Obtaining a private key

Login as your publisher and click on the api link on the bottom left.  From there you will be able to access all of the 
above guids mentioned.  This also includes the two keys that are used to secure our system:

- Publisher API Key
- Publisher Secret Key

### Authenticating requests using Rest API

Authenticating Requests Using the REST API

When accessing iGivefirst API using REST , you must provide the following items in your request so the request can be authenticated:

#### Request Elements

* Publisher API Key - It is the access key id of the identity you are using to send your request.
* Signature — Each request must contain a valid request signature, or the request is rejected.  A request signature is calculated using your Publisher Secret Key, which is a shared secret known only to you and iGivefirst.
* Time stamp — Each request must contain the date and time the request was created, represented as a string in UTC. An example of the timestamp is: Thu, 18 Nov 2010 11:27:35 GMT.  This timestamp must match the
accompanying HTTP Date Header

##### Authorization
The iGivefirst REST API uses the standard HTTPAuthorization header to pass authentication information.  
The following is an example of the header:

* Authorization: IGF\_HMAC\_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg= 

The header consists of the Publisher API Key concatenated with the Signature. 

* Authorization type - IGF\_HMAC\_SHA1
* iGivefirst API Key - 59e680df-df0a-42c3-969a-800d35ca5684
* Signature - frJIUN8DYpKDtOLCwo//yllqDzg=

The Signature computed from Base64( HMAC-SHA1( UTF-8-Encoded( StringToSign ), PublisherSecret ) ) where

<pre>
StringToSign = 
	HTTPVerb + "\n" 
	MD5-HASH-OF-Content + "\n" +
	Content-Type + "\n" +
	Timestamp + "\n" + 
	CanonicalizedResourceURI
</pre>

An Example of the StringToSign

<pre>
POST\n
bc1153d10db6079ecfbe3c3dca023402\n
application/json\n
Thu, 15 Sep 2012 00:51:48 GMT\n
/donation
</pre>

or

<pre>
GET\n
\n
\n
Thu, 15 Sep 2012 00:51:48 GMT\n
/donation/5def4c5f-e318-471f-9ef7-05cc965233cd
</pre>

or 

<pre>
DELETE\n
\n
\n
Thu, 15 Sep 2012 00:51:48 GMT\n
/donation/5def4c5f-e318-471f-9ef7-05cc965233cd
</pre>

The StringToSign is then used to build the Signature by

1. UTF-8 encoding the StringToSign
2. Calculating the HMAC-SHA1 of the StringToSign using your secret key
3. Base64 encoding the resulting hash bytes, excluding any hex encoding of the hash

Once you have your Signature you create the Authorization header by contenating your Access Key and the Signature:

Authorization: IGF\_HMAC\_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=

#### White Listed IP Addresses
The production system only communicates to the IP addresses that you have listed during the setup of the API key.

### Donation API Endpoint
The donation api is used to create, get or delete a donation.  

#### Headers Used
* Content Type: application/json
* Accepts: application/json (where applicable)
* Date: Thu, 15 Sep 2012 00:51:48 GMT
* Authorization: IGF\_HMAC\_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=

#### HTTP Verbs

##### GET - Get a donation
* URI - /donation/:guid
* RETURNS - full json donation object which includes optional fields such as nonprofit name
* SUCCESS CODE - 200
* ERROR CODES 
	- 500 input error
	- 404 no donation found by that guid

Example

```
GET /donation/59e680df-df0a-42c3-969a-800d35ca5684
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Response

```json
{
  "guid"                      :   "59e680df-df0a-42c3-969a-800d35ca5684",
  "amount"                    :   42.80,
  "sponsorMatchingPercentage" :   20, // optional
  "nonProfitCampaignGuid"     :   "8aec529c-dc93-4e5a-a02d-2965f16d327e",
  "publisherCampaignGuid"     :   "59e680df-df0a-42c3-969a-800d35ca5684",
  "sponsorCampaignGuid"       :   "ca712410-8e11-46a5-872c-de8ba7744e42", // optional
  "publisherTransactionId"    :   "42",  // optional provided by publisher
  "donorGuid"                 :   "ca712410-8e11-46a5-872c-de8ba7744e42",
  "status"                    : "NON_PROFIT_PAID", // various status codes - PENDING_BATCH, PUBLISHER_BATCH_COMPLETED, PUBLISHER_PAYMENT_ERROR, NONPROFIT_PAID, NONPROFIT_PAYMENT_ERROR, ON_HOLD, CANCELLED, EXCEPTION
  "dateCreated"               :   "2013-02-14:14:23:00Z", // ISO 3602 timestamp YYYY-MM-DDThh:mmZ GMT
  "publiserName":        : "Publisher Name",
  "nonProfitName":       : "Hobbit's for Humanity",
  "sponsorName" :        : "Thorin & Co." // optional
}
```

##### POST - Create a donation for a donor

* URI: /donation
* BODY - donation object
* SUCCESS CODE - 201 created
* ERROR CODES
   - 500 internal error
   - 400 malformed post body
   - 401 unauthorized

Example

```
POST /donation
Accepts: application/json
Content-Type: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Body

```json
{
  "amount"                    :   42.80, // amount in USD
  "sponsorMatchingPercentage" :   20, // optional
  "nonProfitCampaignGuid"     :   "8aec529c-dc93-4e5a-a02d-2965f16d327e", 
  "publisherCampaignGuid"     :   "59e680df-df0a-42c3-969a-800d35ca5684",
  "sponsorCampaignGuid"       :   "ca712410-8e11-46a5-872c-de8ba7744e42", // optional
  "publisherTransactionId"    :   "42", // optional - supplied by the publisher
  "donorAccountGuid"          : "ca712410-8e11-46a5-872c-de8ba7744e432”
  "donorGuid"				  : "59e680df-df0a-42c3-969a-800d35ca5684"
}
```

Response Success

```json
{
 "guid"                    :   "8aec529c-dc93-4e5a-a02d-2965f16d327e"
}
```

Response Failure

```json
{
 "errors"                    :   {"errors" : "Error messages"} // human readable error messages
}
```

##### DELETE - Cancel a donation

***NOT YET IMPLEMENTED***

* URI: /donation/:guid-of-donation
* RETURNS - string that is the guid for the donation
* SUCCESS CODE - 201
* ERROR CODES
    - 500 internal error
    - 401 unauthorized

Example

```
DELETE /donation/59e680df-df0a-42c3-969a-800d35ca5684
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

##### PUT - not implemented
Cancel the donation with DELETE and then POST to create new donation

* ERROR CODES - 405 - method not allowed

### Donor API Endpoint
The donation api is used to find, create, get, or delete a donor.

#### Headers Used
* Content Type: application/json
* Accepts: application/json (where applicable)
* Date: Thu, 15 Sep 2012 00:51:48 GMT
* Authorization: IGF\_HMAC\_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=

#### HTTP Verbs

##### GET - Find a donor
* URI - /find-donor
* Param - email - the email address of the donor
* RETURNS - donor json object
* SUCCESS CODE - 200
* ERROR CODES - 404

Example

```
GET /find-donor?email=biblo@bagsend.com
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Response

```json
{
   "firstName"         : "Frodo",
   "lastName"          : "Baggins",
   "sharePersonalInfo" : false,
   "contactInfo"       : {
    "cellPhoneNumber"   : "3035511234",
    "workPhoneNumber"   : "234134435",
    "homePhoneNumber"   : "1213",
    "billingAddress1"   : "123 Shire Blvd",
    "billingAddress2"   : "Lower Hobbit Hole",
    "billingCity"       : "The Shire",
    "billingState"      : "CO",
    "billingZip"        : "80125",
    "billingCountry"    : "US"
   },
   "email"             : "bilbo.baggins@gmail.com",
   "accounts"          : [ { active: true, guid: 59e680df-df0a-42c3-969a-800d35ca5684, displayInfomation: "Visa ending in 4358" } ],
   "anonymous"         : false
}
```


##### GET - Get a donor by guid
* URI - /donor/{donor-guid}
* RETURNS - full json donor object.
* SUCCESS CODE - 200
* ERROR CODES - 404

Example

```
GET /donor/59e680df-df0a-42c3-969a-800d35ca5684
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Response

```json
{
   "firstName"         : "Frodo",
   "lastName"          : "Baggins",
   "sharePersonalInfo" : false,
   "contactInfo"       : {
    "cellPhoneNumber"   : "3035511234",
    "workPhoneNumber"   : "234134435",
    "homePhoneNumber"   : "1213",
    "billingAddress1"   : "123 Shire Blvd",
    "billingAddress2"   : "Lower Hobbit Hole",
    "billingCity"       : "The Shire",
    "billingState"      : "CO",
    "billingZip"        : "80125",
    "billingCountry"    : "US"
   },
   "email"             : "bilbo.baggins@gmail.com",
   "accounts"          : [ { active: true, guid: 59e680df-df0a-42c3-969a-800d35ca5684, displayInfomation: "Visa ending in 4358" } ],
   "anonymous"         : false
}
```

##### POST - Create a donor
* URI - /donor
* Payload - full json donor object.  Account creation is not allowed with-in the payload.
* SUCCESS CODE - 202
* ERROR CODES 
	- 500 input validation error
	- 405 user already registered by that email address
	
Example

```
POST /donor
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Body 

```json
{
   "firstName"         : "Frodo",
   "lastName"          : "Baggins",
   "billingAddress1"   : "123 Shire Blvd",
   "billingAddress2"   : "Lower Hobbit Hole",
   "city"              : "The Shire",
   "state"             : "CO",
   "zip"               : "80125",
   "country"           : "US",
   "cellPhoneNumber"   : "3035511234",
   "workPhoneNumber"   : "234134435",
   "homePhoneNumber"   : "1213",
   "username"          : "bilbo.baggins@gmail.com", *REQUIRED*
   "screenName"		   : "bilbo.baggins", *REQUIRED*
   "sharePersonalInfo" : true,
   "anonymous"         : false

}
```

Response

```json
{ "guid" : "8aec529c-dc93-4e5a-a02d-2965f16d327e" }
```

##### PUT - Update a donor

***NOT YET IMPLEMENTED***

* URI - /donor/{donor-guid}
* Payload - full json donor object.  Account updates are  not allowed with-in the payload.
* SUCCESS CODE - 200
* ERROR CODES 
	- 500 input validation error

Example

```
PUT /donor/8aec529c-dc93-4e5a-a02d-2965f16d327e
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Body 

```json
{
   "firstName"         : "Frodo",
   "lastName"          : "Baggins",
   "sharePersonalInfo" : false,
   "contactInfo"       : {
    "billingAddress1"   : "123 Shire Blvd",
    "billingAddress2"   : "Lower Hobbit Hole",
    "billingCity"       : "The Shire",
    "billingState"      : "CO",
    "billingZip"        : "80125",
    "billingCountry"    : "US",
    "cellPhoneNumber"   : "3035511234",
    "workPhoneNumber"   : "234134435",
    "homePhoneNumber"   : "1213",
   },
   "username"             : "bilbo.baggins@gmail.com",
   "sharePersonalInfo" : true,
   "anonymous"         : false
}
```

##### DELETE
Not supported

### Account API Endpoint
The account api is used to create or update donor accounts. The donor api above provides the only 'get' information for accounts. 

#### Headers Used
* Content Type: application/json
* Accepts: application/json (where applicable)
* Date: Thu, 15 Sep 2012 00:51:48 GMT
* Authorization: IGF\_HMAC\_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=

#### HTTP Verbs

##### GET
Not supported, accessible through Donor API

##### POST - Create a donor account
* URI - /account
* Payload - full json account object - cc information if a cc account, otherwise bank account information
* SUCCESS CODE - 202
* ERROR CODES
	- 500 input validation error

Example

```
POST /account
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Body 

```json
{
   "donorGuid"         : "59e680df-df0a-42c3-969a-800d35ca5684",
   "paymentMethod"     : "creditCard", // creditCard or ach allowed
   "creditCardNumber"  : "555555555555",
   "cwCode"            : 1234,
   "expirationMonth"   : "02",
   "expirationYear"    : 2023,
   "accountNumber"     : 4385589350,
   "routingNumber"     : 1234, 
   "accountHolderName" : "Biblo Baggins",
   "contactInfo"       : {
     "billingAddress1"   : "123 Shire Blvd",
     "billingAddress2"   : "Lower Hobbit Hole",
     "billingCity"       : "The Shire",
     "billingState"      : "CO",
     "billingZip"        : "80125",
     "billingCountry"    : "US"
   }
}
```

Response

```json
{ "guid" : "8aec529c-dc93-4e5a-a02d-2965f16d327e" }
```

##### PUT - Update an account
* URI - /account/{account-guid}
* Payload - full json account object
* SUCCESS CODE - 200
* ERROR CODES
	- 500 input validation error

Example

```
PUT /account/59e680df-df0a-42c3-969a-800d35ca5684
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Body 

```json
{
   "donorGuid"         :  "59e680df-df0a-42c3-969a-800d35ca5684",
   "paymentMethod"     : "cc", // cc or ach allowed
   "creditCardNumber"  : "555555555555",
   "cwCode"            : 1234,
   "expirationMonth"   : 02,
   "expirationYear"    : 2023,
   "accountNumber"     : 4385589350,
   "routingNumber"     : 1234, 
   "accountHolderName" : "Biblo Baggins",
   "paymentMethod"       : "ach", // ach or creditCard
   "contactInfo"       : {
     "billingAddress1"   : "123 Shire Blvd",
     "billingAddress2"   : "Lower Hobbit Hole",
     "billingCity"       : "The Shire",
     "billingState"      : "CO",
     "billingZip"        : "80125",
     "billingCountry"    : "US"
   }
}
```

Response

HTTP 200

##### DELETE - Disable an account
* URI - /account/{account-guid}
* SUCCESS CODE - 202
* ERROR CODES
	- 500 input validation error

Example

```
DELETE /account/59e680df-df0a-42c3-969a-800d35ca5684
Accepts: application/json
Date: Tue, 27 Mar 2007 21:15:45 +0000
Authorization: IGF_HMAC_SHA1 59e680df-df0a-42c3-969a-800d35ca5684:frJIUN8DYpKDtOLCwo//yllqDzg=
```

Response

HTTP 202
