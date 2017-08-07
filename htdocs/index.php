<?php
require_once 'autoload.php';
require 'MyPDO.php';
require 'Controller.php';
require 'DataGenController.php';

# Database Information #
Flight::register('db', 'MyPDO');

Flight::map('notFound', function(){
    Flight::redirect('/error');
});

Flight::map('error', function(Exception $ex){
    Flight::redirect('/error');
});

Flight::route('/demo', function() {
    Flight::render('demo');
});

Flight::route('/generate_data', function() {
    $controller = new DataGenController();
    $controller->generateData();

});

Flight::route('/generate_userdata', function() {
    $controller = new DataGenController();
    $controller->generateUserData();

});

# Routes #
Flight::route('/signup', function() {
    $controller = new Controller();
    $controller->displaySignUpPage();
});

Flight::route('/admin', function() {
    $controller = new Controller();
    $controller->displayMainPage();
});

Flight::route('/main(/@page(/@lastId))', function($page, $lastId) {
    $controller = new Controller();
    $controller->displayAllListings($page, $lastId);
});

Flight::route('/listing/@itemId', function($itemId) {
    $controller = new Controller();
    $controller->displayListing($itemId);
});

Flight::route('/createListing', function() {
    $controller = new Controller();
    $controller->displayCreateListing();
});

Flight::route('/bidding/@itemId', function($itemId) {
    $controller = new Controller();
    $controller->displayBiddingPage($itemId);
});

Flight::route('/selectBid/@itemId/@userId', function($itemId, $bidderId) {
    $controller = new Controller();
    $controller->selectBid($itemId, $bidderId);
});

Flight::route('/editListing/@itemId', function($itemId) {
    $controller = new Controller();
    $controller->displayEditListingPage($itemId);
});

Flight::route('/deleteListing/@itemId', function($itemId) {
    $controller = new Controller();
    $controller->deleteListing($itemId);
});

Flight::route('/user/@userId', function($userId) {
    $controller = new Controller();
    $controller->userProfile($userId);
});

Flight::route('GET /login', function() {
    $controller = new Controller();
    $controller->login();
});

Flight::route('POST /login', function() {
    $controller = new Controller();
    $controller->validateLogin();
});

Flight::route('/logout', function() {
    $controller = new Controller();
    $controller->logout();
});

Flight::route('/error', function() {
    Flight::render('error', array(), 'body_content');
    Flight::render('layout', array('title' => '404'));
});

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();
?>
