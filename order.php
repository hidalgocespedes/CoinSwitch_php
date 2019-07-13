<!DOCTYPE html>
<html>
<head>
<body>
    <?php 
    	include 'config.php';
    	include 'function.php';
	    $coins = getOrderCoins(true);
    ?>


<h1>Creating an Order</h1>
An order is a trade to convert from the currency A to the currency B.
<br>
It can be used to get the money from a customer but after the conversion the money is sent the equivalency to our address in the currency B.

<br>
<br>
<br>
<form action="main.php" method="POST" name='deposit'>
In this example, any desired amount of <b><?php echo FIXED_PRICE_SYMBOL;?></b> will be transferred from the source currency selected by the customer.<br>
You, customer, decide the currency and the amount and internally it will be converted to <b><?php echo FIXED_PRICE_SYMBOL;?></b> (for example) and sent to us.<br>
<br>
  	<br>

    <label>Currency (<?php echo count($coins); ?> avail.)</label>
	<br>
	<input list="currencies" name="symbol" value=""/>
	<datalist id="currencies">
        <?php
	    //Real getter using CoinSwitch API
	    foreach($coins as $c) {
		    echo '<option label="'.$c['name'] .'" value="'.$c['symbol'].'">';
	    }
        ?>
    </datalist>
<br>
<input type="number" name="amount" value="<?php echo FIXED_PRICE;?>" >
  	<br><br>
    <br>
	<button type="submit" value="order" name="op">Test ORDER API</button>
</form> 

<br>
<br>
<br>
<a href="index.php">Back to index</a>

<br>

</body>
</html>
