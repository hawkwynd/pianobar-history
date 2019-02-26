<?php
/**
 * Date: 1/5/19
 * Time: 5:33 AM
 * scottybox - scottfleming
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'mongodb/vendor/autoload.php';

$output = $final =[];
$table  = 'pianobar';


// init our collection client
$collection     = (new MongoDB\Client)->scottybox->$table;

$results           = $collection->find(array(),array("last_played" => -1), array('limit' => 10));

foreach( $results as $row){

    $row['last_played'] = new MongoDB\BSON\UTCDateTime(strtotime($row['last_played']) * 1000);

    array_push($output, $row);
}

$final['data'] = $output;
echo "<pre>";

echo json_encode($final);

exit;