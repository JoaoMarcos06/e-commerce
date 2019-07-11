<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$sql = new DB\Sql();
    
    $users = $sql->select("SELECT * FROM users");
    
    echo json_encode($users);

});

$app->run();

 ?>