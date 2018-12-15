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
//      error_reporting(E_ALL);
//      ini_set('display_errors', 1);

// TODO: Build and admin panel to manage the records.
// TODO: Edit/Update/Delete records functions on the back end
// TODO: Require authentication to do this.

// mongodb connection
require 'mongodb/vendor/autoload.php';

// Discogs api credentials
$consumerKey        = "jaRkJhfCzjSmakRoGyjP";
$consumerSecret     = "MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC";
$table              = $_POST['collection'];
$title              = urlencode( preg_replace('#\s*\[.+\]\s*#U', ' ', $_POST['title'] ) ); // remove (text)
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
$results            = $out->results[0]; // just the first row of content returned.
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

// mongo query to find by artist and title
$find = $collection->findOne(
    ['$and'    =>   [
        ['artist'    => $_POST['artist']],
        ['title'     => $_POST['title'] ]
        ]
    ]
);

// -------------- Notes to my former self ---------------------------------------------
// Only perform the findOneAndUpdate() *if we have a valid master id* from discogs.com
//-------------------------------------------------------------------------------------

if($results->master_id):

        $updateResult = $collection->findOneAndUpdate(
            ['$and'    =>   [
                                ['artist'    => $_POST['artist']],
                                ['title'     => $_POST['title'] ]
                            ]
            ],
                ['$set'  => [
                            'title'         => $_POST['title'],
                            'artist'        => $_POST['artist'],
                            'loveDate'      => $dt->format('m-d-y g:i:s a'),
                            'album'         => $_POST['album'],
                            'stationName'   => trim($_POST['stationName']),
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
                            'coverArt'      => isset($_POST['coverArt']) ? $_POST['coverArt'] : null,
                            'lyrics'        => $lyrics,
                            'first_played'  => is_null($find->first_played) ? $dt->format('m-d-y g:i:s a'): $find->first_played,
                            'last_played'   => $dt->format('m-d-y g:i:s a')
                         ]
            ],
            ['upsert'   => true]
        );

    $updatePlays = $collection->findOneAndUpdate(
                ['$and'    =>   [
                                    ['artist'    => $_POST['artist']],
                                    ['title'     => $_POST['title'] ]
                                ]
                ],
                [
                    '$inc' => ['num_plays' => 1 ]
                ],
                ['upsert'   => true]
    );
endif;

exit; // shut the door on your way out..

/**
 * @param $s
 * @return mixed
 * wikidefinition -- returns json format of results from the query searching for
 */

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