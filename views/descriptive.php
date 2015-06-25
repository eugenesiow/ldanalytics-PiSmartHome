<div class="container-fluid">

    <div class="row">
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">Temperature By Hour</div>
                <div class="panel-body">
                    Panel content
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
                <div role="tabpanel" class="tab-pane" id="sparql">2</div>
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
<script src="/js/nv.d3.min.js"></script>
<script>
    $('#graphcanvas').height($(document).height()-120);

    $.get('/query/temperature_by_hour',function(data){
        var resultSet = JSON.parse(data)['results']['bindings'];
        var temperature = [];
        for(var i in resultSet) {
            temperature.push({x:resultSet[i]['hours']['value'],y:resultSet[i]['sval']['value']});
        }
        temperature.sort(temperatureCompare);
        LoadGraph([{
            values: temperature,      //values - represents the array of {x,y} data points
            key: 'Internal Temperature', //key  - the name of the series.
            color: '#ff7f0e'  //color - optional: choose your own line color.
        }]);
//        console.log(temperature)
    });

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