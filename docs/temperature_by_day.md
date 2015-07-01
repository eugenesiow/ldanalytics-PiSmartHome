##Getting Temperature By Day
---

The query aggregates the minimum and maximum internal or external temperature in the Smart Home each day for a particular month.

###Input Variables
Name | Type | Example
--- | --- | ---
type | enum | InternalTemperatureValue / ExternalTemperatureValue
month | xsdDateTime | translated to a range of xsdDateTime

###Algebra
1. `quadpattern` is the join operator between the individual `quad` graph patterns. The subject, object and predicate follow the Basic Graph Pattern style while the _Graph_ is the default _Graph_ or _Context_, identified here as `<urn:x-arq:DefaultGraphNode>`.
2. A `filter` is performed on the date variable between the start and end of the month in xsdDateTime.
3. The aggregation, `GROUP` defined in SPARQL by the _GROUP BY_ clause is then performed. The function day is applied to the `?date` to extract the day of the month to aggregate by. Min and Max `?val`s are identified by the `min` and `max` functions. `?.0` and `?.1` refer, in [ARQ Algebra], to elements of that list with position identifiers. This explains the form of `?.n` and this works because SPARQL variables are not permitted to have a leading '.'. The [extend] operator then extends the solution, mapping the min and max `?val`s to `?min` and `?max` for the project operation.
4. `project` is similar to a RDBMS project operation and is defined in the _select_ part of the SPARQL query.

The actual optimised ARQ Algebra is shown below:
```sparql
  (project (?max ?min ?day)
    (extend ((?max ?.0) (?min ?.1))
      (group ((?day (day (<http://www.w3.org/2001/XMLSchema#dateTime> ?date)))) ((?.0 (max ?val)) (?.1 (min ?val)))
        (filter (exprlist (> ?date "2012-07-01T00:00:00"^^<http://www.w3.org/2001/XMLSchema#dateTime>) (< ?date "2012-07-30T00:00:00"^^<http://www.w3.org/2001/XMLSchema#dateTime>))
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