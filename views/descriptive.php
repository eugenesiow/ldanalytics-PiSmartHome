<div class="container-fluid">

    <div class="row">
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">
<!--                    <a href="#"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></a> -->
                    Temperature By Hour</div>
                <div class="panel-body">
                    <div class="input-group date date-selector" id="graph1">
                        <input type="text" class="form-control" value="19/07/2012"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    </div>
                </div>
            </div>
            <button id="add-graph-btn" type="button" class="btn btn-default">Add</button>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/nv.d3.min.js"></script>
<script>
    var graphList = [];
    var graphData = [];
    var graphCache = {};

    function setupButtons() {
        $('#add-graph-btn').on('click', function () {
            var newGraphId = 'graph'+(graphList.length+1);
            var panel = $('<div>',{'class':'panel panel-default'}).append($('<div>',{'text':'Temperature By Hour','class':'panel-heading'}));
            var dateSelect = $('<div>',{'class':'input-group date date-selector','id':newGraphId})
                .append('<input type="text" class="form-control" value="19/07/2012"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>');
            var panelBody = $('<div>',{'class':'panel-body'}).append(dateSelect);
            $(panel.append(panelBody)).insertBefore(this);
            graphList.push({'start':'2012-07-19T00:00:00','end':'2012-07-20T00:00:00','idName':newGraphId,'name':moment('2012-07-19').format('D MMM')});
            setupDatePicker();
            $('#'+newGraphId).datepicker('show');
            $('#'+newGraphId).datepicker().on('hide',function(){
                renderGraph();
                $('#'+newGraphId).datepicker().off('hide');
            });
        })
    }

    function setupDatePicker() {
        $('.date-selector').datepicker({
            format: "dd/mm/yyyy",
            startDate: "01/05/2012",
            endDate: "29/7/2012",
            autoclose: true
        }).on('changeDate', function(e){
            var d = e.date;
            var startDate = e.format('yyyy-mm-ddT00:00:00');
            var endDate = d.getFullYear() + "-" + ('0' + (d.getMonth() + 1)).slice(-2) + "-" + ('0' + (d.getDate()+1)).slice(-2) + "T00:00:00";

            for(var i in graphList) {
                if(graphList[i].idName == this.id) {
                    graphList[i].start = startDate;
                    graphList[i].end = endDate;
                    graphList[i].name = moment(e.format('yyyy-mm-dd')).format('D MMM');
                    break;
                }
            }
            renderGraph();
        });
    }

    $(document).ready(function() {
        graphList.push({'start':'2012-07-19T00:00:00','end':'2012-07-20T00:00:00','idName':'graph1','name':moment('2012-07-19').format('D MMM')});
        renderGraph();
        resizeGraph();
        setupButtons();
        setupDatePicker();
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
                LoadQuery(graphList[i].start, graphList[i].end, graphName);
            }
        }
    }

    function LoadQuery(startDate,endDate,name) {
        $.get('/query/temperature_by_hour?startDate='+encodeURIComponent(startDate)+'&endDate='+encodeURIComponent(endDate),function(data){
            if(data=="") {
                $('.loading-msg').text('Error!');
            }
            else {
                var resultSet = JSON.parse(data)['results']['bindings'];
                var temperature = [];
                for(var i in resultSet) {
                    temperature.push({x:resultSet[i]['hours']['value'],y:resultSet[i]['sval']['value']});
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

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if ((target == '#sparql')) {
            LoadSparql('temperature_by_hour');
        }
    });

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
        /*These lines are all chart setup.  Pick and choose which chart features you want to utilize. */
        nv.addGraph(function() {
            var chart = nv.models.lineChart()
                    .margin({left: 100})  //Adjust chart margins to give the x-axis some breathing room.
                    .useInteractiveGuideline(true)  //We want nice looking tooltips and a guideline!
                    .showLegend(true)       //Show the legend, allowing users to turn on/off line series.
                    .showYAxis(true)        //Show the y-axis
                    .showXAxis(true)        //Show the x-axis
                ;

            chart.xAxis     //Chart x-axis settings
                .axisLabel('Time (h)')
                .tickFormat(d3.format(',r'));

            chart.yAxis     //Chart y-axis settings
                .axisLabel('Temperature (deg F)')
                .tickFormat(d3.format('.02f'));

            d3.select('#graphcanvas')    //Select the <svg> element you want to render the chart in.
                .datum(data)         //Populate the <svg> element with chart data...
                .call(chart);          //Finally, render the chart!

            //Update the chart when window resizes.
            nv.utils.windowResize(function() { chart.update() });
            return chart;
        });
    }
</script>