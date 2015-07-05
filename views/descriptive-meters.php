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
        'queryName': 'total_meter_by_day',
        'queryLabel': 'Power Meters Per Device By Day'
    };

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
                    var label = termNames.inLabel;
                    if(graphList[i].sensorType==termNames.exName) {
                        label = termNames.exLabel;
                    }
                    graphList[i].name = moment(e.format('yyyy-mm-dd')).format('D MMM')+label;
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
            for(var i in graphList) {
                if(graphList[i].idName == $(this).attr('data-link-date')) {
                    graphList[i].sensorType = sensorType;
                    graphList[i].name = moment(graphList[i].start).format('D MMM') + nameType;
                    break;
                }
            }
            renderGraph();
        });
    }

    function addGraphPanel() {
        var newGraphId = 'graph'+(graphList.length+1);
        var panel = $('<div>',{'class':'panel panel-default'}).append($('<div>',{'text':termNames.queryLabel,'class':'panel-heading'}));
        var dateSelect = $('<div>',{'class':'input-group date date-selector','id':newGraphId})
            .append('<input type="text" class="form-control" value="19/07/2012"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>');
        var typeSelect =  $('<select>',{'class':'selectpicker','data-link-date':newGraphId,'data-width':'100%'})
            .append('<option>'+termNames.inDes+'</option><option>'+termNames.exDes+'</option>');
        var panelBody = $('<div>',{'class':'panel-body'}).append(dateSelect).append(typeSelect);
        $(panel.append(panelBody)).insertBefore($('#add-graph-btn'));
        graphList.push({'start':'2012-07-16T00:00:00','end':'2012-07-22T00:00:00','sensorType':termNames.inName,'idName':newGraphId,'name':moment('2012-07-19').format('D MMM')+termNames.inLabel});
        setupDatePicker();
        setupTypeSelect();
        return newGraphId;
    }

    $(document).ready(function() {
//        addGraphPanel();
        graphList.push({'start':'2012-07-14T00:00:00','end':'2012-07-21T00:00:00','idName':'graph1','name':'2012-07-19T00:00:00'+'2012-07-20T00:00:00'});
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
                LoadQuery(graphList[i].start, graphList[i].end, graphName);
            }
        }
    }

    function LoadQuery(startDate,endDate,name) {
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
                var rooms = {};

                for(var i in resultSet) {
                    var key = formatLocation(resultSet[i]['platform']['value']);
                    var cell = {x:moment(resultSet[i]['dateOnly']['value']),y:parseFloat(resultSet[i]['totalpower']['value'])};
                    if(key in rooms) {
                        rooms[key]['values'].push(cell);
                    } else {
                        rooms[key] = {
                            key: key,
                            values: [cell]
                        }
                    }
                }
                var graphObj = [];
                for(var key in rooms) {
                    rooms[key]['values'].sort(datumCompare);
                    graphObj.push(rooms[key]);
                }
                graphData.push(graphObj);
                graphCache[name] = graphObj;
                finaliseRender();
            }
        });
    }

    function finaliseRender() {
        //if this is the last callback returning
        if(graphData.length==graphList.length) {
            LoadGraph(graphData[0]);
            $('.loading-msg').remove();
            $('#graphcanvas').show();
        }
    }

    function LoadGraph(data) {
        d3.selectAll("svg > *").remove();

        nv.addGraph(function() {
            var chart = nv.models.multiBarChart()
                    .reduceXTicks(true)   //If 'false', every single x-axis tick label will be rendered.
                    .rotateLabels(0)      //Angle to rotate x-axis labels.
                    .showControls(false)   //Allow user to switch between 'Grouped' and 'Stacked' mode.
                    .groupSpacing(0.1)    //Distance between each group of bars.
                ;

            chart.xAxis
                .axisLabel('Time')
                .tickFormat(function(d) { return d3.time.format('%b %d (%a)')(new Date(d)); });

            chart.yAxis
                .axisLabel('Power (W)')
                .tickFormat(d3.format(',.1f'));

            d3.select('#graphcanvas')
                .datum(data)
                .call(chart);

            nv.utils.windowResize(chart.update);

            return chart;
        });
    }
</script>