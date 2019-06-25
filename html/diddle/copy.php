<?php
include_once('common.php');
print DIDDLE_ID;
$sandbox = new DiddleSandbox (DIDDLE_ID, get_diddler());
$new_sandbox = new DiddleSandbox (DiddleSandbox::get_new_id(), get_diddler());
$new_sandbox->init_assets();

$old_fs_stream = fopen($sandbox->file, 'r');
$new_fs_stream = fopen($new_sandbox->file, 'w');
var_dump($sandbox->file, $new_sandbox->file);
rewind($old_fs_stream);
stream_copy_to_stream($old_fs_stream, $new_fs_stream);
$f = file_get_contents($sandbox->file);
header("location: /diddle/{$new_sandbox->id}");


