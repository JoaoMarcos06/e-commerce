<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;


$app->get('/', function() {
    
    $products = Product::listAll();
    
	$page = new Page();
    
    $page->setTpl('index',["products" => Product::checklist($products)]);
});

$app->get("/category/:id",function($id){
    
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    
    $category = new Category();
    
    $category->get((int)$id);
    
    $pagination = $category->getProductsPage($page);
    
    $pages = [];
    
    for($i = 1; $i <= $pagination["pages"]; $i++ ){
        array_push($pages, [
            "link" => "/e-commerce/category/".$category->getidcategory()."?page=".$i,
            "page" => $i]);
        
    }  
    
    $page = new Page();
    
    
    $page->setTpl("category",[
        "category" => $category->getData(), 
        "products" => $pagination["data"],
        "pages" => $pages
    ]);
    
    
});

$app->get("/products", function(){
   
    $products = Product::listALl();
    
});

$app->get("/products/:desurl",function($desurl){
    
    $product = new Product();
    
    $product->getFromUrl($desurl);
    
    $page = new Page();
    
    $page->setTpl("product-detail",[
        "product" => $product->getData(),
        "categories" => $product->getCategories()
    ]);
    
    
});

$app->get("/cart", function(){
    
    $cart = Cart::getFromSession();
    
    $page = new Page();
    
    $page->setTpl("cart",[
        "cart" => $cart->getData(),
        "products" => $cart->getProducts(),
        "error" => Cart::getMsgError()
    ]);
    
});

$app->get("/cart/:idproduct/add", function($idproduct){
     
    $product = new Product();
    
    $product->get((int) $idproduct);
    
    $cart = Cart::getFromSession();
    
    $qtd = (isset($_GET["qtd"])) ? (int) $_GET["qtd"] : 1;
    
    for($i = 0; $qtd > $i; $i++){
      $cart->addProduct($product);  
    }
    
    header("Location: /e-commerce/cart");
    exit;
    
    
});

$app->get("/cart/:idproduct/remove", function($idproduct){
     
    $product = new Product();
    
    $product->get((int) $idproduct);
    
    $cart = Cart::getFromSession();
    
    $cart->removeProduct($product);
    
    header("Location: /e-commerce/cart");
    exit;
    
    
});


$app->get("/cart/:idproduct/all", function($idproduct){
     
    $product = new Product();
    
    $product->get((int) $idproduct);
    
    $cart = Cart::getFromSession();
    
    $cart->removeProduct($product, true);
    
    header("Location: /e-commerce/cart");
    exit;
    
    
});

$app->post("/cart/freight",function(){
    
    $cart = Cart::getFromSession();
    
    $cart->setFreight($_POST["zipcode"]);
    
    header("Location: /e-commerce/cart");
    exit;
    
    
});

$app->get("/checkout", function(){
    
    User::verifyLogin(false);
    
    $cart = Cart::getFromSession();
    
    $address = new Address();
    
    $page = new Page();
    
    $page->setTpl("checkout",[
        "cart" => $cart,
        "address" => $address,
    ]);
    
    
});

$app->get("/login", function(){
    
    
    $page = new Page();
    
    $page->setTpl("login",[
        "error" => User::getMsgError(),
        "errorregister" => User::getRegisterError(),
        "registervalues" => (isset($_SESSION["registervalues"])) ? $_SESSION["registervalues"] : ["name" => "","email" => "", "phone" => ""] 
    ]);
    
    
});

$app->post("/login", function(){
    
    try{
        User::login($_POST["login"],$_POST["password"]);
    }catch(Exception $e){
        User::setMsgError($e->getMessage());
    }
    
    header("Location: /e-commerce/checkout");
    exit; 
    
    
    
});


$app->get("/logout", function(){
    
    User::logout();
    
    header("Location: /e-commerce/login");
    exit;
    
});

$app->post("/register",function(){
    
    
    if(!isset($_POST["name"]) || $_POST["name"] == ""){
        User::setRegisterError("Preencha o seu nome");
        header("Location: /e-commerce/login");
        exit;
    }
    
    if(!isset($_POST["email"]) || $_POST["email"] == ""){
        User::setRegisterError("Preencha o seu email");
        header("Location: /e-commerce/login");
        exit;
    }
    
    if(User::checkLoginExists($_POST["email"]) === true){
        User::setRegisterError("Email já cadastrado");
        header("Location: /e-commerce/login");
        exit;
    }
    
    if(!isset($_POST["password"]) || $_POST["password"] == ""){
        User::setRegisterError("Preencha a senha");
        header("Location: /e-commerce/login");
        exit;
    }
    
    
    $user = new User();
    
    $user->setData([
        "inadmin" => 0,
        "deslogin" => $_POST["email"],
        "desperson" => $_POST["name"],
        "desemail" => $_POST["email"],
        "despassword" => $_POST["password"],
        "nrphone" => $_POST["phone"],
    ]);
    
    $user->save();
    
    User::login($_POST["email"], $_POST["password"]);
    
    header("Location: /e-commerce/checkout");
    exit;
    
});


$app->get("/forgot", function(){
   
    $page = new Page();
    
    $page->setTpl('forgot');
    
    
});

$app->post("/forgot", function(){
    
    User::getForgot($_POST["email"],false);
    
    header("Location: /e-commerce/forgot/sent");
    exit;
    
});

$app->get("/forgot/sent", function(){
   
    $page = new Page();
    
    $page->setTpl('forgot-sent');
    
    
});

$app->get("/forgot/reset", function(){
   
    
    $user = User::validCodeReset($_GET["code"]);
    
    $page = new Page();
    
    $page->setTpl('forgot-reset',array( "name" => $user["desperson"], "code" => $_GET["code"]));
    
});

$app->post("/forgot/reset", function(){
   
    $forgot = User::validCodeReset($_POST["code"]);
    
    User::setForgotUser($forgot["idrecovery"]);
    
    $user = new User();
    
    $user->get((int)$forgot["iduser"]);
    
    $password = password_hash($_POST["password"],PASSWORD_DEFAULT,["cost" => 12]);
    
    $user->setPassword($password);
    
    $page = new Page();
    
    $page->setTpl('forgot-reset-success');
    
});

?>