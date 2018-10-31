<?php
/**
 * Date: 10/31/18
 * Time: 7:22 AM
 * scottybox - scottfleming
 */

$artistID = '108713';

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_URL                 => "https://api.discogs.com/artists/$artistID/releases?sort=year&sort_order=asc",
        CURLOPT_USERAGENT           => "pianobar/1.1"
    )
);
// Send the request & save response to $resp
$resp   = curl_exec($curl);
$output = json_decode($resp);

echo "<pre>";
print_r( $output );