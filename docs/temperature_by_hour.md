##Getting Temperature By Hour
---

The query aggregates either the average internal or external temperature in the Smart Home by hour on a particular day.

###Input Variables
Name | Type | Example
--- | --- | ---
type | enum | InternalTemperatureValue / ExternalTemperatureValue
startDate | xsdDateTime | 2012-07-19T00:00:00
endDate | xsdDateTime | 2012-07-20T00:00:00

###Algebra
1. `quadpattern` is the join operator between the individual `quad` graph patterns. The subject, object and predicate follow the Basic Graph Pattern style while the _Graph_ is the default _Graph_ or _Context_, identified here as `<urn:x-arq:DefaultGraphNode>`.
2. A `filter` is performed on the date variable between the _startDate_ and _endDate_.
3. The aggregation, `GROUP` defined in SPARQL by the _GROUP BY_ clause is then performed. The function hours is applied to the `?date` to extract hour of the day to aggregate by. `?val`s are averaged by the `avg` function. `?.0` refers, in [ARQ Algebra], to elements of that list with position identifiers. This explains the form of `?.n` and this works because SPARQL variables are not permitted to have a leading '.'. The [extend] operator then extends the solution, mapping the average `?val` to `?sval` for the project operation.
4. `project` is similar to a RDBMS project operation and is defined in the _select_ part of the SPARQL query.

The actual optimised ARQ Algebra is shown below:
```sparql
  (project (?sval ?hours)
    (extend ((?sval ?.0))
      (group ((?hours (hours (<http://www.w3.org/2001/XMLSchema#dateTime> ?date)))) ((?.0 (avg ?val)))
        (filter (exprlist (> ?date "2012-07-19T00:00:00"^^<http://www.w3.org/2001/XMLSchema#dateTime>) (< ?date "2012-07-20T00:00:00"^^<http://www.w3.org/2001/XMLSchema#dateTime>))
          (quadpattern
            (quad <urn:x-arq:DefaultGraphNode> ?instant <http://www.w3.org/2006/time#inXSDDateTime> ?date)
            (quad <urn:x-arq:DefaultGraphNode> ?obs <http://purl.oclc.org/NET/ssnx/ssn#observationSamplingTime> ?instant)
            (quad <urn:x-arq:DefaultGraphNode> ?obs <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <http://iot.soton.ac.uk/smarthome/sensor#environmental1>)
            (quad <urn:x-arq:DefaultGraphNode> ?obs <http://purl.oclc.org/NET/ssnx/ssn#observationResult> ?snout)
            (quad <urn:x-arq:DefaultGraphNode> ?snout <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?obsval)
            (quad <urn:x-arq:DefaultGraphNode> ?obsval <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://purl.oclc.org/NET/iot#InternalTemperatureValue>)
            (quad <urn:x-arq:DefaultGraphNode> ?obsval <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?val)
          )))))
```



[extend]:http://www.w3.org/TR/sparql11-query/#sparqlAlgebra
[ARQ Algebra]:http://www.w3.org/2011/09/SparqlAlgebra/ARQalgebra