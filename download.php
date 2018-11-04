<?php
/**
 * Date: 11/4/18
 * Time: 3:21 PM
 * scottybox - scottfleming
 */

$fileName   = $_GET['file'];
$path       = 'downloads/';
$file       = $path.$fileName;

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}
