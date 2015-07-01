<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.5/js/bootstrap-select.min.js"></script>
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/nv.d3.min.js"></script>
<script src="/js/common.js"></script>
<script>
    var graphList = [];
    var graphData = [];
    var graphCache = {};
    var termNames = {
        'queryName':'temperature_by_day',
        'queryLabel':'Temperature By Day',
        'inName':'InternalTemperatureValue',
        'exName':'ExternalTemperatureValue',
        'inDes':'Internal Temperature',
        'exDes':'External Temperature',
        'inLabel':' (In)',
        'exLabel': ' (Ex)'};

    function addGraphPanel() {
        var newGraphId = 'graph'+(graphList.length+1);
        var panel = $('<div>',{'class':'panel panel-default'}).append($('<div>',{'text':termNames.queryLabel,'class':'panel-heading'}));
        var dateSelect = $('<div>',{'class':'input-group date date-selector','id':newGraphId})
            .append('<input type="text" class="form-control" value="July 2012"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>');
        var typeSelect =  $('<select>',{'class':'selectpicker','data-link-date':newGraphId,'data-width':'100%'})
            .append('<option>'+termNames.inDes+'</option><option>'+termNames.exDes+'</option>');
        var panelBody = $('<div>',{'class':'panel-body'}).append(dateSelect).append(typeSelect);
        $('#sidebar').append(panel.append(panelBody));
        graphList.push({'month':'07','sensorType':termNames.inName,'idName':newGraphId,'name':'July 2012'+termNames.inLabel});
        setupDatePicker();
        setupTypeSelect();
        return newGraphId;
    }

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
                    var label = termNames.inLabel;
                    if(graphList[i].sensorType==termNames.exName) {
                        label = termNames.exLabel;
                    }
                    graphList[i].name = e.format('MM yyyy')+label;
                    break;
                }
            }
            renderGraph();
        });
    }

    function setupTypeSelect() {
        $('.selectpicker').selectpicker().on('change',function() {
            var sensorType = termNames.inName;
            var nameType = termNames.inLabel;
            if($(this).val()==termNames.exDes) {
                sensorType = termNames.exName;
                nameType = termNames.exLabel;
            }
            var linkedDatePicker = $(this).attr('data-link-date');
            for(var i in graphList) {
                if(graphList[i].idName == linkedDatePicker) {
                    graphList[i].sensorType = sensorType;
                    graphList[i].name = moment($('#'+linkedDatePicker).datepicker('getDate')).format('MMMM YYYY') + nameType;
                    break;
                }
            }
            renderGraph();
        });
    }

    $(document).ready(function() {
        addGraphPanel();
        renderGraph();
        resizeGraph();
        setupTabs(termNames.queryName);
        setupWindow();
    });

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
                LoadQuery(graphList[i].month, graphList[i].sensorType, graphName);
            }
        }
    }

    function LoadQuery(month,sensorType,name) {
        var queryStoreUrl = 'query';
        if($('#datastore-select').val() == 'Through Web Observatory') {
            queryStoreUrl = 'querywo';
        }
        $.get('/'+queryStoreUrl+'/'+termNames.queryName+'?month='+encodeURIComponent(month)+'&type='+encodeURIComponent(sensorType),function(data){
            if(data=="") {
                $('.loading-msg').text('Error!');
            }
            else {
                var resultSet = JSON.parse(data)['results']['bindings'];
                var temperature = [];
                for(var i in resultSet) {
                    temperature.push({x:resultSet[i]['day']['value'],high:resultSet[i]['max']['value'],low:resultSet[i]['min']['value'],open:0,close:0});
                }
                temperature.sort(datumCompare);
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

    function LoadGraph(data) {
        d3.selectAll("svg > *").remove();
        /*These lines are all chart setup.  Pick and choose which chart features you want to utilize. */
        nv.addGraph(function() {
            var chart = nv.models.candlestickBarChart()
                .x(function(d) { return d['x'] })
                .y(function(d) { return d['high'] })
                .duration(250)
                .margin({left: 75, bottom: 50})
                .forceY([32,100]);
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