<?php include_once('inc_init_sandbox.php'); ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="My Diddle">
    <meta property="og:description" content="A snippet of my code written in PHP">
    <meta property="og:url" content="https:////azurefiddle.com/diddle/<?php echo DIDDLE_ID; ?>">
    <title>PHP|Diddle</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/editor.css">
    <link rel="stylesheet" type="text/css" href="css/loader.css">
    <link rel="stylesheet" type="text/css" href="css/fancy.css">
</head>
<body>

<div id="output-navbar">
    <div class="navbar-set navbar-title-block">
        <img class="transparent-50 logo-php" src="images/logo-php.png">
        <div class="title fancy word"><span>D</span><span>i</span><span>d</span><span>d</span><span>L</span><span>e</span></div>
    </div>
    <div id="nav-iconset" class="navbar-set navbar-iconset">
        <a href="/diddle/n" target="_blank" title="New Diddle"><i class="glyphicon glyphicon-plus"></i></a>
        <a href="c/<?php print DIDDLE_ID; ?>" target="_blank" title="Clone this Diddle"><i class="glyphicon glyphicon-duplicate"></i></a>
        <a id="diddle-password-set" class="hidden" href="#" title="Set a password on this Diddle"><i class="glyphicon glyphicon-lock"></i></a>
        <a id="link-url-copy" href="#" title="Copy the URL for this Diddle"><i class="glyphicon glyphicon-link"></i></a>
        <a id="diddle-refresh" href="#" title="Refresh Diddle output"><i class="glyphicon glyphicon-refresh"></i></a>
        <a href="v/<?php print DIDDLE_ID; ?>" target="_blank" title="Open output in new window"><i class="glyphicon glyphicon-new-window"></i></a>
        <div class="pseudo-hidden"><input id="text-url-copy" type="text" value="<?php print get_uri_diddle_landing(); ?>"></div>
    </div>
    <div id="nav-protectset" class="navbar-set navbar-protectset hidden">
        Set a password on this Diddle
        <input id="param-fiddle-password" type="password">
        <input type="submit" value="OK">
    </div>
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
            <div id="ui-spinner-updating" class="hidden resize-45 ui-actitivy-spinner"><div class="lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
            <span class="manual-refresh">
                <input id="opt-manual-refresh" type="checkbox">&nbsp;Manual refresh
            </span>
            <a href="https://github.com/raffi-ismail/lampodbc" target="_blank"><img class="logo logo-gh" src="images/logo-gh-cat.png"></a>
            <a href="https://hub.docker.com/r/chubbycat/lampodbc" target="_blank"><img class="logo logo-dh" src="images/logo-dh.png"></a>
        </div>
    </div>
</div>
<script src="js/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="js/diff_match_patch.js" ></script>
<script src="js/diddle.js"></script>
<script src="js/fancy.js"></script>
<script>
    var raw_string = `<?php echo $raw_content; ?>`;
    var diddle_id = '<?php echo DIDDLE_ID; ?>';
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/solarized_dark");
    editor.getSession().setMode("ace/mode/php");
    editor.getSession().setUseWrapMode(true);

    const diddlerObject = new Diddler('update.php');

<?php if (!get_current_sandbox()->did_diddler_diddle()) { ?>
    diddlerObject.set_navbar_warning_notice(`Read-only Diddle. Clone 
    <a class="no-decorations text-yellow larger" href="c/<?php print DIDDLE_ID; ?>" target="_blank" title="Clone this Diddle">
    <i class="glyphicon glyphicon-duplicate"></i></a> to make changes.`);
<?php } ?>

<?php  if (get_current_sandbox()->did_diddler_diddle() || DEBUG_MODE) { ?>
    editor.on('change', function(delta) {
        if (!document.getElementById("opt-manual-refresh").checked) {
            diddlerObject.attempt_refresh_output();
        }
    });
<?php } else { ?>
    editor.setReadOnly(true);
<?php }  ?>
</script>
<script src="js/lastloaded.js"></script>
</body>
</html>
