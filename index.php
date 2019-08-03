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

$app->get('/admin/users', function(){
   
    User::verifyLogin();
    
    $users = User::listALL();
    
    $page = new Page(array(),'/views/admin/');
    
    $page->setTpl('users/users', array(
        "users" => $users
    ));
    
});

$app->get('/admin/users/create', function(){
   
    User::verifyLogin();
    
    $page = new Page(array(),'/views/admin/');
    
    $page->setTpl('users/users-create');
    
});

$app->get('/admin/users/:id/delete', function($id){
    
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int)$id);
    
    $user->delete();
    
    header("Location: /e-commerce/admin/users");
    exit;
    
});

$app->get('/admin/users/:id', function($id){
   
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int) $id);
    
    $page = new Page(array(),'/views/admin/');
    
    $page->setTpl('users/users-update', array(
    "user" => $user->getData()
    ));
    
});

$app->post('/admin/users/create', function(){
    
    User::verifyLogin();
    
    $user = new User();
    
    $user->setData($_POST);
    
    $user->save();
    
    header("Location: /e-commerce/admin/users");
    exit;
    
    
});

$app->post('/admin/users/:id', function($id){
    
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int)$id);
    
    $user->setData($_POST);
    
    $user->update();
    
    header("Location: /e-commerce/admin/users");
    exit;
    
});



$app->run();

 ?>