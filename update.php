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
require 'mongodb/vendor/autoload.php';

$consumerKey        = "jaRkJhfCzjSmakRoGyjP";
$consumerSecret     = "MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC";
$table              = $_POST['collection'];
$title              = urlencode( $_POST['title'] );
$artist             = urlencode( $_POST['artist'] );
$lyrics             = $_POST['lyrics'];
$curl               = curl_init();

curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_URL                 => "https://api.discogs.com/database/search?key=$consumerKey&secret=$consumerSecret&track=$title&artist=$artist",
        CURLOPT_USERAGENT           => "pianobar/1.1"
    )
);
// Send the request & save response to $resp
$resp               = curl_exec($curl);

// Close request to clear up some resources
curl_close($curl);

$out                = json_decode($resp);
$results            = $out->results[0]; // just the first row.
$id                 = $results->id;
$master_id          = $results->master_id;
$year               = $results->year;
$style              = implode("," ,$results->style);
$genre              = implode(",", $results->genre);
$country            = $results->country;
$thumb              = $results->thumb;
$coverImg           = $results->cover_image;
$formats            = implode(",", $results->format);
$labels             = $results->label[0];
$catno              = $results->catno;
$collection         = (new MongoDB\Client)->scottybox->$table;
$tz                 = 'America/Chicago';
$timestamp          = time();
$dt                 = new DateTime("now", new DateTimeZone($tz));  //first argument "must" be a string

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
                'coverArt'      => isset($_POST['coverArt']) ? $_POST['coverArt'] : '',
                'lyrics'        => $lyrics,
                ''
                 ]
    ],
    ['upsert'   => true]
);

$matchFound     = $updateResult->getMatchedCount() > 0 ? $updateResult->getMatchedCount() : false;
$updateFound    = $updateResult->getModifiedCount() > 0 ? $updateResult->getModifiedCount() : false;

exit;

function wikidefinition($s) {
    $url = "https://en.wikipedia.org/w/api.php?action=query&prop=extracts&exintro=&format=json&titles=".urlencode($s);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
    curl_setopt($ch, CURLOPT_POST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY, FALSE);
    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
    curl_setopt($ch, CURLOPT_REFERER, "");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");
    $page = curl_exec($ch);

    return($page);
}