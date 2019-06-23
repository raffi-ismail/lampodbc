<?php
    if (isset($_REQUEST['id'])) {
        $id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
    } else {
        $random = bin2hex(random_bytes(4));
        $microtime = microtime(true) - 1557012600;
        $base = base_convert($microtime * 1000, 10, 16);
        $new_id = $random.$base;
        $id = gmp_strval ( gmp_init( "0x{$new_id}" ), 62 ); 
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
        header("location: /diddle/{$id}");
        exit;
    }
    $raw_content = file_get_contents($sandbox->file);
?>