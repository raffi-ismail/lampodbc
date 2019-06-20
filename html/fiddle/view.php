<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors',1);
ini_set('html_errors', 0);
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

if (!$id) {
    header('content-type: text/plain', true, 404);
    print "invalid diddle";
    exit;
}

$path = 'sandbox/' . $id;
$url = dirname($_SERVER['SCRIPT_NAME']) . "/{$path}";
$file = __DIR__ . "/{$path}";
if (!file_exists($file)) {
    header('content-type: text/plain', true, 404);
    print "not found";
    exit;
}
header('content-type: text/plain');
include_once ($file);   

