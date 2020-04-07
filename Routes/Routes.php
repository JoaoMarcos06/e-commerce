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
        "error" => User::getMsgError()
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

?>