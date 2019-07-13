<!DOCTYPE html>
<html>
<head>
<body>
    <?php 
    	include 'config.php';
    	include 'function.php';

	    $coins = getInvoiceCoins($headers,true);

    ?>

<h1>Creating an Invoice</h1>
An invoice is a virtual bill to be paid for a services or a product. It represents a fixed price to be paid.

<br>
<br>
<br>

<form action="main.php" method="POST" name='deposit'>
In this example, the price for the service is <b><?php echo FIXED_PRICE. ' '.FIXED_PRICE_SYMBOL; ?></b><br>
  	<br>
  	<br><br>

    <label>Select the currency you want to pay <?php echo "(".count($coins) . " available)"; ?>. </label>
	<br>
	<input list="currencies" name="symbol" value=""/>
	<datalist id="currencies">
        <?php
        /* Next 5 lines are useful For testing, 
            * ... but what if we fix the currencies we are interested in instead of get the list too often ? */
	    //$coins = Array();
	    //$coins[] = Array("name" => "Waltonchain", "symbol" => "wtc", "isActive"=>  true, "isFiat"=>  false, 
	    //		"logoUrl"=> "https://files.coinswitch.co/public/coins/wtc.png", "parentCode"=> "eth", "addressAdditionalData"=> null);
	    //$coins[] = Array("name"=> "OAX", "symbol"=> "oax", "isActive"=> true, "isFiat"=> false, 
	    //		"logoUrl"=> "https://files.coinswitch.co/public/coins/oax.png", "parentCode"=> "eth", "addressAdditionalData"=> null);
	
	    //getter using CoinSwitch API
	    foreach($coins as $c) {
		    echo '<option label="'.$c['name'] .'" value="'.$c['symbol'].'">';
	    }
        ?>
    </datalist>
  	<br><br>
	<button type="submit" value="invoice" name="op">Test INVOICE API</button>
</form> 
<br>
<br>
<br>
<br>
<br>
<a href="index.php">Back to index</a>


</body>
</html>
