<?php
include_once('common.php');

$sandbox = null;
if (isset($_REQUEST['id'])) {
    $id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
    $sandbox = get_sandbox($id, get_diddler());
    if (!file_exists($sandbox->file)) {
        header('content-type: text/html', true, 404);
        print "Not found";
        exit;
    }
} else {
    $id = DiddleSandbox::get_new_id(); 
    $sandbox = get_sandbox($id, get_diddler());
    $sandbox->init_assets();
    header("location: /diddle/{$id}");
    exit;
}

$raw_content = file_get_contents($sandbox->file);