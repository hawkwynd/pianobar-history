<?php
/**
 * Date: 10/23/18
 * Time: 8:49 PM
 * scottybox - scottfleming
 * Gathers master information about the artist
 * and includes wikipedia definitional info about the artist.
 */

require 'mongodb/vendor/autoload.php';

$consumerKey        = "jaRkJhfCzjSmakRoGyjP";
$consumerSecret     = "MGSKueXgidqwXOxbmmtSOGfUoFHtXdfC";
$masterID           = intval($_GET['id']);
$pianobar           = new stdClass(); // init our object
$output             = [];
// Some band names need to be set as (band). Expand this list as needed. -sf
$bandsToFilter = array('Boston', 'Styx', 'Incubus', 'Eagles', 'Kenny Wayne Shepherd', 'Journey', 'Chicago');
$tz                 = 'America/Chicago';
$timestamp          = time();
$dt                 = new DateTime("now", new DateTimeZone($tz));  //first argument "must" be a string
$dt->setTimestamp($timestamp); //adjust the object to correct timestamp



// init our collection client
$collection = (new MongoDB\Client)->scottybox->pianobar;
$results = $collection->find(
    [ 'masterId' => $masterID ],
    ['projection' => [
        'coverImg'  => 1,
        'formats'   => 1,
        'thumb'     => 1,
        'catno'     => 1,
        'lyrics'    => 1,
        'num_plays' => 1,
        'stationName'   => 1,
        'first_played' => 1,
        'last_played'   => 1
    ]] );


    foreach( $results as $row){

                    // drop the first line of the lyrics, which leaves 2 \n\n afterwards
                    $formatted_lyrics                 = preg_replace('/^.+\n/', '', $row->lyrics);
                    $pianobar->lyrics                 = nl2br(  preg_replace('/^.+\n\n/', '', $formatted_lyrics)    );
                    $pianobar->metadata->coverImg     = $row->coverImg;
                    $pianobar->metadata->formats      = str_replace(',', ', ', $row->formats);
                    $pianobar->metadata->thumb        = $row->thumb;
                    $pianobar->metadata->catno        = $row->catno;
                    $pianobar->core->first_played     = $row->first_played == null ? $row->last_played : $row->first_played;
                    $pianobar->core->last_played      = $row->last_played;
                    $pianobar->core->num_plays        = $row->num_plays;
                    $pianobar->core->stationName      = $row->stationName;
    }


/**
 * Get the master information based on the master_id from query of search
 */

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_URL                 => "https://api.discogs.com/masters/$masterID",
        CURLOPT_USERAGENT           => "pianobar/1.1"
    )
);
// Send the request & save response to $resp
$resp   = curl_exec($curl);
$output = json_decode($resp);

//echo "<pre>";

//echo "https://api.discogs.com/masters/$masterID" . PHP_EOL;
//print_r($output);

$pianobar->song_title       = $output->title;
$pianobar->year_released    = $output->year;
$pianobar->styles           = implode(", ", $output->styles);
$pianobar->genres           = implode(", ", $output->genres);
$pianobar->main_release     = $output->main_release;
$pianobar->tracklist        = array();

foreach($output->tracklist as $track){
    array_push($pianobar->tracklist, $track->title);
}
$pianobar->tracklist = array_unique($pianobar->tracklist);

foreach($output->artists as $artist){
    $artistID = $artist->id;
    $pianobar->artist->id = $artist->id;
    // removes (2) or whatever number
    $pianobar->artist->name = trim(preg_replace('/\([0-9]\)/', '', $artist->name));
}

// videos
$pianobar->artist->videos = array();
foreach($output->videos as $video){
    array_push($pianobar->artist->videos, $video);
}

/**
 * Get the Artist Info based on the artist_id from the master query
 */

$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl,
    array(
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_URL                 => "https://api.discogs.com/artists/$artistID",
        CURLOPT_USERAGENT           => "pianobar/1.1"
    )
);
// Send the request & save response to $resp
$resp = curl_exec($curl);
$info = json_decode($resp);

$pianobar->artist->members = array();

foreach($info->members as $member){

    $member->name = trim(preg_replace('/\([0-9]\)/', '', $member->name));
    $wiki = json_decode( wikidefinition( $member->name ) );

    foreach($wiki->query->pages as $content){
        $memberContent = filterWikiContent( strip_tags($content->extract), $member->name);
    }

     array_push($pianobar->artist->members,  array(
            'active'            => $member->active,
            'member_name'       => $member->name,
            'member_content'    => $memberContent
        )
     );

}
// Get the wiki Main_Page for the artist name.
// sometimes you need to add (band) to the end of the string
// to get the correct content from wikipedia...

$wiki = json_decode( wikidefinition($pianobar->artist->name ) );

foreach($wiki->query->pages as $wikiResult){
    $content                    = trim(strip_tags($wikiResult->extract));
    $pianobar->wiki->extract    = $wikiResult->extract;

    if( !$content || strpos( $content, 'may refer to:') > 0  || in_array( $pianobar->artist->name, $bandsToFilter)){
        $wiki_retry = json_decode( wikidefinition($pianobar->artist->name . " (band)") );
        foreach($wiki_retry->query->pages as $content_retry){
            $pianobar->wiki->search         = $pianobar->artist->name ." (band)";
            $content                        = strip_tags($content_retry->extract);
            $pianobar->wiki->extract        = $content_retry->extract;
        }
    }else{
        $pianobar->wiki->search  = $pianobar->artist->name;
    }

    $pianobar->wiki->notag_content = $content;
}

echo json_encode($pianobar);


/**
 * @param $s
 * @return array|string
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

/**
 * filterWikiContent
 * @description used to check if the wikipedia search result has a string matching in it and return
 * something less idiotic than what wikipedia says...
 * @param $content
 * @param $memberName
 * @param string $filter
 * @return string
 */



function filterWikiContent($content, $memberName, $filter="may refer to:"){
    if( strpos($content,$filter) !== false ){
        return "The internet (wikipedia) has no relevant info on " .$memberName. ". Guess you are on your own to Google it for yourself, sorry, but I'm a little busy at the moment and don't have time to do everything for you, ya ungrateful, lazy sloth!";
    }

    return $content;
}