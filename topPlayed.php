<?php
/**
 * Date: 12/19/18
 * Time: 4:56 PM
 * scottybox - scottfleming
 * Find the top played title/Artist in collection with num_plays.
 */
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require 'mongodb/vendor/autoload.php';

$collection = (new MongoDB\Client)->scottybox->pianobar;
$pianobar       = new stdClass(); // init our object

$filter=[];
$options = ['sort' => ['num_plays' => -1]]; // -1 is for DESC
$result = $collection->findOne($filter, $options);

    $pianobar->first_played = $result->first_played;
    $pianobar->last_played  = $result->last_played;
    $pianobar->top_artist   = $result->artist;
    $pianobar->top_title    = $result->title;
    $pianobar->plays        = $result->num_plays;

echo json_encode($pianobar);
exit;
