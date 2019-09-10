<?php 
session_start();
require_once("vendor/autoload.php");



use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

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

$app->get("/admin/forgot", function(){
   
    $page = new Page([
        "header" => false,
        "footer" =>false
    ],'/views/admin/');
    
    $page->setTpl('forgot/forgot');
    
    
});

$app->post("/admin/forgot", function(){
    
    User::getForgot($_POST["email"]);
    
    header("Location: /e-commerce/admin/forgot/sent");
    exit;
    
});

$app->get("/admin/forgot/sent", function(){
   
    $page = new Page([
        "header" => false,
        "footer" =>false
    ],'/views/admin/');
    
    $page->setTpl('forgot/forgot-sent');
    
    
});

$app->get("/admin/forgot/reset", function(){
   
    
    $user = User::validCodeReset($_GET["code"]);
    
    $page = new Page([
        "header" => false,
        "footer" =>false
    ],'/views/admin/');
    
    $page->setTpl('forgot/forgot-reset',array( "name" => $user["desperson"], "code" => $_GET["code"]));
    
});

$app->post("/admin/forgot/reset", function(){
   
    $forgot = User::validCodeReset($_POST["code"]);
    
    User::setForgotUser($forgot["idrecovery"]);
    
    $user = new User();
    
    $user->get((int)$forgot["iduser"]);
    
    $password = password_hash($_POST["password"],PASSWORD_DEFAULT,["cost" => 12]);
    
    $user->setPassword($password);
    
    $page = new Page([
        "header" => false,
        "footer" =>false
    ],'/views/admin/');
    
    $page->setTpl('forgot/forgot-reset-success');
    
});

$app->get("/admin/categories", function(){
    
    $categories = Category::listAll();
    
    $page = new Page([],'/views/admin/');
    
    $page->setTpl("categories/categories",[
        "categories" => $categories
    ]);
});


$app->get("/admin/categories/create", function(){
   
    User::verifyLogin();
    
    $page = new Page([],'/views/admin/');
    
    $page->setTpl("categories/categories-create");
    
});

$app->post("/admin/categories/create", function(){
   
    User::verifyLogin();
    
    
    $category = new Category();
    
    $category->setData($_POST);
    
    $category->save();
    
    header("Location: /e-commerce/admin/categories");
    exit;
    
    
});

$app->get("/admin/categories/:idcategory/delete",function($idcategory){
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
    
    $category->delete();
    
    header("Location: /e-commerce/admin/categories");
    exit;
});

$app->get("/admin/categories/:idcategory",function($idcategory){
    
    User::verifyLogin();
    
    $category = new Category();
    $page = new Page([],"/views/admin/");
    
    $category->get((int)$idcategory);
    
    $page->setTpl("categories/categories-update",["category" => $category->getData()]);   
    
    
});

$app->post("/admin/categories/:idcategory",function($idcategory){
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
    
    $category->setData($_POST);
    
    $category->save();
    
    header("Location: /e-commerce/admin/categories");
    exit;
    
    
});

$app->get("/category/:id",function($id){
    
    $category = new Category();
    
    $category->get((int)$id);
    
    $page = new Page();
    
    $page->setTpl("category",["category" => $category->getData(), "products" => '']);
    
    
});


$app->run();

 ?>