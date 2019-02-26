<?php
/**
 * Date: 1/24/19
 * Time: 8:21 AM
 * scottybox - scottfleming
 *
 * var MasterUrl   = 'getMaster.php?id='+data;
 *
 */

$id = $_GET['id'];

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://scottybox.tech/getMaster.php?id=' . $id,
    CURLOPT_USERAGENT => 'scottybox master request'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);

$out = json_decode($resp, true);

print_r($out);