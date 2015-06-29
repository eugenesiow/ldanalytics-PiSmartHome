<div class="container-fluid">

    <div class="row">
        <div class="col-md-2">
            <select id="datastore-select" class="selectpicker" data-width="100%" data-style="btn-info">
                <option data-store="direct">Query Pi Directly</option>
                <option data-store="wo">Through Web Observatory</option>
            </select><p></p>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Temperature By Day</div>
                <div class="panel-body">
                    <div class="input-group date date-selector" id="graph1">
                        <input type="text" class="form-control" value="July 2012"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#viz" aria-controls="sparql" role="tab" data-toggle="tab">Viz</a></li>
                <li role="presentation"><a href="#sparql" aria-controls="sparql" role="tab" data-toggle="tab">SPARQL</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="viz">
                    <svg id="graphcanvas"></svg>
                </div>
                <div role="tabpanel" class="tab-pane" id="sparql"><pre id="sparql-code" class="tab-pane-content"></pre></div>
            </div>
        </div>
    </div>

</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.5/js/bootstrap-select.min.js"></script>
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/nv.d3.min.js"></script>
<script>
    var graphList = [];
    var graphData = [];
    var graphCache = {};

    function setupDatePicker() {
        $('.date-selector').datepicker({
            format: "MM yyyy",
            startDate: "05/2012",
            endDate: "07/2012",
            minViewMode: 1,
            autoclose: true
        }).on('changeDate', function(e){
            var month = e.format('mm');
            for(var i in graphList) {
                if(graphList[i].idName == this.id) {
                    graphList[i].month = month;
                    graphList[i].name = e.format('MM yyyy');
                    break;
                }
            }
            renderGraph();
        });
    }

    $(document).ready(function() {
        graphList.push({'month':'07','idName':'graph1','name':'July 2012'});
        renderGraph();
        resizeGraph();
        setupDatePicker();
        setupTabs();
    });

    // window resize
    $(window).resize(function() {
        resizeGraph();
    });

    function resizeGraph() {
        $('#graphcanvas').height($(document).height()-120);
    }

    function renderGraph() {
        $('#graphcanvas').hide();
        $('#viz').prepend('<span class="loading-msg tab-pane-content">Loading...</span>');
        graphData = [];
        for(var i in graphList) {
            var graphName = graphList[i].name;
            if(graphCache[graphName]) {
                graphData.push(graphCache[graphName]);
                finaliseRender();
            } else {
                LoadQuery(graphList[i].month, graphName);
            }
        }
    }

    function LoadQuery(month,name) {
        var queryStoreUrl = 'query';
        if($('#datastore-select').val() == 'Through Web Observatory') {
            queryStoreUrl = 'querywo';
        }
        $.get('/'+queryStoreUrl+'/temperature_by_day?month='+encodeURIComponent(month),function(data){
            if(data=="") {
                $('.loading-msg').text('Error!');
            }
            else {
                var resultSet = JSON.parse(data)['results']['bindings'];
                var temperature = [];
                for(var i in resultSet) {
                    temperature.push({x:resultSet[i]['day']['value'],high:resultSet[i]['max']['value'],low:resultSet[i]['min']['value'],open:0,close:0});
                }
                temperature.sort(temperatureCompare);
                var graphObj = {
                    values: temperature,      //values - represents the array of {x,y} data points
                    key: name, //key  - the name of the series.
//                    color: '#ff7f0e'  //color - optional: choose your own line color.
                };
                graphData.push(graphObj);
                graphCache[name] = graphObj;
                finaliseRender();
            }
        });
    }

    function finaliseRender() {
        //if this is the last callback returning
        if(graphData.length==graphList.length) {
            LoadGraph(graphData);
            $('.loading-msg').remove();
            $('#graphcanvas').show();
        }
    }

    function setupTabs() {
        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if ((target == '#sparql')) {
                LoadSparql('temperature_by_day');
            }
        });
    }

    function LoadSparql(queryType) {
        $.get('/sparql/'+queryType,function(data){
            $('#sparql-code').text(data);
        });
    }

    function temperatureCompare(a,b) {
        if (a.x < b.x)
            return -1;
        if (a.x > b.x)
            return 1;
        return 0;
    }

    function LoadGraph(data) {
        d3.selectAll("svg > *").remove();
        /*These lines are all chart setup.  Pick and choose which chart features you want to utilize. */
        nv.addGraph(function() {
            var chart = nv.models.candlestickBarChart()
                .x(function(d) { return d['x'] })
                .y(function(d) { return d['high'] })
                .duration(250)
                .margin({left: 75, bottom: 50})
                .forceY([62,90]);
            chart.interactiveLayer.tooltip.contentGenerator(function(data) {
                var dataObj = data['series'][0];
                return dataObj['data']['x'] + ' ' + dataObj['key'] + ' (' +  d3.format(',.1f')(dataObj['data']['low']) + '&#8457; - ' + d3.format(',.1f')(dataObj['data']['high']) + '&#8457;)';
            });
            // chart sub-models (ie. xAxis, yAxis, etc) when accessed directly, return themselves, not the parent chart, so need to chain separately
            chart.xAxis
                .axisLabel("Day of Month")
                .tickFormat(function(d) {
                    return d;
                });
            chart.yAxis
                .axisLabel('Temperature Range (deg F)')
                .tickFormat(function(d){ return d3.format(',.1f')(d); });
            d3.select('#graphcanvas')
                .datum(data)
                .transition().duration(500)
                .call(chart);
            nv.utils.windowResize(chart.update);
            return chart;
        });
    }
</script>