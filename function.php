<?php	


/* _------------------------------------------  */
/*          HTTP                                */
/* _------------------------------------------  */
    //Envía un post HTTP
    function httpPost($url, $data, $headers)   {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
    }


    //Envía un get HTTP
    function httpGET($url, $headers = null)   {
            $curl = curl_init($url);
            //curl_setopt($curl, CURLOPT_GET, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            if (isset($headers))
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
    }

    //Get the a list from CoinsSwitch
    function getJSON($url, $file = null, $onlyActive = false, $headers = null, $data = null){

        $ret = Array();
        $headers = empty($headers) ? AUTH_HEADER : $headers;

	    //Posibility 1: already downloaded ?
        if (isset($file) && file_exists($file)) {
		    $ret = json_decode(file_get_contents($file),true);
		    error_log($url . " from cache");
	    } else {  

	        //Pos  2: it has to be downloaded
            if (isset($data)) {
	            $res = json_decode(httpPOST($url, $data, $headers),true);
                //error_log(json_encode($data));
            }
            else 
	            $res = json_decode(httpGET($url, $headers),true);
	        error_log("Downloading list...".$url);

	        if (isset($res['success']) && ($res['success']=='true')) {
                if (isset($file))
                    file_put_contents($file, json_encode($res['data']));
		        $ret = $res['data'];
	        }
        }
        return $onlyActive ? onlyActive($ret) : $ret;
    }




/* _------------------------------------------  */
/*          INVOICES                            */
/* _------------------------------------------  */
//Get the coin list ready for invoices from CoinsSwitch
//Not all the coins are available for invoices and orders, and viceversa.
function getInvoiceCoins($onlyActive = false){
    return getJSON(URL_INVOICECOINS, FILE_INVOICECOINS, $onlyActive);
}



function createInvoice($price, $curA, $curB, $rate)  {

	//Building headers
    $headers = AUTH_HEADER;
	$headers[] = 'content-type: application/json';

	//Building data
	$data = Array();
	$data['payout'] = Array(
		"currency" => $curA 
		,"amount" => $price
		,"address" => ""
		,"callback" => CS_CALLBACK
        //,'defaultPaymentMode' => $currency
		);
	$data['payout']['address'] = Array( 'currency' => $curA, 'address' => PAYOUT_ADDRESS[$curA], "tag" => "");
	//$data['payment'] = Array( 'currency' => $currency, "amount" => 22);
	error_log("Creating invoice with data = " . json_encode($data));

	//POSTing
	$invo = json_decode(httpPost(URL_INVOICECREATE,json_encode($data), $headers),true);
    if (($invo['success'] == 'true')) {
        $r = httpPost(URL_INVOICEPAYMENT .'/'.  $invo['data']['invoiceId'].'/paymentmode/' . $curB, null, AUTH_HEADER);
        $invo = checkInvoiceStatus($invo['data']['invoiceId']);
        error_log(json_encode($invo));
    }
	return $invo;
}

//Checks the status of an specific order by orderId
function checkInvoiceStatus($invoiceId) {
    return json_decode(httpGet(URL_INVOICESTATUS.'/'.$invoiceId, AUTH_HEADER),true);
}

/* _------------------------------------------  */
/*          ORDERS                              */
/* _------------------------------------------  */

//Get the coin list ready for orders from CoinsSwitch
//Not all the coins are available for invoices and orders, and viceversa.
function getOrderCoins($onlyActive = false){
    return getJSON(URL_ORDERCOINS, FILE_ORDERCOINS,$onlyActive);
}

//Get the rate to change from currencyA to B. We can get more data about limits and so on, but for now it will be ignored
//Not all the coins are available for invoices and orders, and viceversa.
function getRate($curA, $curB){
	//Building headers
	$headers = AUTH_HEADER;
	$headers[] = 'content-type: application/json';

    //Building data
    $data = Array("depositCoin" => $curA,"destinationCoin" =>$curB);
    error_log("Quering rate with ".json_encode($data));
    $res = getJSON(URL_RATE,  null, true, $headers, json_encode($data));
    error_log(json_encode($res));
    if (isset($res['rate']))
        return strval($res['rate']);
    else return 0;
}

//Create an order to trade from a currency into another.
//The destination currency and our private address is a constant defined in config.php
function createOrder($amount, $currency) {

	//Building headers
	$headers = AUTH_HEADER;
	$headers[] = 'content-type: application/json';

	//Building data
	$data = Array(
        "depositCoin" => $currency
        ,"destinationCoin" => PAYOUT_CURRENCY
        ,"depositCoinAmount" => $amount
        ,"destinationCoinAmount" => ""
        ,"destinationAddress" => ""     // PAYOUT_ADDRESS[PAYOUT_CURRENCY]
        //,"refundAddress" => ""        // alternate JSON object to recollect in caso of a trade error
		//,"callback" => CS_CALLBACK    //to receive any change in the status of the order
		);
    //$data['refundAddress'] = Array(
        //    "address" => "" // alternative collecting address
        //    ,'tag' => null);

    $data['destinationAddress'] = Array(
        "address" => PAYOUT_ADDRESS[PAYOUT_CURRENCY]
        ,'tag' => null);

	error_log("Creating order with data = " . json_encode($data));

	//POSTing
	$res = httpPost(URL_ORDERCREATE,json_encode($data), $headers);
	error_log($res);
	return json_decode($res,true);
}

//Get a JSON with the description and the status of the invoice
function checkOrderStatus($orderId) {
    return json_decode(httpGet(URL_ORDERSTATUS.'/'.$orderId, AUTH_HEADER),true);
}


/* _------------------------------------------  */
/*          tools                               */
/* _------------------------------------------  */

//Inefficient. Given a symbol and a list, it returns the index 
function coin_index($coins, $symbol) {
	foreach($coins as $i => $c) {
		if ($c['symbol'] == $symbol)
			return $i;
	}
	return -1;
}

//Inneficient.
//Sublist with the attribute isActive
//Calling this everytime is inefficient. We should cache a copy of it and reuse it a while. 
function onlyActive($list) {

    if ((count($list)>0) && isset($list[0]['isActive'])) {
        $new = Array();
        foreach($list as $c) {
        if ($c['isActive'] == 'true')
            $new[] = $c;
        }
        return $new;
    }
    return $list;
}

