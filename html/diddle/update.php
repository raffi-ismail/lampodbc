<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors',1);


$data = file_get_contents('php://input');
$json = json_decode($data, true);
if (!$json) {
    header('content-type: text/plain', true, 403);
    print 'invalid data';
    exit;
}

include_once('common.php');
$sandbox = get_sandbox($json['id'], get_diddler());

if (!file_exists($sandbox->file)) {
    header('content-type: text/plain', true, 404);
    print "not found";
    exit;
}

$file_contents = file_get_contents($sandbox->file);
$patch_text_raw = isset($json['patch_text']) ? $json['patch_text'] : false;
$content_text_raw = isset($json['content_text']) ? $json['content_text'] : false;
$patch_text = base64_decode($patch_text_raw);
$content_text = base64_decode($content_text_raw);

if (!$patch_text) {
    if (!$content_text) {
        header('content-type: text/plain', true, 403);
        print 'invalid update';
        exit;
    }
}

$patch = false;
$patched_text = false;

$new_text = $patched_text ? $patched_text : $content_text;

$sandbox->update_code($new_text);

$return = [
    'id' => $sandbox->id,
    'contents' => $new_text,
    'checksum' => $sandbox->checksum,
    'chainsum' => $sandbox->chainsum,
    'patch_applied' => json_decode(json_encode($patch), true),
    'patch_text' => $patch_text,
    'original_content' => $file_contents
];

header('content-type: application/json');
print json_encode($return);


