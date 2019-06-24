<?php
include_once('common.php');
print DIDDLE_ID;
$sandbox = new DiddleSandbox (DIDDLE_ID, get_diddler());
$new_sandbox = new DiddleSandbox (DiddleSandbox::get_new_id(), get_diddler());
$new_sandbox->init_assets();
header("location: /diddle/{$new_sandbox->id}");


