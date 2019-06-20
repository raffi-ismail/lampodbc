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
    $file = __DIR__ . "/{$path}";
    if ($_REQUEST['id']) {
        if (!file_exists($file)) {
            header('content-type: text/html', true, 404);
            print "Not found";
            exit;
        }
    } else {
        touch($file);
        header("location: {$_SERVER['SCRIPT_NAME']}?id={$id}");
        exit;
    }
    $raw_content = file_get_contents($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ACE in Action</title>
<style type="text/css" media="screen">
    #editor { 
        position: absolute;
        top: 0;
        right: 50vw;
        bottom: 0;
        left: 0;
    }

    #output-wrapper {
        background-color:#eeeeee;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 50vw;
    }

    #output {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        border: 0;
        overflow-y: auto;
        overflow-x: hidden;
        word-wrap: break-word;
    }

    body {
        overflow: hidden;
    }
</style>

</head>
<body>

<div id="editor"><?php print htmlspecialchars($raw_content); ?></div>
 
<div id="output-wrapper">
    <iframe id="output" src="view.php?id=<?php print $id; ?>"></iframe>
</div>
<script src="js/lz-string.js" type="text/javascript" charset="utf-8"></script>
<script src="js/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="js/diff_match_patch.js" ></script>
<script src="js/diddle.js" ></script>
<script>
    var raw_string = `<?php echo $raw_content; ?>`;
    var diddle_id = '<?php echo $id; ?>';

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/terminal");
    editor.getSession().setMode("ace/mode/php");
    editor.getSession().setUseWrapMode(true);
    //editor.getSession().getAnnotations();

    const diddlerObject = new Diddler('update.php');
    editor.on('change', function(delta) {
        diddlerObject.attempt_refresh_output();
    });


</script>

</body>
</html>
