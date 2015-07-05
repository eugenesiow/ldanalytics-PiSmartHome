<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.5/js/bootstrap-select.min.js"></script>
<script src="/js/bootstrap-datepicker.min.js"></script>
<!--<script src="/js/nv.d3.min.js"></script>-->
<script src="/js/common.js"></script>

<script>
    var termNames = {
        'queryName':'unattended_metering'};

    $(document).ready(function() {
//        addGraphPanel();
//        renderGraph();
//        resizeGraph();
        setupPage();
        setupTabs(termNames.queryName);
        setupWindow();
    });

    function setupPage() {
        $('#viz').append($('<div>').append('<h2>What metered device consumes power when it is unattended?</h2><hr>'));
        $('#viz').append($('<div>',{'id':'results-table'}));
        $('#graphcanvas').hide();
        LoadQuery("2012-07-19T00:00:00","2012-07-20T00:00:00");
    }

    function LoadQuery(startDate,endDate) {
        var queryStoreUrl = 'query';
        if($('#datastore-select').val() == 'Through Web Observatory') {
            queryStoreUrl = 'querywo';
        }
        $.get('/'+queryStoreUrl+'/'+termNames.queryName+'?startDate='+encodeURIComponent(startDate)+'&endDate='+encodeURIComponent(endDate),function(data){
            if(data=="") {
                $('.loading-msg').text('Error!');
            }
            else {
                var resultSet = JSON.parse(data)['results']['bindings'];
                var resultTable = [];
                for(var i in resultSet) {
                    var results = resultSet[i];
                    resultTable.push({
                        'Meter':results['name']['value'],
                        'Date':results['motiondate']['value'],
                        'Hour of Day':results['motionhours']['value'],
                        'Location':formatLocation(results['motionplatform']['value']),
                        'Power(Watts)':results['power']['value']
                    });
                }

                renderTable(resultTable);
            }
        });
    }

    function renderTable(resultsTable) {
        var table = $('<table>',{'class':'table table-striped table-nonfluid'});
        var headerRow = $('<tr>');
        if(resultsTable.length > 0) {
            for (var key in resultsTable[0]) {
                headerRow.append('<th>' + key + '</th>');
            }
        }
        table.append($('<thead>').append(headerRow));
        for (var i in resultsTable) {
            var row = $('<tr>');
            for(var key in resultsTable[i]) {
                row.append('<td>' + resultsTable[i][key] + '</td>');
            }
            table.append(row);
        }

        $('#results-table').append(table);
    }
</script>