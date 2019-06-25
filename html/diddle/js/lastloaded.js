document.getElementById('link-url-copy').addEventListener('click', function(e) {
    var texturl = document.getElementById('text-url-copy');
    texturl.select();
    document.execCommand("copy")
    var elem = document.getElementById('nav-iconset')
    var classlist = elem.classList
    classlist.add("animated");
    classlist.add("bounce");
    setTimeout(function(e) {
        classlist.remove('animated');
        classlist.remove('bounce');
    }, 1000);
});

document.getElementById('diddle-password-set').addEventListener('click', function(e) {
    var elem = document.getElementById('nav-protectset');
    elem.classList.remove('hidden');
})
diddle-password-set