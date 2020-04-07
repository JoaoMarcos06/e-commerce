<?php
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;


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

$app->get("/admin/categories/:idcategory/products",function($idcategory){
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
    
    $page = new Page([],"/views/admin/");
    
    $page->setTpl("categories/categories-products",[
            "category" => $category->getData(),
            "productsRelated" =>$category->getProducts(),
            "productsNotRelated" => $category->getProducts(false)
    ]);    
    
});

$app->get("/admin/categories/:idcategory/products/:idproduct/add",function($idcategory,$idproduct){
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    $category->addProduct($product);
    
    header("Location: /e-commerce/admin/categories");
    exit;
    
    
});

$app->get("/admin/categories/:idcategory/products/:idproduct/remove",function($idcategory,$idproduct){
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    $category->removeProduct($product);
    
    header("Location: /e-commerce/admin/categories");
    exit;
    
      
    
});

$app->get("/admin/products", function(){
    
    User::verifyLogin();
    
    $products = Product::listAll();
    
    $page = new Page([],'/views/admin/');
    
    $page->setTpl("products/products",["products" => $products]);
    
});

$app->get("/admin/products/create", function(){
    
    User::verifyLogin();
    
    
    $page = new Page([],'/views/admin/');
    
    $page->setTpl("products/products-create");
    
});

$app->post("/admin/products/create", function(){
    
    User::verifyLogin();
    
    $product = new Product();
    
    $product->setData($_POST);
    
    
    $product->save();
    
    
    header("Location: /e-commerce/admin/products");
    exit;
});

$app->get("/admin/products/:idproduct", function($idproduct){
    
    User::verifyLogin();
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    
    $page = new Page([],'/views/admin/');
    
    $page->setTpl("products/products-update",["product" => $product->getData()]);
    
});

$app->post("/admin/products/:idproduct", function($idproduct){
    
    User::verifyLogin();
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    $product->setData($_POST);
    
    $product->save();
    
   if(!empty($_FILES["file"])){
        $product->uploadPhoto($_FILES["file"]);
   }    
    header("Location: /e-commerce/admin/products");
    exit;

    
});

$app->get("/admin/products/:idproduct/delete", function($idproduct){
    
    User::verifyLogin();
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    
    $product->delete();
    
    header("Location: /e-commerce/admin/products");
    exit;
});




?>