<?php

class iGivefirst_Error extends Exception {}
class iGivefirst_HttpError extends Exception {}

/**
 * An authentication error occurred as a result of an invalid api key, secret, or un-whitelisted IP
 */
class iGivefirst_AuthenticationError extends iGivefirst_HttpError {};

/**
 * The object requested was not found
 */
class iGivefirst_ObjectNotFound extends iGivefirst_HttpError {};

/**
 * The object sent already exists
 */
class iGivefirst_ObjectAlreadyExists extends iGivefirst_HttpError {};

/**
 * Donor information was not complete enough
 */
class iGivefirst_DonorInformationIncomplete extends iGivefirst_Error {};

/**
 * Donor could not be created because of invalid parameters
 */
class iGivefirst_DonorNotCreated extends iGivefirst_Error {};

/**
 * Donor could not be created because it already exists
 */
class iGivefirst_DonorAlreadyExists extends iGivefirst_Error {};

/**
 * Account information was not complete enough
 */
class iGivefirst_AccountInformationIncomplete extends iGivefirst_Error {};

/**
 * Account could not be created because of invalid parameters
 */
class iGivefirst_AccountNotCreated extends iGivefirst_Error {};

/**
 * Account could not be updated because of invalid parameters
 */
class iGivefirst_AccountNotUpdated extends iGivefirst_Error {};

/**
 * Donation information was not complete enough
 */
class iGivefirst_DonationInformationIncomplete extends iGivefirst_Error {};

/**
 * Donation could not be created because of invalid parameters
 */
class iGivefirst_DonationNotCreated extends iGivefirst_Error {};

?>
