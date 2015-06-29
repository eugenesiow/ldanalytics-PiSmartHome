<?php
require 'token.php';

function querywo($type){
    if(!is_null($type)) {
        //get parameter data
        $parameters = Flight::request()->query->getData();

        $query_string = file_get_contents('queries/'.$type.'.txt');
        foreach($parameters as $key=>$value){
            $query_string = str_replace('{'.$key.'}',$value,$query_string);
        }
        $queryUrl = 'http://webobservatory.soton.ac.uk/api/wo/5591671c1be9bbf079e33983/endpoint?query='.urlencode($query_string);

        $accessToken = getToken();

        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $queryUrl);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array (
            "Authorization:Bearer ".$accessToken.""
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute get
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        echo $result;
    }
}