<div class="container">

    <div class="page-header">
        <h1>Data Model</h1>
    </div>

    <p>Linked Data is a powerful way of presenting and publishing data. One way to think of Linked Data is as modular LEGO® blocks which fit together and can easily be used to build all sorts of structures.</p>
    <p>The Internet of Things should be about machines talking to connected machines. Linked Data is a powerful option for devices to be interoperable - in another words, to understand each other.</p>
    <p>The PiSmartHome follows Linked Data conventions making it compatible with other data sources on the <a href="http://semanticweb.org/">Semantic Web</a> and other devices that serve Linked Data.</p>
    <p>Data is modelled from the Smart Home sensors based on the <a href="http://purl.oclc.org/NET/ssnx/ssn">Semantic Sensor Network Ontology (SSN)</a>. A basic data model is described as follows.</p>
    <img src="/img/basic_ontology_ssn.png" class="img-responsive" alt="Data Model">
    <div class="caption">
        <p>Basic Data Model based on the <a href="http://purl.oclc.org/NET/ssnx/ssn">SSN</a></p>
    </div>
    <p>An extension to the SSN, the  <a href="http://purl.oclc.org/NET/iot">Semantic Internet of Things Ontology(IoT Ontology)</a>, has been designed to provide specific representations for sensors on Internet of Things devices. The PiSmartHome is a model for this ontology's development. An example of a environmental weather sensor in the Smart Home is visualised as follows.</p>
    <img src="/img/weather_ontology_iot.png" class="img-responsive" alt="Data Model">
    <div class="caption">
        <p>The <a href="http://purl.oclc.org/NET/iot">IoT Ontology</a> extends the SSN for modelling Smart Home data
    </div>

    <div class="page-header">
        <h1>Links</h1>
    </div>

    <div class="list-group">
        <a href="http://purl.oclc.org/NET/iot.owl" target="_blank" class="list-group-item">
            <h4 class="list-group-item-heading">Download IoT OWL Ontology</h4>
            <p class="list-group-item-text">The IoT Ontology available as an RDF/XML file.</p>
        </a>
        <a href="http://purl.oclc.org/NET/iot" target="_blank" class="list-group-item">
            <h4 class="list-group-item-heading">View IoT Ontology Documentation</h4>
            <p class="list-group-item-text">Documentation of the classes, object and data properties of the IoT Ontology and its subclassing from the OWL Time and SSN Ontologies.</p>
        </a>
        <a href="http://iot.ee.surrey.ac.uk/SSNValidation/" target="_blank" class="list-group-item">
            <h4 class="list-group-item-heading">SSN Ontology Validation Tool</h4>
            <p class="list-group-item-text">Kolozali, Sefki, Tarek Elsaleh, and Payam Barnaghi. <strong>A Validation Tool for the W3C SSN Ontology Based Sensory Semantic Knowledge.</strong></p>
            <p class="list-group-item-text">The IoT Ontology has passed the SSN Ontology validation provided as part of the EU funded CityPulse project.</p>
        </a>
    </div>

</div><!-- /.container -->