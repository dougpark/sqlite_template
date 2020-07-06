// include another html file into this document
// danger: it runs synchronous
// requires jquery
// date: July 4, 2020
// reference: https: //stackoverflow.com/a/32674115
//
// put this in your main html file
// <div class="includeHtml" title="settings.html"></div>
//
// title contains the file you want to include
// must be in same domain
// include path to file if in sub-folder
//
// include this near the bottom of your main html file
// <script src="./includeHtml.js"></script>

$(".includeHtml").each(function () {
    var inc = $(this);
    $.get({
        url: inc.attr("title"),
        async: false
    }, function (data) {
        inc.replaceWith(data);
    });
});