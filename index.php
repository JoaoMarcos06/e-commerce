<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
    
    $page->setTpl('index');
});

$app->get('/admin', function() {
    
    User::verifyLogin();
    
	$page = new Page(array(),'/views/admin/');
    
    $page->setTpl('index');
});


$app->get('/admin/login', function() {
    
	$page = new Page([
        "header" => false,
        "footer" =>false
    ],'/views/admin/');
    
    $page->setTpl('login');
});

$app->post('/admin/login',function(){
    
    User::login($_POST['user'],$_POST['pass']);
    header("Location: /e-commerce/admin");
    exit;
});

$app->get('/admin/logout',function(){
    User::logout();
    
    header("Location: /e-commerce/admin/login");
    exit;
});

$app->run();

 ?>