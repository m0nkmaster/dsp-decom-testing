<?php

$subDomain = strstr($_SERVER['HTTP_HOST'], 'm.') ? 'm' : 'www';

$url = "http://$subDomain.test.bbc.co.uk" . str_replace('clone/', '', $_SERVER['REQUEST_URI']);
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
?>