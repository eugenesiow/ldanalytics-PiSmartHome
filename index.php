<?php
require 'flight/Flight.php';
require 'query.php';

Flight::set('pageList',array('Home'=>'/','Descriptive'=>'/analytics/descriptive','Discovery'=>'/analytics/discovery','Predictive'=>'/analytics/predictive'));

Flight::route('/query/@type', 'query');

Flight::route('/', function(){
    Flight::render('header', array('activePage' => 'Home'), 'header_content');
    Flight::render('home', array(), 'body_content');
    return true;
});

Flight::route('/analytics/@pageName', function($pageName){
    Flight::render('header', array('activePage' => ucfirst($pageName)), 'header_content');
    Flight::render($pageName, array(), 'body_content');
    return true;
});

Flight::route('*', function(){
    Flight::render('layout', array('title' => 'PiSmartHome'));
});

Flight::start();
?>
