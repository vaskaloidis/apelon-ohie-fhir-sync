<?php
global $load_error, $gui,  $url;

if($load_error) {
	$gui->alertDanger("The Default ValueSet was not set in config.values.php");
}

//HTML
echo '<html>';
echo '<head>';
	//echo '<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>'; //TODO: JQUERY 
	echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">';
	echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">';
echo '</head>';
echo '<body>';
	echo '<h2>Terminology Asset Management&nbsp;&nbsp;&nbsp; <a href="' . $url . '"><small>(Home) </small></a></h2>';
	echo '<hr>';
	echo '<div class="container">';
		echo '<div class="col-xl-9 col-md-9 col-sm-9">';