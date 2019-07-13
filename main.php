<?php

include 'config.php';
include 'function.php';


function error($json) { 
    echo "We've got an error: ".json_encode($json);
    echo "<br><br>Mind that:";
    echo "<br> - The amount cannot be 0";
    echo "<br> - In this example, we have only receiving address for ". json_encode(array_keys(PAYOUT_ADDRESS));
    echo "<br> - (JUST FOR INVOICES) Payout must be one of ['btc', 'eth', 'ltc', 'trx', 'doge', 'bchabc']";
    echo "<br> - Some currencies are limited with a minimum and a maximum amount to be traded. Out of that limits we get an error";
}

function qr($text) {
	return '<img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$text.'&chld=L|1&choe=UTF-8" alt="'.$text.'">';
}

function showInvoice($invoice) { 
    $cad = "JSON received: ".json_encode($invoice)
	."<br><br>"
    ;

    $cad .= "INITIAL: Invoice for ".$invoice['data']['defaultPaymentMode']['amount'].' '.$invoice['data']['defaultPaymentMode']['currency']
		."<br>EQUIVALENCY: Invoice for ". $invoice['data']['alternatePaymentModes'][0]['amount'] .' '. $invoice['data']['alternatePaymentModes'][0]['currency']
		."<br><br>";

    $cad .= "You can send ".$invoice['data']['defaultPaymentMode']['amount'].' of '.$invoice['data']['defaultPaymentMode']['currency']. ' to the following address :'
	."<br><br>";
	$a = $invoice['data']['defaultPaymentMode']['address']['address'];
    $cad .= $a
		."<br><br>"
        .qr($a)
		."<br><br>";

	$cad .= "or you can send ".$invoice['data']['alternatePaymentModes'][0]['amount'] .' '. $invoice['data']['alternatePaymentModes'][0]['currency']. ' to the following address : ';
	$cad .= "<br><br>";
    $a = $invoice['data']['alternatePaymentModes'][0]['address']['address'];
    $cad .= $a;

	$cad .= "<br><br>"
        .qr($a)
		."<br><br>"
        .'<a href="main.php?op=invoice&invoiceId='.$invoice['data']['invoiceId'].'">Check the status of the invoice ' . $invoice['data']['invoiceId'].'</a>';
    return $cad;
}

function showOrder($order) { 
    error_log(json_encode($order));
    $cad = '<br><br>'
        .'JSON received: '.json_encode($order)
		.'<br><br>'
        ."INTERPRETATION"
		.'<br><br>'
		.'You have to send '.$order['data']['expectedDepositCoinAmount'] .' <b>'.$order['data']['depositCoin']  .'</b> to the following address : '
		.'<br><br>';
	
    $a = $order['data']['exchangeAddress']['address'];
    $cad .= $a;
	$expectedA = $order['data']['expectedDepositCoinAmount'];
	$expectedB = $order['data']['expectedDestinationCoinAmount'];
	$cad .= '<br> - We are waiting you to send '.$expectedA. ' ' .strtoupper($order['data']['depositCoin'])
		.'<br> - The estimated amount to be received is '.$expectedB. ' ' .strtoupper($order['data']['destinationCoin']).'<br>'
	    .'<br><br>'
        .qr($a)
		.'<br><br>'
		.'<br><br>'
        .'<a href="main.php?op=order&orderId='.$order['data']['orderId'].'">Check the status of the order ' . $order['data']['orderId'].'</a>';
    return $cad;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST : " . json_encode($_POST));
	switch ($_POST['op']) {

        //Create an invoice
	case 'invoice':
        $coins = getInvoiceCoins(true);
		$cur = coin_index($coins, $_POST['symbol']);

		echo "Creating invoice ...";
		$invoice = createInvoice(FIXED_PRICE, FIXED_PRICE_SYMBOL, $coins[$cur]['symbol'], $rate);
        error_log("Invoice " . json_encode($invoice));
        if ($invoice['success'] == 'true') { 
		    echo "Done.";
		    echo "<br><br>";
            echo showInvoice($invoice);
        } else {
		    echo "<br><br>";

            error($invoice);

            return;
        }
		break;

        //Create an order
    case 'order':
        $coins = getOrderCoins(true);

		$cur = coin_index($coins, $_POST['symbol']);
		echo "REQUEST:  Order for ".$_POST['amount'] .' '.$coins[$cur]['name'];
		echo "<br><br>";
		echo "Creating order ...";
		$order = createOrder($_POST['amount'], $coins[$cur]['symbol']);
        error_log("Order  " . json_encode($order));
        if ($order['success'] == 'true') { 
		    echo "Done.";
		    echo "<br><br>";
            echo showOrder($order);

        } else {
		    echo "<br><br>";
            error($order);
            return;
        }
		break;
	default:  echo "Don't understand!";

	}

}
else  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	//header('Location: index.php');
    error_log("GET : " . json_encode($_GET));
	switch ($_GET['op']) {

        //Check invoice status
	case 'invoice':
        echo date("Y-m-d H:i:s") . "   Status for the invoice : <br>" . $_GET['invoiceId'] . ': ';
        $st = checkInvoiceStatus($_GET['invoiceId']);
        echo  json_encode($st);
        if ($st['success'] == 'true') {
            echo showInvoice($st);
            echo '<br><br>';
            echo "To be analyzed:<br>";
            echo "- The status of the invoice contains its full description<br>";
            echo "- Current status is <b>" . $st['data']['status'].'</b>';
            echo '<br><br>Any time you reload this page the status is re-checked using the CoinSwitch API<br>';
        } else {
            echo "<br>Something was wrong";
        }
        break;

        //Check order status
    case 'order':
        echo date("Y-m-d H:i:s"). "   Status for the order <br>" . $_GET['orderId'] . ': ';
            echo '<br><br>Any time you reload this page the status is re-checked using the CoinSwitch API<br>';
        $st = checkOrderStatus($_GET['orderId']);
        if ($st['success'] == 'true') {
            echo '<br><br>';
            echo showOrder($st);
            echo '<br><br>';
            echo "To be analyzed:<br>";
            echo "- The status of the order contains its full description<br>";
            echo "- Current status is <b>" . $st['data']['status'].'</b>';
        } else {
            echo "<br>Something was wrong";
        }
        break;

	default:  echo "Don't understand!";
    }

}

