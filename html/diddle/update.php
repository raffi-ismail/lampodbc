<?php
// error_reporting(E_ALL);
// ini_set('display_errors',1);
// ini_set('error_reporting', E_ALL);
// ini_set('display_startup_errors',1);


$data = file_get_contents('php://input');
$json = json_decode($data, true);
if (!$json) {
    header('content-type: text/plain', true, 403);
    print 'invalid data';
    exit;
}

include_once('common.php');
$sandbox = get_new_sandbox($json['id'], get_diddler());

if (!file_exists($sandbox->file)) {
    header('content-type: text/plain', true, 404);
    print "not found: {$sandbox->file}";
    exit;
}

if (!$sandbox->did_diddler_diddle() && !DEBUG_MODE) {
    header('content-type: text/plain', true, 403);
    print "You weren't the diddler for this Diddle";
    exit;
}


$textFile = new TextFile($sandbox->file);
if (isset($json['deltas'])) {
    $deltas = $json['deltas'];
    if (!empty($deltas)) {
        foreach($deltas as $delta) {
            list($start_row, $start_col) = $delta['<'];
            list($end_row, $end_col) = $delta['>'];
            if ($delta['@'] == '+') {
                $textFile->insertText ($start_row, $start_col, $end_row, $end_col, $delta['$']);
            } else if ($delta['@'] == '-') {
                $textFile->deleteText ($start_row, $start_col, $end_row, $end_col);
            }
        }
    }
    $a = $textFile->getAllText();
    $s = $textFile->saveFile();
    $return = [
        'id' => $sandbox->id,
        'deltas' => $deltas,
        'content' => $a,
        's' => $s
    ];
    
} else {
    $file_contents = $textFile->getAllText();
    $content_text_raw = isset($json['content_text']) ? $json['content_text'] : false;
    $content_text = base64_decode($content_text_raw);
    if (!$content_text) {
        header('content-type: text/plain', true, 403);
        print 'invalid update';
        exit;    
    }
    try {
        $sandbox->update_code($content_text);
    } catch (Exception $ex) {
        header('content-type: text/plain', true, 403);
        print "An error occured while trying to update the Diddle: " . $ex->getMessage();
        exit;
    }
    $return = [
        'id' => $sandbox->id,
        'content' => $content_text,
        'checksum' => $sandbox->checksum,
        'chainsum' => $sandbox->chainsum,
        'original_content' => $file_contents,
    ];
}


header('content-type: application/json');
print json_encode($return);


