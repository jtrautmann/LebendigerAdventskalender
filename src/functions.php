<?php

function get_current_url() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function shift($s,$d) {
	for($i=0; $i<strlen($s); $i++){
		$n = ord($s[$i]);
		if($n>=8364) $n = 128;
		$r .= chr($n+$d);
	}
	return $r;
}

function rand_shift($s) {
    $rand = rand(1,5);
	$emailShifted = shift($s,$rand);
}

?>