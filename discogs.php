<?php
/**
 * Date: 10/22/18
 * Time: 7:39 PM
 * scottybox - scottfleming
 * Consumer Key	jaRkJhfCzjSmakRoGyjP
   Consumer Secret	MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC
   Request Token URL	https://api.discogs.com/oauth/request_token
   Authorize URL	https://www.discogs.com/oauth/authorize
   Access Token URL	https://api.discogs.com/oauth/access_token
 */

$consumerKey        = "jaRkJhfCzjSmakRoGyjP";
$consumerSecret     = "MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC";
$album              = urlencode("Tuff Enuff");
$title              = urlencode("Wrap It Up");
$artist             = urlencode("The Fabulous Thunderbirds");

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "https://api.discogs.com/database/search?key=" .
    "$consumerKey&secret=$consumerSecret&track=$title&artist=$artist",
    CURLOPT_USERAGENT => "pianobar/1.1"
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);

echo "<pre>";

$out = json_decode($resp);
$results = $out->results[0]; // just the first row.

print_r($results);

// vars for input
$id        = $results->id;
$master_id = $results->master_id;
$year      = $results->year;
$style     = implode("," ,$results->style);
$genre     = implode(",", $results->genre);
$country   = $results->country;
$thumb     = $results->thumb;
$coverImg  = $results->cover_image;
$formats   = implode(",", $results->format);
$labels    = implode(",", $results->label);
$catno     = $results->catno;

echo "TRACK: " . urldecode($title) .PHP_EOL;
echo "ARTIST: " . urldecode($artist) .PHP_EOL;
echo "ALBUM: ". urldecode($album) .PHP_EOL;
echo "LABEL: " . $labels . PHP_EOL;
echo "CATNO: " . $catno . PHP_EOL;
echo "DISCOGS ID: " . $id .PHP_EOL;
echo "MASTER ID: ". $master_id . PHP_EOL;
echo "YEAR: " . $year. PHP_EOL;
echo "STYLE: " . $style .PHP_EOL;
echo "GENRE: " . $genre .PHP_EOL;
echo "FORMATS: " . $formats . PHP_EOL;
echo "COUNTRY " . $country .PHP_EOL;
echo "THUMB: " . $thumb . PHP_EOL;
echo "COVERIMG: " . $coverImg . PHP_EOL;




exit;
