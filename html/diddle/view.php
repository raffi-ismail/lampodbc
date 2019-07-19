<?php
namespace SandboxedNamespace {
    include_once('common.php');    

    if (!DIDDLE_ID) {
        header('content-type: text/plain', true, 404);
        print "invalid diddle";
        exit;
    }

    function error_handler($errno, $errstr, $errfile, $errline) {
        $errfile = basename($errfile);
        print "\nWarning: {$errstr} ({$errno}) in {$errfile} on line {$errline}\n";
    }   \set_error_handler('SandboxedNamespace\error_handler');
    
    $sandbox = get_new_sandbox(DIDDLE_ID, get_diddler());

    if (!file_exists($sandbox->file)) {
        header('content-type: text/plain', true, 404);
        print "not found";
        exit;
    }

    $R_ENV = json_decode(file_get_contents(WWW_DIR . '/env.json'), true);
    $O_SERVER = [
        'SCRIPT_NAME' => '/diddle/v/' . DIDDLE_ID,
        'PHP_SELF' => '/diddle/v/' . DIDDLE_ID,
        'SCRIPT_FILENAME' => $sandbox->file
    ];
    if ($R_ENV) {
        $_SERVER = array_diff_key($_SERVER, $R_ENV);
        $_SERVER = array_merge($_SERVER, $O_SERVER);
        $_ENV = [];
    }
    
    define ('__DIDDLE_FILE__', $sandbox->file);
    define ('__DIDDLE_DIR__', $sandbox->dir);
    unset($R_ENV, $O_SERVER, $sandbox);

    ini_set('open_basedir', __DIDDLE_DIR__);
    
    header('content-type: text/plain');
    print file_get_contents (__DIDDLE_FILE__);
}