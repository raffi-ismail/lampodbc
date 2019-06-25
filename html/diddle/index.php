<?php
include_once('inc_init_sandbox.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta property="og:title" content="My Diddle">
<meta property="og:description" content="A snippet of my code written in PHP">
<meta property="og:url" content="https:////azurefiddle.com/diddle/<?php echo DIDDLE_ID; ?>">
<title>PHP|Diddle</title>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/editor.css">
<link rel="stylesheet" type="text/css" href="css/loader.css">

</head>
<body>

<div id="output-navbar">
    <div class="navbar-title-block">
        <img class="transparent-50 logo-php" src="images/logo-php.png">
        <span class="title">Diddle</span>
    </div>
    <div class="navbar-iconset">
        <a href="/diddle" target="_blank" title="New Diddle"><i class="glyphicon glyphicon-plus"></i></a>
        <a href="c/<?php print DIDDLE_ID; ?>" target="_blank" title="Clone this Diddle"><i class="glyphicon glyphicon-duplicate"></i></a>
        <a href="v/<?php print DIDDLE_ID; ?>" target="_blank" title="Open output in new window"><i class="glyphicon glyphicon-new-window"></i></a>
    </div>
    <div id="ui-spinner-updating" class="ui-actitivy-spinner hidden resize-50 lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
</div>
<div class="navbar-notices-wrapper">
    <div class="navbar-notices">
        <div id="notice-warning" class="notice-warning"></div>
    </div>
</div>
<div class="content-wrapper">
    <div id="editor"><?php print htmlspecialchars($raw_content); ?></div>
    <div id="output-wrapper">
        <iframe id="output" src="v/<?php print DIDDLE_ID; ?>"></iframe>
        <div id="output-statusbar">
            <a href="https://github.com/raffi-ismail/lampodbc" target="_blank"><img class="logo logo-gh" src="images/logo-gh-cat.png"></a>
            <a href="https://hub.docker.com/r/chubbycat/lampodbc" target="_blank"><img class="logo logo-dh" src="images/logo-dh.png"></a>
        </div>
    </div>
</div>
<script src="js/lz-string.js" type="text/javascript" charset="utf-8"></script>
<script src="js/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="js/diff_match_patch.js" ></script>
<script src="js/diddle.js" ></script>
<script>
    var raw_string = `<?php echo $raw_content; ?>`;
    var diddle_id = '<?php echo DIDDLE_ID; ?>';

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/solarized_dark");
    editor.getSession().setMode("ace/mode/php");
    editor.getSession().setUseWrapMode(true);

    const diddlerObject = new Diddler('update.php');

<?php 
if (!get_current_sandbox()->did_diddler_diddle()) { 
?>
    diddlerObject.set_navbar_warning_notice(`
    Viewing this Diddle in readonly mode. Clone 
    <a class="no-decorations text-yellow larger" href="c/<?php print DIDDLE_ID; ?>" target="_blank" title="Clone this Diddle"><i class="glyphicon glyphicon-duplicate"></i></a> 
    to make your own changes.
    `);
<?php 
}
?>

<?php 
if (get_current_sandbox()->did_diddler_diddle() || DEBUG_MODE) { 
?>
    editor.on('change', function(delta) {
        diddlerObject.attempt_refresh_output();
    });
<?php 
} else { 
?>
    editor.setReadOnly(true);
<?php 
} 
?>


</script>

</body>
</html>
