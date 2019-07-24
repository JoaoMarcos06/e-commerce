<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {
    
    const SESSION = "user";
    
    public static function login($user, $pass){
        
        $sql = new Sql();
        
        
        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ':LOGIN' => $user
        ));
        
        if(count($results) === 0){
            throw new \Exception("Usuário inexistente ou senha incorreta !!");
        }
        
        $data = $results[0];
        
        $user = new User();
            
           
        
        if(password_verify($pass, $data['despassword']) === true){
            
            $user = new User();
            
            $user->setData($data);
            
            $_SESSION[$user::SESSION] = $user->getData();
            
           return $user;
            
        }else{
            throw new \Exception("Usuário inexistente ou senha incorreta !!");
        }
        
    }
    
    public static function verifyLogin($inadmin = true){
        
        if(
            !isset($_SESSION[User::SESSION]) ||
            !$_SESSION[User::SESSION] ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0 ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
          ){
            header("Location: /e-commerce/admin/login");
            exit;
        }
        
    }
    
    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }
}



?>