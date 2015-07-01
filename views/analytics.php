<div class="container-fluid">

    <div class="row">
        <div id="sidebar" class="col-md-2">
            <select id="datastore-select" class="selectpicker" data-width="100%" data-style="btn-info">
                <option data-store="direct">Query Pi Directly</option>
                <option data-store="wo">Through Web Observatory</option>
            </select><p></p>
        </div>
        <div class="col-md-10">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#viz" aria-controls="sparql" role="tab" data-toggle="tab">Viz</a></li>
                <li role="presentation"><a href="#sparql" aria-controls="sparql" role="tab" data-toggle="tab">SPARQL</a></li>
                <li role="presentation"><a href="#docs" aria-controls="docs" role="tab" data-toggle="tab">Docs</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="viz">
                    <svg id="graphcanvas"></svg>
                </div>
                <div role="tabpanel" class="tab-pane" id="sparql"><pre id="sparql-code" class="tab-pane-content"></pre></div>
                <div role="tabpanel" class="tab-pane" id="docs"><div id="docs-code" class="tab-pane-content"></div></div>
            </div>
        </div>
    </div>

</div><!-- /.container -->