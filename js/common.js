/**
 * Created by Eugene on 01/07/2015.
 */
function LoadSparql(queryType) {
    $.get('/sparql/'+queryType,function(data){
        $('#sparql-code').text(data);
    });
}

function LoadDocs(docType) {
    $('#docs-code').load('/docs/'+docType);
}

function datumCompare(a,b) {
    if (a.x < b.x)
        return -1;
    if (a.x > b.x)
        return 1;
    return 0;
}

function setupWindow() {
    // window resize
    $(window).resize(function () {
        resizeGraph();
    });
}

function resizeGraph() {
    $('#graphcanvas').height($(document).height()-120);
}

function setupTabs(queryName) {
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if ((target == '#sparql')) {
            LoadSparql(queryName);
        } else if ((target == '#docs')) {
            LoadDocs(queryName);
        }
    });
}

function formatLocation(loc) {
    var replaced = loc.replace('http://iot.soton.ac.uk/smarthome/platform#','');
    return replaced.charAt(0).toUpperCase() + replaced.slice(1);
}