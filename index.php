<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

$app = new Slim();

$app->config('debug', true);

require_once 'Routes/Routes.php';
require_once 'Routes/Routes-Admin.php';


$app->run();

 ?>