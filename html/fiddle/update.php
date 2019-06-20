<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors',1);

// require_once('diff_match_patch/src/Utils.php');
// require_once('diff_match_patch/src/Match.php');
// require_once('diff_match_patch/src/Patch.php');
// require_once('diff_match_patch/src/DiffMatchPatch.php');
// require_once('diff_match_patch/src/PatchObject.php');
// require_once('diff_match_patch/src/Diff.php');
// require_once('diff_match_patch/src/DiffToolkit.php');

$data = file_get_contents('php://input');
$json = json_decode($data, true);
if (!$json) {
    header('content-type: text/plain', true, 403);
    print 'invalid data';
    exit;
}

$path = 'sandbox/' . $json['id'];
$url = dirname($_SERVER['SCRIPT_NAME']) . "/{$path}";
$file = __DIR__ . "/{$path}";
if (!file_exists($file)) {
    header('content-type: text/plain', true, 404);
    print "not found";
    exit;
}

$file_contents = file_get_contents($file);
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
use DiffMatchPatch\DiffMatchPatch;
if (!$content_text) {    
    $dmp = new DiffMatchPatch();
    $patch = $dmp->patch_fromText($patch_text);
    $patched = $dmp->patch_apply($patch, '');
    if (count($patched) != 2) {    
        header('content-type: text/html', true, 403);
        print 'could not update at this time';
        exit;
    }
    $patched_text = preg_replace('/[\r|\n]/', "\n", $patched[0]);


    $status = $patched[1];
    if (!$status) {
        header('content-type: text/plain', true, 400);
        print "Oops! There was a problem.\n";
        print "Text: \n---------\n{$patched_text}\n---------\n";
        exit;
    }
}

$new_text = $patched_text ? $patched_text : $content_text;

$checksum_patched = sha1($new_text);
$checksum_content = sha1($file_contents);
$chainsum = sha1("{$checksum_content}{$checksum_patched}");

$buffer_stream = fopen('php://temp', 'r+');
$output_stream = fopen($file, 'w');

## No payloads for now
/**
$payload = [
    "<?php \$_ENV['_diddle_doc_id']='{$json['id']}';putenv('_diddle_doc_id={$json['id']}'); ?>",
    "<?php \$_ENV['_diddle_doc_checksum']='{$checksum_patched}';putenv('_diddle_checksum={$checksum_patched}'); ?>",
    "<?php \$_ENV['_diddle_doc_chainsum']='{$chainsum}';putenv('_diddle_chainsum={$chainsum}'); ?>",
];
fwrite($buffer_stream, implode('', $payload));
**/

fwrite($buffer_stream, $new_text);
rewind($buffer_stream);
stream_copy_to_stream($buffer_stream, $output_stream);
fclose($buffer_stream);
fclose($output_stream);

$return = [
    'id' => $json['id'],
    'contents' => $new_text,
    'checksum' => $checksum_patched,
    'chainsum' => $chainsum,
    'patch_applied' => json_decode(json_encode($patch), true),
    'patch_text' => $patch_text,
    'original_content' => $file_contents
];

header('content-type: application/json');
print json_encode($return);


