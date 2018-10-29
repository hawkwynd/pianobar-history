<?php
/**
 * Date: 10/22/18
 * Time: 4:07 PM
 * scottybox - sfleming
 * This updates the mongo db lovedSongs table on Scottybox.
 * Handles duplicate loves as well, by updating the datetime stamp
 * and it's records.
 * https://www.discogs.com/developers/#page:home
 * Api information
 */
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$consumerKey        = "jaRkJhfCzjSmakRoGyjP";
$consumerSecret     = "MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC";

// lets get some variables, ok?
$table = $_POST['collection'];
$title = urlencode( $_POST['title'] );
$artist = urlencode( $_POST['artist'] );

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_URL                 => "https://api.discogs.com/database/search?key=$consumerKey&secret=$consumerSecret&track=$title&artist=$artist",
        CURLOPT_USERAGENT           => "pianobar/1.1"
    )
);
// Send the request & save response to $resp
$resp = curl_exec($curl);

// Close request to clear up some resources
curl_close($curl);

$out        = json_decode($resp);
$results    = $out->results[0]; // just the first row.
$id         = $results->id;
$master_id  = $results->master_id;
$year       = $results->year;
$style      = implode("," ,$results->style);
$genre      = implode(",", $results->genre);
$country    = $results->country;
$thumb      = $results->thumb;
$coverImg   = $results->cover_image;
$formats    = implode(",", $results->format);
$labels     = $results->label[0];
$catno      = $results->catno;

require 'mongodb/vendor/autoload.php';

// create collection if it doesnt exist
$collection = (new MongoDB\Client)->scottybox->$table;
$tz         = 'America/Chicago';
$timestamp  = time();
$dt         = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
$dt->setTimestamp($timestamp); //adjust the object to correct timestamp

// insert record into pianobar collection, if exists update the record
// so we don't have a duplicate entry, ever.

$updateResult = $collection->updateOne(
    ['title'    => $_POST['title']],
        ['$set'     => [
                'title'         => $_POST['title'],
                'artist'        => $_POST['artist'],
                'loveDate'      => $dt->format('m-d-y g:i a'),
                'album'         => $_POST['album'],
                'stationName'   => $_POST['stationName'],
                'id'            => $id,
                'masterId'      => $master_id,
                'style'         => $style,
                'genre'         => $genre,
                'country'       => $country,
                'coverImg'      => $coverImg,
                'thumb'         => $thumb,
                'formats'       => $formats,
                'year'          => $year,
                'catno'         => $catno,
                'status'        => $id,
                'label'         => $labels,
                'coverArt'      => $_POST['coverArt']
                 ]
    ],
    ['upsert'   => true]
);

$matchFound     = $updateResult->getMatchedCount() > 0 ? $updateResult->getMatchedCount() : false;
$updateFound    = $updateResult->getModifiedCount() > 0 ? $updateResult->getModifiedCount() : false;

    if($updateFound) {
        echo "Your love has been updated!!\n\n";
}

exit;
