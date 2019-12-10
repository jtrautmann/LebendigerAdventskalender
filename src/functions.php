<?php

function get_current_url() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function shift($s,$d) {
	$r = '';
	for($i=0; $i<strlen($s); $i++){
		$n = ord($s[$i]);
		if($n>=8364) $n = 128;
		$r .= chr($n+$d);
	}
	return $r;
}

// https://stackoverflow.com/questions/5809774/manipulate-a-url-string-by-adding-get-parameters
function add_param($url, $param, $value) {
	$url_parts = parse_url($url);
	// If URL doesn't have a query string.
	if (isset($url_parts['query'])) { // Avoid 'Undefined index: query'
		parse_str($url_parts['query'], $params);
	} else {
		$params = array();
	}
	
	$params[$param] = $value;     // Overwrite if exists
	
	// Note that this will url_encode all values
	$url_parts['query'] = http_build_query($params);
	
	return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
}

function remove_param($url, $param) {
	$url_parts = parse_url($url);
	if (isset($url_parts['query'])) {
		parse_str($url_parts['query'], $params);
	} else {
		return $url;
	}
	
	if (array_key_exists($param, $params)) {
		unset($params[$param]);     // Unset if exists
	}
	
	$url_parts['query'] = http_build_query($params);
	
	return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
}