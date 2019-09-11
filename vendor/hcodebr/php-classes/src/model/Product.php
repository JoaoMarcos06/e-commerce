<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Product extends Model {
    
   

    public static function listAll(){
        
        $sql = new Sql();
        
        return $sql->select("SELECT * FROM tb_products ORDER BY desproduct ");
        
    }
    
    
    public function save(){
        
        
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct,:vlprice,:vlwidth,:vlheight,:vllength,:vlweight, :desurl)", array(
            ":idproduct" => $this->getidproduct(),
            ":desproduct" => $this->getdesproduct(),
            ":vlprice" => $this->getvlprice(),
            ":vlwidth" => $this->getvlwidth(),
            ":vlheight" => $this->getvlheight(),
            ":vllength" => $this->getvllength(),
            ":vlweight" => $this->getvlweight(),
            ":desurl" => $this->getdesurl()
        ));
        
        
        $this->setData($results[0]);
    }
    
    public function get($idproduct){
        
        $sql = new Sql();
        
         $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct",array(
            ":idproduct" => $idproduct
        ));
        
        
        $this->setData($results[0]);
    }
    
    public function delete(){
        
        $sql = new Sql();
        
        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct",[":idproduct"=> $this->getidproduct()]);
        
    }
    
    public function checkPhoto(){
        
        if(file_exists(
            $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
            "e-commerce".DIRECTORY_SEPARATOR.
            "assets".DIRECTORY_SEPARATOR.
            "arquivos".DIRECTORY_SEPARATOR.
            "products".DIRECTORY_SEPARATOR.
            $this->getidproduct()."jpg")){
            
          $url =  "/e-commerce/assets/arquivos/products/".$this->getidproduct()."jpg";
            
        }else{
            $url = "/e-commerce/assets/arquivos/default/product.jpg";
        }
            
        return $this->setdesphoto($url);
        
    }
    
    public function getData(){
        
        $this->checkPhoto();
        
        $values = parent::getData();
        
        return $values;
    }
    
    public function uploadPhoto($file){
        
        $ext = explode(".",$file["name"]);
        $ext = end($ext);
        
        switch($ext){
            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($file["tmp_name"]);
            break;
                
            case "gif":
                $image = imagecreatefromgif($file["tmp_name"]);                
            break;
                
            case "png":
                $image = imagecreatefrompng($file["tmp_name"]);
            break;
        }
        
        $dist = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
            "e-commerce".DIRECTORY_SEPARATOR.
            "assets".DIRECTORY_SEPARATOR.
            "arquivos".DIRECTORY_SEPARATOR.
            "products".DIRECTORY_SEPARATOR.
            $this->getidproduct()."jpg";
        
        imagejpeg($image,$dist);
        
        imagedestroy($image);
        
        $this->checkPhoto();
    }
    

}

?>