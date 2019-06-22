<?php
    if (isset($_REQUEST['id'])) {
        $id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
    } else {
        $id = hash('sha256', random_bytes(128));
    }
    if (!$id) {
        header('content-type: text/html', true, 404);
        print "Not found";
        exit;
    }
    
    include_once('common.php');
    $sandbox = get_sandbox($id);
    
    if ($_REQUEST['id']) {
        if (!file_exists($sandbox->file)) {
            header('content-type: text/html', true, 404);
            print "Not found";
            exit;
        }
    } else {
        $sandbox->init_assets();
        header("location: {$_SERVER['SCRIPT_NAME']}?id={$id}");
        exit;
    }
    $raw_content = file_get_contents($sandbox->file);
?>