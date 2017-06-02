<?php
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => 'http://localhost/CharonMVC/',
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_NOBODY => true,
    CURLOPT_VERBOSE => true,
));
$r = curl_exec($ch);

echo PHP_EOL.'Response Headers:'.PHP_EOL;
print_r($r);
curl_close($ch);
