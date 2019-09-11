<?php

use \Hcode\Page;


$app->get('/', function() {
    
	$page = new Page();
    
    $page->setTpl('index');
});

$app->get("/category/:id",function($id){
    
    $category = new Category();
    
    $category->get((int)$id);
    
    $page = new Page();
    
    $page->setTpl("category",["category" => $category->getData(), "products" => '']);
    
    
});

?>