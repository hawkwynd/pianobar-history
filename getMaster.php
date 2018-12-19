<?php
/**
 * Date: 10/23/18
 * Time: 8:49 PM
 * scottybox - scottfleming
 * Gathers master information about the artist
 * and includes wikipedia definitional info about the artist.
 * YES THIS IS THE DATAMINER!!!
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'mongodb/vendor/autoload.php';

$consumerKey    = "jaRkJhfCzjSmakRoGyjP";
$consumerSecret = "MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC";
$masterID       = intval($_GET['id']); //339449
$pianobar       = new stdClass(); // init our object
$output         = [];
$bandsToFilter  = array('Boston', 'Styx', 'Incubus', 'Eagles', 'Kenny Wayne Shepherd', 'Journey', 'Chicago', 'Saga','A Flock of Seagulls','John Mayall & The Bluesbreakers', 'Tom Petty & The Heartbreakers', 'Uriah Heep');

$tz             = 'America/Chicago';
$timestamp      = time();
$dt             = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
$dt->setTimestamp($timestamp); //adjust the object to correct timestamp

// init our collection client
$collection = (new MongoDB\Client)->scottybox->pianobar;
$stations   = (new MongoDB\Client)->scottybox->stations;

$results = $collection->find( ['masterId' => $masterID] );





//$pianobar->_id = (string) $results->_id;

$pianobar->master_id = $masterID;

foreach ($results as $row) {

    // drop the first line of the lyrics, which leaves 2 \n\n afterwards
    $formatted_lyrics               = preg_replace('/^.+\n/', '', $row->lyrics);
    $pianobar->lyrics               = nl2br(preg_replace('/^.+\n\n/', '', $formatted_lyrics));
    $pianobar->metadata->coverImg   = $row->coverImg;
    $pianobar->metadata->formats    = str_replace(',', ', ', $row->formats);
    $pianobar->metadata->thumb      = $row->thumb;
    $pianobar->metadata->catno      = $row->catno;
    $pianobar->core->first_played   = $row->first_played == null ? $row->last_played : $row->first_played;
    $pianobar->core->last_played    = $row->last_played;
    $pianobar->core->num_plays      = $row->num_plays;
    $pianobar->core->stationName    = $row->stationName;
    $pianobar->core->title          = $row->title;

    // find stationDescription from stationName
    $stationResults = $stations->find( ['stationName' => $row->stationName ]);
    foreach($stationResults as $station){
        $pianobar->core->stationDescription = $station->description;
    }
}

$pianobar->core->station_plays = $collection->count( ['stationName' => $pianobar->core->stationName] );



/**
 * Get the master information based on the master_id from query of search
 */

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here

curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_URL             => "https://api.discogs.com/masters/$masterID",
        CURLOPT_USERAGENT       => "pianobar/1.1"
    )
);

// Send the request & save response to $resp
$resp                       = curl_exec($curl);
$output                     = json_decode($resp);
$pianobar->year_released    = $output->year;
$pianobar->styles           = implode(", ", $output->styles);
$pianobar->genres           = implode(", ", $output->genres);
$pianobar->main_release     = $output->main_release;
$pianobar->tracklist        = array();

//tracklist -- is this needed?
foreach ($output->tracklist as $track) {
    array_push($pianobar->tracklist, $track->title);
}
// filter dupes
$pianobar->tracklist = array_unique($pianobar->tracklist);


foreach ($output->artists as $artist) {
    $artistID               = $artist->id;
    $pianobar->artist->id   = $artist->id;
    $pianobar->artist->name = trim(preg_replace('/\([0-9]\)/', '', $artist->name)); // strip (2) from string
}

// videos -- no longer required, as we're getting our vids from youtube
// $pianobar->artist->videos = array();
// foreach ($output->videos as $video) {
//    array_push($pianobar->artist->videos, $video);
// }

/**
 * Get the Artist Info based on the artist_id from the master query
 */

$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "https://api.discogs.com/artists/$artistID",
        CURLOPT_USERAGENT => "pianobar/1.1"
    )
);

// Send the request & save response to $resp
$resp = curl_exec($curl);
$info = json_decode($resp);

$pianobar->artist->members = array();

foreach ($info->members as $member) {

    $member->name   = trim(preg_replace('/\([0-9]\)/', '', $member->name));
    $wiki           = json_decode(wikidefinition($member->name));

    foreach ($wiki->query->pages as $content) {
        $memberContent = filterWikiContent(strip_tags($content->extract), $member->name, 'From a short name:');
    }
    // build array of band members
    array_push($pianobar->artist->members, array(
            'active'            => $member->active,
            'member_name'       => $member->name,
            'member_content'    => $memberContent
        )
    );

}

// Get the wiki Main_Page for the artist name from wikipedia
// sometimes you need to add (band) to the end of the string
// to get the correct content from wikipedia...

$wiki = json_decode(wikidefinition($pianobar->artist->name));

foreach ($wiki->query->pages as $wikiResult) {
    $content                    = trim(strip_tags($wikiResult->extract));
    $pianobar->wiki->extract    = $wikiResult->extract;

    if (!$content || strpos($content, 'may refer to:') > 0 || in_array($pianobar->artist->name, $bandsToFilter)) {
        $wiki_retry             = json_decode(wikidefinition($pianobar->artist->name . " (band)"));
        foreach ($wiki_retry->query->pages as $content_retry) {

            $pianobar->wiki->search     = $pianobar->artist->name . " (band)";
            $content                    = strip_tags($content_retry->extract);
            $pianobar->wiki->extract    = $content_retry->extract;
        }

    } else {
        $pianobar->wiki->search         = $pianobar->artist->name;
    }

    $pianobar->wiki->notag_content      = $content;
}

// spew our array
echo json_encode($pianobar);


/**
 * @param $s
 * @return array|string
 */
function wikidefinition($s) {
    $url = "https://en.wikipedia.org/w/api.php?action=query&prop=extracts&exintro=&format=json&titles=" . urlencode($s);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
    curl_setopt($ch, CURLOPT_POST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_NOBODY, FALSE);
    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
    curl_setopt($ch, CURLOPT_REFERER, "");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");
    $page = curl_exec($ch);
    return ($page);
}

/**
 * filterWikiContent
 * @description used to check if the wikipedia search result has a string matching in it and return
 * something less idiotic than what wikipedia says...
 * @param $content
 * @param $memberName
 * @param string $filter
 * @return string
 */


function filterWikiContent($content, $memberName, $filter = "may refer to:")
{
    if (strpos($content, $filter) !== false) {
        return false; // don't send "may refer to:" string
    }

    return $content;
}