<?php

function getToken()
{
    $tokenUrl = 'http://webobservatory.soton.ac.uk/oauth/token';

    //open connection
    $ch = curl_init();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query(array(
            'grant_type' => 'password',
            'client_id' => 'CLIENTID',
            'client_secret' => 'CLIENTSECRET',
            'username' => 'USERNAME',
            'password' => 'PASSWORD')));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    return json_decode($result)->access_token;
}