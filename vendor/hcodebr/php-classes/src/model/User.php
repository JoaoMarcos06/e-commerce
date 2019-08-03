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
    
    public static function listAll(){
        
        $sql = new Sql();
        
        return $sql->select("SELECT * FROM tb_users u INNER JOIN tb_persons p USING(idperson) ORDER BY p.desperson ASC ");
        
    }
    
    public function save(){
        
        $sql = new Sql();
        
         return $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));
        
        
        
    }
    
    public function get($id){
        
        $sql = new Sql();
        
        $results = $sql->select("SELECT * FROM tb_users u INNER JOIN tb_persons p USING(idperson) WHERE u.iduser = :id ", array(
        ":id" => $id
        ));
        
        $this->setData($results[0]);        
        
    }
    
    public function update(){
        
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_usersupdate_save(:iduser , :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
            ":iduser" => $this->getiduser(),
            ":desperson" => $this->getdesperson(),
            ":deslogin" => $this->getdeslogin(),
            ":despassword" => $this->getdespassword(),
            ":desemail" => $this->getdesemail(),
            ":nrphone" => $this->getnrphone(),
            ":inadmin" => $this->getinadmin()
        ));
        
    }
    
    public function delete(){
        
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_users_delete(:iduser )",array(
            ":iduser" => $this->getiduser()
        ));
        
    }
}



?>