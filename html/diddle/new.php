<?php
include_once('common.php');
$new_sandbox = new DiddleSandbox (DiddleSandbox::get_new_id(), get_diddler());
$new_sandbox->init_assets();
header("location: /diddle/{$new_sandbox->id}");


