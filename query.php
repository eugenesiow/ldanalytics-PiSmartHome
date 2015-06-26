<?php
function query($type){
    if(!is_null($type)) {
        //get parameter data
        $parameters = Flight::request()->query->getData();

        $url = 'http://localhost:3030/smarthome_tdb/query';
        $query_string = file_get_contents('queries/'.$type.'.txt');
        foreach($parameters as $key=>$value){
            $query_string = str_replace('{'.$key.'}',$value,$query_string);
        }

        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array (
            "Content-Type: application/sparql-query"
        ));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        echo $result;
    }
}

function sparql($type) {
    $query_string = file_get_contents('queries/'.$type.'.txt');
    echo $query_string;
}