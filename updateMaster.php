<?php
/**
 * Date: 11/11/18
 * Time: 12:08 PM
 * scottybox - scottfleming
 * Makes update based on masterID
 */

require 'mongodb/vendor/autoload.php';
$collection         = (new MongoDB\Client)->scottybox->pianobar;
$masterId = intval($_GET['id']);

$updateResult = $collection->findOneAndUpdate(
      ['masterId'    => $masterId ],
        ['$set'  => [ 'num_plays'     => 2 ]
    ]
);

echo "<pre>";
print_r($updateResult);