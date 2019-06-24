<?php
include_once('common.php');
print DIDDLE_ID;
$sandbox = new DiddleSandbox (DIDDLE_ID, get_diddler());
$new_sandbox = new DiddleSandbox (DiddleSandbox::get_new_id(), get_diddler());
$new_sandbox->init_assets();

$old_fs_stream = fopen($sandbox->file, 'r');
$new_fs_stream = fopen($new_sandbox->file, 'w');
stream_copy_to_stream($old_fs_stream, $new_fs_stream);
fclose($old_fs_stream);
fclose($new_fs_stream);

header("location: /diddle/{$new_sandbox->id}");


