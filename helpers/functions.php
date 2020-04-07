<?php

use \Hcode\Model\User;

function formatCurrency($value){
    if($value > 0)
        return number_format($value, 2, ",",".");
    
    return 0;
}

function checklogin($inadmin = true){
    
    return User::checkLogin($inadmin);
}

function getUserName(){
    
    $user = User::getFromSession();
    
    return $user->getdesperson();
    
}



?>