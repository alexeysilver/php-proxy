<?php

error_reporting(0); // no errors pls
set_time_limit(2); // 2 second for everything

session_start();

$hidden_mode = false;

$base_http_host = 'google.com';  //set this to the url you want to scrape
$base = 'http://' . $base_http_host;

$url = $base . $_SERVER['REQUEST_URI'];

// static locate to orign host. 
// If you whant to hide orign host, better delete next code
if(preg_match('~\.(jpg|jpeg|gif|png|bmp|js|css|txt|jar|tgz|rar|zip|gz)$~i', $_SERVER['REQUEST_URI']) && $hidden_mode == false)// If you whant to hide orign host, better delete next code
{
  header("Location: ".$url);
  exit;
}

if($_SERVER['HTTPS'] == 'on') exit; // no https pls. Hate it

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl, CURLOPT_TIMEOUT, 2);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $_POST);
}

// proxy cookie to orign
foreach($_COOKIE as $key=>$value)
{
	if(is_array($value)) continue; // no sub array
	curl_setopt($curl, CURLOPT_COOKIE, $key."=".$value."; domain=.".$base_http_host." ; path=/");
}

$response = curl_exec($curl);
curl_close($curl);

list($header, $body) = explode("\r\n\r\n", str_replace("HTTP/1.1 100 Continue\r\n\r\n", '', $response), 2);

$header = split(chr(10),$header);
foreach($header as $key=>$value){
	
	if(preg_match("~^transfer-encoding~i",$value) continue;
	$value = trim(str_replace(array($base, $base_http_host), array($mydomain,$_SERVER['HTTP_HOST']), $value));
	header($value);

}

print str_replace($base, $mydomain, $body);
