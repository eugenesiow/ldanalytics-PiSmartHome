<?php
require 'flight/Flight.php';
require 'query.php';
require 'querywo.php';

Flight::set('pageList',array('Home'=>'/',
    'Information'=>array('Data Model'=>'/info/datamodel','Datasets'=>'/info/dataset'),
    'Descriptive'=>array('Temperature By Hour'=>'/analytics/descriptive/temphour','Temperature By Day'=>'/analytics/descriptive/tempday'),
    'Discovery'=>'/analytics/discovery/home','Predictive'=>'/analytics/predictive/home'));

Flight::route('/query/@type', 'query');

Flight::route('/querywo/@type', 'querywo');

Flight::route('/sparql/@type', 'sparql');

Flight::route('/docs/@type', 'docs');

Flight::route('/', function(){
    Flight::render('header', array('activePage' => 'Home'), 'header_content');
    Flight::render('home', array(), 'body_content');
    Flight::render('blank', array(), 'body_code');
    return true;
});

Flight::route('/analytics/@class/@type', function($class,$type){
    Flight::render('header', array('activePage' => ucfirst($class)), 'header_content');
    Flight::render('analytics', array(), 'body_content');
    Flight::render($class.'-'.$type, array(), 'body_code');
    return true;
});

Flight::route('/info/@pageName', function($pageName){
    Flight::render('header', array('activePage' => 'Information'), 'header_content');
    Flight::render('info/'.$pageName, array(), 'body_content');
    Flight::render('blank', array(), 'body_code');
    return true;
});

Flight::route('/iot', function(){
    Flight::render('iot/index.html');
});

Flight::route('/iot.owl', function(){
    Flight::redirect('iot/iot.owl');
});

Flight::route('*', function(){
    Flight::render('layout', array('title' => 'PiSmartHome'));
});

Flight::start();
?>
