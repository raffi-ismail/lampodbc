<?php
include_once('inc_init_sandbox.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PHPDiddle</title>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/editor.css">
<link rel="stylesheet" type="text/css" href="css/loader.css">

</head>
<body>

<div id="editor"><?php print htmlspecialchars($raw_content); ?></div>
<div id="output-navbar">
    <div class="navbar-title-block">
        <img class="transparent-50 logo-php" src="images/logo-php.png">
        <span class="title">Diddle</span>
    </div>
    <div class="navbar-iconset">
        <a href="view.php?id=<?php print $id; ?>" target="_blank" alt="Open output in new window"><i class="glyphicon glyphicon-new-window"></i></a>
    </div>
    <div id="ui-spinner-updating" class="ui-actitivy-spinner hidden resize-50 lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
</div>
<div id="output-wrapper">
    <iframe id="output" src="view.php?id=<?php print $id; ?>"></iframe>
</div>
<div id="output-statusbar">
    <a href="https://github.com/raffi-ismail/lampodbc" target="_blank"><img class="logo logo-gh" src="images/logo-gh-cat.png"></a>
    <a href="https://hub.docker.com/r/chubbycat/lampodbc" target="_blank"><img class="logo logo-dh" src="images/logo-dh.png"></a>
</div>
<script src="js/lz-string.js" type="text/javascript" charset="utf-8"></script>
<script src="js/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="js/diff_match_patch.js" ></script>
<script src="js/diddle.js" ></script>
<script>
    var raw_string = `<?php echo $raw_content; ?>`;
    var diddle_id = '<?php echo $id; ?>';

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/solarized_dark");
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
