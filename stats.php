<?php
/**
 * Date: 11/2/18
 * Time: 6:12 PM
 * scottybox - scottfleming
 *
 * Display total plays for an artist queried overall.
 */

require 'mongodb/vendor/autoload.php';
$artist             = $_GET['artist'];
$collection         = (new MongoDB\Client)->scottybox->pianobar;
$req                = $collection->find([]);
$globalPlaycount    = 0;
$out                = [];

// get total global count
foreach($req as $recrow){
 $globalPlaycount++;
}
// get artist data
$results            = $collection->find( [],
                                        ['projection' =>
                                            [
                                                'artist'        => 1,
                                                'num_plays'     => 1,
                                                'title'         => 1,
                                                'last_played'   => 1
                                            ] ]
                                    );
foreach( $results as $row ){
    $out[$row->artist]['plays'] += $row->num_plays;
}

//spew forth json barfola...
echo json_encode(array('globalPlays'=> $globalPlaycount, 'rating' => sprintf("%01.1f",($out[$artist]['plays']/$globalPlaycount*100)) , 'plays' => $out[$artist]['plays']));