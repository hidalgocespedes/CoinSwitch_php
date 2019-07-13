<?php

	//VARIABLES
	define('CS_CALLBACK','http://foo.goo.ee');
	define('PAYOUT_CURRENCY', 'btc');


    define("FIXED_PRICE",0.3);
    define("FIXED_PRICE_SYMBOL",'btc');



	$PAYOUT_ADDRESS = Array(
		'btc'	=> '18TtHadQmkB1qcmX2o8kX9tuhnAvdjmbJT'
		, 'eth' => '0xbf9525da02ad57ad5febc8586d415edc4f16443c'
		, 'ltc' => 'LSX627aParGLEBGRtTTg78BfLLh3mGfJaE'
		, 'etc' => '0xd672eb886719649b74649c9ae08a34ff20e7b24d'
        ,'doge' => 'DHKM6NDUUv9kaHAGi1QU7MRBNKfQiAdP3F'     //fake address
		,'bch'	=> '18TtHadQmkB1qcmX2o8kX9tuhnAvdjmbJT'
	);

	define('PAYOUT_ADDRESS', $PAYOUT_ADDRESS);
	
	//Generic Sandbox
	$headers = Array( 'x-api-key: cRbHFJTlL6aSfZ0K2q7nj6MgV5Ih4hbA2fUG0ueO', 'x-user-ip: 1.1.1.1');
	$headers = Array( 'x-api-key: F6NtLiKEMMlGgZsnq7gEa9rMCswgaUu8NfvOW8Fe', 'x-user-ip: 63.32.142.141');
    define('AUTH_HEADER', $headers);


	//APIS
	define('URL_INVOICECREATE', 'https://api.coinswitch.co/v2/payment/invoice');
	define('URL_INVOICESTATUS', 'https://api.coinswitch.co/v2/payment/invoice');
	define('URL_INVOICECOINS', 'https://api.coinswitch.co/v2/payment/coins');
	define('URL_INVOICEPAYMENT', 'https://api.coinswitch.co/v2/payment/invoice');

	define('URL_ORDERCREATE', 'https://api.coinswitch.co/v2/order');
	define('URL_ORDERSTATUS', 'https://api.coinswitch.co/v2/order');
	define('URL_ORDERCOINS', 'https://api.coinswitch.co/v2/coins');
	define('URL_PAIRS', 'https://api.coinswitch.co/v2/pairs');
	define('URL_RATE', 'https://api.coinswitch.co/v2/rate');


    //Optionally, we cached
    define('DIR_CACHE', '/tmp/');
    define('FILE_ORDERCOINS', DIR_CACHE . 'order_coins.json');
    define("FILE_INVOICECOINS", DIR_CACHE . 'invoice_coins.json');
    define("FILE_RATE", DIR_CACHE .'rates.json');
    define("FILE_PAIRS", DIR_CACHE .'pairs.json');

	
?>
