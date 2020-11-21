<?php
/**
 * OSSMailScanner module config.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 */
return [
	'ONE_MAIL_FOR_MULTIPLE_RECIPIENTS' => [
		'default' => false,
		'description' => "Add only one mail for multiple recipients.\n@var bool",
		'validation' => '\App\Validator::bool',
		'sanitization' => '\App\Purifier::bool'
	],
	'attachHtmlAndTxtToMessageBody' => [
		'default' => false,
		'description' => "Attach the content of HTML and TXT files to the email message body.\nThe content of all attachments will be added at the very end of the email body.\n@var bool",
		'validation' => '\App\Validator::bool',
		'sanitization' => '\App\Purifier::bool'
	],
	'searchPrefixInBody' => [
		'default' => false,
		'description' => 'Search prefix in body, type: boolean',
		'validation' => '\App\Validator::bool',
		'sanitization' => '\App\Purifier::bool'
	],
	'createTicketWithoutNoRelation' => [
		'default' => true,
		'description' => 'Create ticket when contact and account does not exist, type: boolean',
		'validation' => '\App\Validator::bool',
		'sanitization' => '\App\Purifier::bool'
	],
];
