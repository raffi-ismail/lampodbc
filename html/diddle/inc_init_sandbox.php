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
    $path = 'sandbox/' . $id;
    $url = dirname($_SERVER['SCRIPT_NAME']) . "/{$path}";
    $dir = __DIR__ . "/{$path}";
    $file = "{$dir}/{$id}";
    if ($_REQUEST['id']) {
        if (!file_exists($file)) {
            header('content-type: text/html', true, 404);
            print "Not found";
            exit;
        }
    } else {
        mkdir($dir);
        touch($file);
        header("location: {$_SERVER['SCRIPT_NAME']}?id={$id}");
        exit;
    }
    $raw_content = file_get_contents($file);
?>