<?php
include_once('common.php');

$sandbox = null;
if (isset($_REQUEST['id'])) {
    $id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
    $sandbox = get_new_sandbox($id, get_diddler());
    if (!file_exists($sandbox->file)) {
        header('content-type: text/html', true, 404);
        print "Not found";
        exit;
    }
}
else if (isset($_COOKIE['last_diddle'])) {
    $id = $_COOKIE['last_diddle'];
    $sandbox = get_new_sandbox($id, get_diddler());
    if (!file_exists($sandbox->file)) {
        header('content-type: text/html', true, 404);
        print "Not found";
        exit;
    }
    header("location: /diddle/{$id}");
} else {
    $id = DiddleSandbox::get_new_id(); 
    setcookie('last_diddle', $id, time()+86400*999, '/diddle/', DIDDLER_DOMAIN, true, true);
    $sandbox = get_new_sandbox($id, get_diddler());
    $sandbox->init_assets();
    header("location: /diddle/{$id}");
    exit;
}

$raw_content = file_get_contents($sandbox->file);
