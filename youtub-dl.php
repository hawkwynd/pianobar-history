<?php
/**
 * Date: 11/4/18
 * Time: 8:47 AM
 * scottybox - scottfleming
 */
# https://music.youtube.com/watch?v=WEQnzs8wl6E
$vidID      = $_GET['v'];
$url        = 'https://music.youtube.com/watch?v='.$vidID;
$template   = '/var/www/scottybox/html/pianobar/downloads/%(id)s.%(ext)s';
$string     = ('youtube-dl --config-location /home/ubuntu/.config/youtube-dl ' . escapeshellarg($url));

$descriptorspec = array(
    0 => array("pipe", "r"),  // stdin
    1 => array("pipe", "w"),  // stdout
    2 => array("pipe", "w"),  // stderr
);
$process = proc_open($string, $descriptorspec, $pipes);
$stdout  = stream_get_contents($pipes[1]);
fclose($pipes[1]);
$stderr = stream_get_contents($pipes[2]);
fclose($pipes[2]);
$ret = proc_close($process);

$output = array('status' => $ret, 'errors' => $stderr, 'url_orginal'=>$url, 'output' => $stdout, 'command' => $string);

foreach(preg_split("/((\r?\n)|(\r\n?))/", $output['output']) as $line){
    // do stuff with $line
    //echo $line;
    if (strpos($line, '[ffmpeg] Destination:') !== false){
        $destination    = str_replace('[ffmpeg] Destination: ', '', $line);
        $pathinfo       = pathinfo($destination);
        $filesize       = human_filesize(filesize($destination));
    ?>
    <div id="player-wrapper">
        <audio preload="auto" controls>
            <source src="downloads/<?=$pathinfo['basename']?>">
        </audio>
        <script src="js/jquery.js"></script>
        <script src="js/audioplayer.js"></script>
        <script>$( function() { $( 'audio' ).audioPlayer(); } );</script>
    </div>
    <div class="download">
        <i class="fas fa-cloud-download-alt"></i>
        <a download target="=_blank" href="downloads/<?=$pathinfo['basename']?>"><?=str_replace('_',' ', $pathinfo['filename'])?></a> <?=$filesize?>
    </div>
<?php
    } // ffmpg destination search
} // foreach

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}