<?php
/**
 * Date: 11/1/18
 * Time: 8:18 PM
 * scottybox - scottfleming
 * Example code to use findOne and findOneAndUpdate as
 * well as getting the _id of a record.
 *
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'mongodb/vendor/autoload.php';

$collection = (new MongoDB\Client)->scottybox->pianobar;
$tz                 = 'America/Chicago';
$timestamp          = time();
$dt                 = new DateTime("now", new DateTimeZone($tz));  //first argument "must" be a string
$dt->setTimestamp($timestamp); //adjust the object to correct timestamp

$artist = 'Blondie';
$title  = 'One Way Or Another';

$results = $collection->findOne(
    ['$and'    =>   [
        ['artist'    => $artist],
        ['title'     => $title ]
    ]
    ]
);


#echo "first_played : ". $results->first_played !== null ? $results->first_played : 'first_played is null.<br/>';

/*
$results = $collection->findOneAndUpdate(
    ['$and'    =>   [
        ['artist'    => $artist],
        ['title'     => $title]
    ]
    ],
    [
        '$set'  => [
            'last_played'   => $dt->format('m-d-y g:i:s a'),
            'first_played'  => $find->first_played !== null ? $find->first_played : $dt->format('m-d-y g:i:s a'),
            'style'         => 'This is a test updated to see if we get our shit together.',
            'num_plays'     => intval($find->num_plays) > 0 ? (intval($find->num_plays)+1) : $find->num_plays
        ]
    ],
    ['upsert', true]
);
*/

// using _id as results for updates
$myOid = (string) $results->_id;
// $item = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID( $myOid )]);

echo "OID: ". $myOid. "<br/>";
echo "artist: " . $results->artist . "<br/>";
echo "title: " . $results->title . "<br/>";
echo "num_plays: " . $results->num_plays . "<br/>";
echo "last_played: " . $results->last_played . "<br/>"; // to be updated
echo "first_played: " . $results->first_played . "<br/>";
echo "style: " . $results->style;

echo "<pre>";
//print_r($results);




