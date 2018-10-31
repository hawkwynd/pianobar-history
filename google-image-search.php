<?php
/**
 * Date: 10/28/18
 * Time: 1:51 PM
 * scottybox - scottfleming
 * List of filters : https://developers.google.com/custom-search/v1/cse/list
 *
 */

require 'google-image-search/vendor/autoload.php';
use odannyc\GoogleImageSearch\ImageSearch;

$searchTerms    = trim($_GET['q']);
$output         = [];

if($searchTerms){
    // fire off the instance
    ImageSearch::config()->apiKey('AIzaSyAgYn97chSoaLbqFuTt0T-sKcrvlRMCsW0');
    ImageSearch::config()->cx('000763343904251371927:bgfrihs__um');

    $myImage = ImageSearch::search($searchTerms, [

                                    'searchType'    => 'image',
                                    'imgSize'       => 'large',
                                    'imgType'       => 'photo',
                                    'num'           => 10,
                                    'exactTerms'    => $searchTerms
                                ]);

    foreach($myImage['items'] as $image){
            array_push(
                $output,
                array(
                    'link'      => $image['link'],
                    'thumb'     => $image['image']['thumbnailLink'],
                    'title'     => $image['title']
                )
            );
    }
    echo "<pre>";
    echo json_encode( $output ,true );
}
exit;
