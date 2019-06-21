<?php
namespace SandboxedNamespace {
    function error_handler($errno, $errstr, $errfile, $errline) {
        $errfile = basename($errfile);
        print "\nWarning: {$errstr} ({$errno}) in {$errfile} on line {$errline}\n";
    }

    function set_error_handler($func) {
        if ($func == 'SandboxedNamespace\error_handler') {
            \set_error_handler($func);
        } else {
        }
    }
    
    set_error_handler('SandboxedNamespace\error_handler');

    
    error_reporting(E_ALL);

    ini_set('display_errors',1);
    ini_set('error_reporting', E_ALL);
    ini_set('display_startup_errors',1);
    ini_set('html_errors', 0);
    ini_set('allow_url_fopen', 0);

    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
    
    if (!$id) {
        header('content-type: text/plain', true, 404);
        print "invalid diddle";
        exit;
    }
    
    $path = 'sandbox/' . $id;
    $url = dirname($_SERVER['SCRIPT_NAME']) . "/{$path}";
    $dir = __DIR__ . "/{$path}";
    $file = "{$dir}/{$id}";
    if (!file_exists($file)) {
        header('content-type: text/plain', true, 404);
        print "not found";
        exit;
    }
    header('content-type: text/plain');

    include_once ($file);       
}



