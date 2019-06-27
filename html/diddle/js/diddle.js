var JsonPostRequest = function(url) {
    return {
        send : function(data, callback_success) {
            console.log('refreshing....');
            xhr = new XMLHttpRequest();
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-type", "application/json");
            xhr.onreadystatechange = function () { 
                if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                    var json = JSON.parse(xhr.responseText);
                    callback_success(json);
                    if (json.contents == '') {
                        console.error('### oops');
                        console.error(json, data);
                    }
                    //console.error('Changed from', raw_string);
                    raw_string = editor.getSession().getDocument().getValue();
                    //console.error('Changed to', raw_string);
                    document.getElementById('output').contentWindow.location.reload();
                    setTimeout(function(e) {
                        document.getElementById("ui-spinner-updating").classList.add("hidden");
                        document.getElementById("diddle-refresh").classList.remove("animate-refresh");
                    }, 1000);
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
    this.deltas = [];
}

Diddler.prototype.parse_editor_deltas = function (delta) {
    var start_row = delta.start.row;
    var end_row = delta.end.row;
    var actions = { 'insert' : '+', 'remove' : '-' };
    var d = {  '@' : actions[delta.action], 
        '<' : [ start_row, delta.start.column ], 
        '>' : [ end_row, delta.end.column ] };
    if (delta.action == 'insert') {
        d['$'] = delta.lines;
    } else if (delta.action == 'insert') {
        d['$'] = [];
        delta.lines.forEach(function(line) {
            d['$'].push(line.length);
        })
    }

    var last_delta = this.deltas.length > 0 ? this.deltas [ this.deltas.length - 1 ] : null;
    if (
        last_delta !== null &&
        delta.action == 'insert' && last_delta['@'] == '+' && 
        last_delta['>'][0] == start_row && last_delta['>'][1] == delta.start.column
    ) {
        console.log('---test');
        this.deltas.pop();
        last_delta['>'][0] = delta.end.row;
        last_delta['>'][1] = delta.end.column;
        var cursor_row = delta.start.row;
        
        var line = delta.lines.shift();
        if (line) {
            last_delta['$'][last_delta['$'].length - 1] = last_delta['$'][last_delta['$'].length - 1].concat(line);
        }
        delta.lines.forEach(function(line) {
            last_delta['$'].push(line);
        })

        this.deltas.push(last_delta);           
    } else if (
        last_delta !== null &&
        delta.action == 'remove' && last_delta['@'] == '-' && 
        last_delta['<'][0] == end_row && 
        last_delta['<'][1] == delta.end.column
    ) {
        this.deltas.pop();
        last_delta['<'][0] = start_row;
        last_delta['<'][1] = delta.start.column;
        this.deltas.push(last_delta);            
    } else {
        this.deltas.push ( d );
    }
    console.log('Deltas now:', this.deltas)
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
        _this.refresh_output();
    }, 1000); // pause 1second without any further changes before refresh
}

Diddler.prototype.refresh_output = function (manually_triggered) {
    manually_triggered = manually_triggered === true || false;
    document.getElementById("ui-spinner-updating").classList.remove("hidden");
    document.getElementById("diddle-refresh").classList.add("animate-refresh");
    
    var content = editor.getSession().getDocument().getValue();
    var _this = this;
    var params = { id: diddle_id };

    if (manually_triggered) {
        params.content_text = window.btoa(content);
    } else {
        params.deltas = this.deltas;
    }

    this.json_post.send(params, function(success) {
        _this.deltas = [];
        console.error('### OK ###', _this.deltas);
    });

    
}

