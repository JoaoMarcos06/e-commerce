<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\MailerPHP;

class User extends Model {
    
    const SESSION = "user";
    const SECRET = "SERCRETKEYCRYPTED";
    
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
    
    
    public static function getForgot($email){
        
        $sql = new Sql();
        
        $results = $sql->select("SELECT * FROM tb_persons p INNER JOIN tb_users u USING(idperson) WHERE p.desemail = :email", array(
            ":email" => $email));
        
        
        if(count($results) === 0 ){
            throw new \Exception("Não foi possível recuperar a senha ");
        }else{
            
            $data = $results[0];
            $recovery = $sql->select("CALL sp_userspasswordsrecoveries_create(:id,:desip)",array(
                ":id" => $data["iduser"],
                ":desip" => $_SERVER["REMOTE_ADDR"]
            ));
            
            if(count($recovery) === 0){
                throw new \Exception("Não foi possível recuperar a senha");
            }else{
                $dataRecovery = $recovery[0];
                
                $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));
                $link = "http://localhost/e-commerce/admin/forgot/reset?code=".$code;
                
                $mailer = new MailerPHP($data["desemail"], $data["desperson"], "Redefinir senha", "forgot" ,array(
                "name" => $data["desperson"],
                "link" => $link    
                ));
                
                $mailer->send();
                
                //return $data;
                
            }
        }
    }
    
    
    public static function validCodeReset($code){
                
        $idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB);
        
        
        $sql = new Sql();
        
        $results = $sql->select("SELECT 
                                *
                                FROM
                                    tb_userspasswordsrecoveries pr
                                        INNER JOIN
                                    tb_users u ON u.iduser = pr.iduser
                                        INNER JOIN
                                    tb_persons p ON p.idperson = u.idperson
                                WHERE 
                                 pr.idrecovery = :idrecovery
                                 AND pr.dtrecovery is null
                                 AND date_add(pr.dtregister, INTERVAL 1 HOUR) >= NOW()
                        ", array(":idrecovery" => $idrecovery));
        
        if(count($results) === 0){
            throw new \Exception("Não foi possível reucperar a senha");
        }else{
            return $results[0];
        }
        
        
    }
    
    
    public static function setForgotUser($idrecovery){
        
        $sql = new Sql();
        
        $sql->query("UPDATE tb_userpasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(":idrecovery" => $idrecovery));
        
        
    }
    
    public function setPassword($password){
        
        $sql = new Sql();
        
        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(":iduser" => $this->getiduser() , ":password" => $password));
        
    }
}



?>