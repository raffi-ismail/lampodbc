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
    // moved ini sets to .htaccess in each sandbox directory
    // ini_set('display_errors',1);
    // ini_set('error_reporting', E_ALL | E_NOTICE);
    // ini_set('display_startup_errors',1);
    // ini_set('html_errors', 0);
    // ini_set('allow_url_fopen', 0);

    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
    
    if (!$id) {
        header('content-type: text/plain', true, 404);
        print "invalid diddle";
        exit;
    }
    
    include_once('common.php');
    $sandbox = get_sandbox($id, get_diddler());

    if (!file_exists($sandbox->file)) {
        header('content-type: text/plain', true, 404);
        print "not found";
        exit;
    }
    ini_set('open_basedir', $sandbox->dir);

    header('content-type: text/plain');

    include_once ($sandbox->file);      
}

