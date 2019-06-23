<?php
function get_sandbox ($id) {
    return new DiddleSandbox($id);
}


// require_once('diff_match_patch/src/Utils.php');
// require_once('diff_match_patch/src/Match.php');
// require_once('diff_match_patch/src/Patch.php');
// require_once('diff_match_patch/src/DiffMatchPatch.php');
// require_once('diff_match_patch/src/PatchObject.php');
// require_once('diff_match_patch/src/Diff.php');
// require_once('diff_match_patch/src/DiffToolkit.php');
// use DiffMatchPatch\DiffMatchPatch;


require_once('classes/base62x.php');
class DiddleSandbox {
    public $id, $checksum, $chainsum, $path, $url, $dir, $file, $checksum_file;

    function __construct($id) {
        $this->id = $id;
        $this->path = 'sandbox/' . $id;
        $this->url = dirname($_SERVER['SCRIPT_NAME']) . "/{$this->path}";
        $this->dir = "/var/{$this->path}";
        $this->file = "{$this->dir}/code";
        $this->checksum = hash('sha256', $this->file);
        $this->checksum_file = "{$this->dir}/checksum";
    }

    function patch_code() {
        // $dmp = new DiffMatchPatch();
        // $patch = $dmp->patch_fromText($patch_text);
        // $patched = $dmp->patch_apply($patch, '');
        // if (count($patched) != 2) {    
        //     header('content-type: text/html', true, 403);
        //     print 'could not update at this time';
        //     exit;
        // }
        // $patched_text = preg_replace('/[\r|\n]/', "\n", $patched[0]);
        // $status = $patched[1];
        // if (!$status) {
        //     header('content-type: text/plain', true, 400);
        //     print "Oops! There was a problem.\n";
        //     print "Text: \n---------\n{$patched_text}\n---------\n";
        //     exit;
        // }    
    }

    function update_code($text = null, $input_stream = null) {
        $checksum_content = $this->checksum;
        $this->checksum = $checksum_patched = sha1($text);
        $this->chainsum = $chainsum = sha1("{$checksum_content}...{$checksum_patched}");
        
        $buffer_stream = fopen('php://temp', 'r+');
        $output_stream = fopen($this->file, 'w');
        
        ## No payloads for now
        /**
        $payload = [
            "<?php \$_ENV['_diddle_doc_id']='{$json['id']}';putenv('_diddle_doc_id={$json['id']}'); ?>",
            "<?php \$_ENV['_diddle_doc_checksum']='{$checksum_patched}';putenv('_diddle_checksum={$checksum_patched}'); ?>",
            "<?php \$_ENV['_diddle_doc_chainsum']='{$chainsum}';putenv('_diddle_chainsum={$chainsum}'); ?>",
        ];
        fwrite($buffer_stream, implode('', $payload));
        **/
        
        if ($input_stream) {
            stream_copy_to_stream($input_stream, $buffer_stream);
        } else {
            fwrite($buffer_stream, $text);
        }
        rewind($buffer_stream);
        stream_copy_to_stream($buffer_stream, $output_stream);
        fclose($buffer_stream);
        fclose($output_stream);
        $this->update_assets();        
    }

    function init_assets () {
        mkdir($this->dir);
        touch($this->file);
        $htaccess = "{$this->dir}/.htaccess";
        copy('defaults/htaccess_diddle', $htaccess);
        chmod($htaccess, 0400);
        $default_stream = fopen('defaults/php_diddle', 'r');
        $this->update_code(null, $default_stream);
        fclose($default_stream);
    }

    function update_assets () {
        $this->update_checksum();
    }

    protected function update_checksum () {
        $f = fopen($this->checksum_file, 'w');
        fwrite($f, sha1($this->file));
        fclose($f);
    }
}