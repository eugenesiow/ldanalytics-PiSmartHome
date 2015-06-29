<?php
require 'flight/Flight.php';
require 'query.php';

Flight::set('pageList',array('Home'=>'/',
    'Information'=>array('Datasets'=>'/info/dataset'),
    'Descriptive'=>array('Temperature By Hour'=>'/analytics/descriptive/temphour','Temperature By Day'=>'/analytics/descriptive/tempday'),
    'Discovery'=>'/analytics/discovery/home','Predictive'=>'/analytics/predictive/home'));

Flight::route('/query/@type', 'query');

Flight::route('/sparql/@type', 'sparql');

Flight::route('/', function(){
    Flight::render('header', array('activePage' => 'Home'), 'header_content');
    Flight::render('home', array(), 'body_content');
    return true;
});

Flight::route('/analytics/@class/@type', function($class,$type){
    Flight::render('header', array('activePage' => ucfirst($class)), 'header_content');
    Flight::render($class.'-'.$type, array(), 'body_content');
    return true;
});

Flight::route('/info/@pageName', function($pageName){
    Flight::render('header', array('activePage' => 'Information'), 'header_content');
    Flight::render('info/'.$pageName, array(), 'body_content');
    return true;
});

Flight::route('*', function(){
    Flight::render('layout', array('title' => 'PiSmartHome'));
});

Flight::start();
?>
