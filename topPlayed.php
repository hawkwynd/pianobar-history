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

// find the top played song in collection
/*

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

echo "<h1>Genre Count</h1>";
echo "<br>";
*/

if( isset($_GET['q']) && $_GET['q'] == 'genres' ):
// get distinct field values by genre and count
$out    = $payload =[];
$genres = $collection->distinct('genre');

sort($genres);

    foreach ($genres as $option) {
        $result = $collection->countDocuments(['genre' => $option]);
        $option = $option == '' ? 'Unknown' : $option;

        $out[$option] = $result;
        //echo $option . ": " . $result . "<br/>";
    }
    arsort($out);

    foreach($out as $genre => $value){
        array_push($payload, array('genre' => $genre , 'count' => $value));
    }

    echo json_encode($payload);

endif;


if( isset($_GET['q']) && $_GET['q'] == 'artists' ):
    // display top 25 played titles/artist sorted by num_plays
    $filter = [];
    $result = $collection->find($filter, [ 'limit'          => 25,
                                            'sort'          => [ 'num_plays' => -1 ],
                                            'projection'    => [
                                                                '_id'           => 1,
                                                                  'num_plays'   => 1,
                                                                  'artist'      => 1,
                                                                  'title'       => 1
                                                                ]
                                        ]
    );

    $out = [];
    foreach($result as $row){
        array_push($out, array(
            'artist' => $row->artist,
            'title'  => $row->title,
            'num_plays' => $row->num_plays
        ));
    }

    echo json_encode($out);
endif;


exit;
