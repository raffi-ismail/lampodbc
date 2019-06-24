var JsonPostRequest = function(url) {
    return {
        send : function(data) {
            console.log('refreshing....');
            xhr = new XMLHttpRequest();
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-type", "application/json");
            xhr.onreadystatechange = function () { 
                if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                    var json = JSON.parse(xhr.responseText);
                    if (json.contents == '') {
                        console.error('### oops');
                        console.error(json, data);
                    }
                    console.error('Changed from', raw_string);
                    raw_string = editor.getSession().getDocument().getValue();
                    console.error('Changed to', raw_string);
                    document.getElementById('output').contentWindow.location.reload();
                    var element = document.getElementById("ui-spinner-updating");
                    element.classList.add("hidden");
                }
            }
            var data = JSON.stringify(data);
            xhr.send(data); 
        }
    }
}

const Diddler = function(script_path) {
    this.json_post = JsonPostRequest(script_path);
    this.refresh_keypress_timeout = null;
    this.script_path = script_path;
}

Diddler.prototype.set_navbar_warning_notice = function (message) {
    var element = document.getElementById("notice-warning");
    element.innerHTML = message;
}

Diddler.prototype.get_editor_syntax_errors = function () {
    return editor.getSession().getAnnotations().filter(annotation => { 
        return annotation.type == 'error' && annotation.text.indexOf('unexpected $EOF') == -1;
    });
}

Diddler.prototype.attempt_refresh_output = function () {
    var _this = this;
    if (this.refresh_keypress_timeout) {
        clearTimeout(this.refresh_keypress_timeout);
    }
    this.refresh_keypress_timeout = setTimeout(function() {
        var a = _this.get_editor_syntax_errors();
        if (a.length) return;
        var content = editor.getSession().getDocument().getValue();

        var element = document.getElementById("ui-spinner-updating");
        element.classList.remove("hidden");

        //*** not in use for now */
        //var dmp = new diff_match_patch();
        //var patch_text = dmp.patch_toText(dmp.patch_make(raw_string, content));    
        //if (content.length > patch_text.length) {
        if (false) { // not using patching for now
            var params = { id: diddle_id, patch_text: window.btoa(patch_text) };
            _this.json_post.send(params);    
        } else {
            var params = { id: diddle_id, content_text: window.btoa(content) };
            _this.json_post.send(params);
        }
    }, 1000); // pause 1second without any further changes before refresh
}


