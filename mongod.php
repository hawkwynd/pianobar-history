<?php
/**
 * Date: 6/28/18
 * Time: 3:21 PM
 * scottybox - sfleming
 */
require '../zip/vendor/autoload.php';

$datapak = array();
$table = $_GET['table'];
// create collection if it doesnt exist
$collection = (new MongoDB\Client)->scottybox->$table;
$cursor     = $collection->find();
$output     = $final =[];

foreach($cursor as $row){
    array_push($output, $row);
}

$final['data'] = $output;

echo json_encode($final);