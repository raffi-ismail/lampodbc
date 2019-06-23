<?php
define('DEBUG_MODE', $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');
define('DIDDLER_DOMAIN', $_SERVER['SERVER_NAME']);
define('DIDDLE_ID', isset($_REQUEST['id']) ? $_REQUEST['id'] : false);

$visitor =  null;
if (!isset($_COOKIE['v_id'])) {
    $visitor = new DiddleVisitor();
        setcookie('v_id', $visitor->id, time()+86400*999, '/diddle/', DIDDLER_DOMAIN, true, true);
} else {    
    $visitor_id = $_COOKIE['v_id'];
    $visitor = new DiddleVisitor($visitor_id);
}

function get_diddler () {
    global $visitor;
    return $visitor;
}

function get_new_sandbox ($id, $diddler) {
    return new DiddleSandbox($id, $diddler);
}

function get_current_sandbox () {
    global $sandbox;
    return $sandbox;
}


// require_once('diff_match_patch/src/Utils.php');
// require_once('diff_match_patch/src/Match.php');
// require_once('diff_match_patch/src/Patch.php');
// require_once('diff_match_patch/src/DiffMatchPatch.php');
// require_once('diff_match_patch/src/PatchObject.php');
// require_once('diff_match_patch/src/Diff.php');
// require_once('diff_match_patch/src/DiffToolkit.php');
// use DiffMatchPatch\DiffMatchPatch;

class DiddleVisitor {
    public $id, $salt, $hash;

    function __construct($id = null) {
        $this->id = $id ? $id : hash('sha256', random_bytes(256), true);
        $id = bin2hex($this->id);
        $salt = bin2hex($this->salt);
    }
}

class DiddleSandbox {
    public $id, $checksum, $chainsum, $path, $url, $dir, $file, $checksum_file, $diddler;

    function __construct($id, $diddler) {
        $this->id = $id;
        $this->diddler = $diddler;
        $this->path = 'sandbox/' . $id;
        $this->url = dirname($_SERVER['SCRIPT_NAME']) . "/{$this->path}";
        $this->dir = "/var/{$this->path}";
        $this->file = "{$this->dir}/code";
        $this->checksum = hash('sha256', $this->file);
        $this->checksum_file = "{$this->dir}/checksum";
        $this->diddler_file = "{$this->dir}/diddler";
    }

    static function get_new_id () {
        $random = bin2hex(random_bytes(4));
        $microtime = microtime(true) - 1557012600;
        $base = base_convert($microtime, 10, 16);
        $id = gmp_strval ( gmp_init( "0x{$random}{$base}" ), 62 );
        return $id;
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

    function get_diddler_data () {
        $fs_diddler = fopen($this->diddler_file, 'r');
        $diddle_data = fgets($fs_diddler);
        if (!$diddle_data) {
            return null;
        } 
        return json_decode($diddle_data, false);
    }

    function update_code($text = null, $input_stream = null) {
        if (!$this->did_diddler_diddle() && !DEBUG_MODE) {
            throw new Exception("Diddler didn't diddle this");
        }
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

    function did_diddler_diddle () {
        $json = $this->get_diddler_data();
        if (!$json) {
            return false;
        }
        $salt = hex2bin($json->salt);
        $hash = $json->hash;
        $diddler = get_diddler();
        $verify = $this->verify_password($hash, $salt);
        return $verify;
    }


    function get_password ($salt, $rounds = 100) {
        for ($n = 1; $n <= $rounds; $n++) {
            $hash = hash('sha1', "{$this->diddler->id}{$salt}", $n < $rounds ? true : false);
        }
        return $hash;
    }

    function get_password_hash ($salt, $rounds = 100) {
        $password = $this->get_password($salt);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        return $hash;
    }

    function verify_password ($hash, $salt, $rounds = 100) {
        $password = $this->get_password($salt);
        $v = password_verify($password, $hash);
        return $v;
    }

    function init_assets () {
        mkdir($this->dir);
        touch($this->file);
        $htaccess = "{$this->dir}/.htaccess";
        copy('defaults/htaccess_diddle', $htaccess);
        chmod($htaccess, 0400);

        $salt = hash('sha256', random_bytes(256), true);
        $hash = $this->get_password_hash($salt);
        $json = json_encode( [ 'salt' => bin2hex($salt), 'hash' => $hash ] );

        touch($this->diddler_file);
        $fsDiddler = fopen($this->diddler_file, 'w');
        fwrite($fsDiddler, $json); 
        fclose($fsDiddler);
        chmod($this->diddler_file, 0400);

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