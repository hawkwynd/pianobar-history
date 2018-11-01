<?php
/**
 * Date: 6/28/18
 * Time: 3:21 PM
 * scottybox - sfleming
 * get all the records in the mongo and return.
 */
require 'mongodb/vendor/autoload.php';

$output = $final =[];
$table  = $_GET['table'];

// init our collection client
$collection = (new MongoDB\Client)->scottybox->$table;

foreach( $collection->find() as $row){
    array_push($output, $row);
}

$final['data'] = $output;

echo json_encode($final);
exit;
