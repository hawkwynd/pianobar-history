<?php
/**
 * Date: 10/29/18
 * Time: 4:26 PM
 * scottybox - sfleming
 * Youtube search client for displaying Youtube Links in getmaster.php
 *
 */

require_once __DIR__ . '/google-api-client/vendor/autoload.php';


if (isset($_GET['q']) && isset($_GET['maxResults'])) {

$query          = $_GET['q'];
$maxResults     = $_GET['maxResults'];

$DEVELOPER_KEY  = 'AIzaSyCGWMsm_DXXCFO8eIXHWJJQaznDyWQcicU';
$client         = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);

// Define an object that will be used to make all API requests.
$youtube        = new Google_Service_YouTube($client);

try {

    // Call the search.list method to retrieve results matching the specified
    // query term.

    $searchResponse = $youtube->search->listSearch('id, snippet', array(
        'q'          => $_GET['q'],
        'maxResults' => $_GET['maxResults'],
    ));

    $json       = [];
    $i = 0;

    // Add each result to the appropriate list, and then display the lists of
    // matching videos, channels, and playlists.

    foreach ($searchResponse['items'] as $searchResult) {

        switch ($searchResult['id']['kind']) {
            case 'youtube#video':
                array_push($json, array(
                        'videoId' => $searchResult['id']['videoId'],
                        'title'   => $searchResult['snippet']['title'],
                        'postDate'=> date('m-d-Y',strtotime($searchResult['snippet']['publishedAt'])),
                        'thumb'   => $searchResult['snippet']['thumbnails']['default']['url'],
                        'description'   => $searchResult['snippet']['description'],
                        'type'  => 'video'
                        )
                );
                break;

            case 'youtube#playlist':

                array_push($json, array('videoId' => $searchResult['id']['videoId'],
                        'title'   => $searchResult['snippet']['title'],
                        'postDate'=> date('m-d-Y',strtotime($searchResult['snippet']['publishedAt'])),
                        'thumb'   => $searchResult['snippet']['thumbnails']['default']['url'],
                        'description'   => $searchResult['snippet']['description'],
                        'type'  => 'playlist'
                    )
                );

                break;
        }
        $i++;
    }


} catch (Google_Service_Exception $e) {
    $htmlBody = sprintf('<p>A service error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
} catch (Google_Exception $e) {
    $htmlBody = sprintf('<p>An client error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
}
   echo json_encode($json, true);

}
exit;