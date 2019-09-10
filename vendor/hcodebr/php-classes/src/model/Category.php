<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\MailerPHP;

class Category extends Model {
    
   

    public static function listAll(){
        
        $sql = new Sql();
        
        return $sql->select("SELECT * FROM tb_categories ORDER BY descategory ");
        
    }
    
    
    public function save(){
        
        $sql = new Sql();
        
          $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",array(
            ":idcategory" => $this->getidcategory(),
            ":descategory" => $this->getdescategory()
        ));
        
        Category::updateFile();
        
        $this->setData($results[0]);
    }
    
    public function get($idcategory){
        
        $sql = new Sql();
        
         $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory",array(
            ":idcategory" => $idcategory
        ));
        
        
        $this->setData($results[0]);
    }
    
    public function delete(){
        
        $sql = new Sql();
        
        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",[":idcategory"=> $this->getidcategory()]);
        
        Category::updateFile();
    }
    
    public static function updateFile(){
        
        $categories = Category::listAll();
        
        $html = [];
        
        foreach($categories as $category){
            
            array_push($html,"<li><a href='/e-commerce/category/".$category["idcategory"]."'>". $category["descategory"] ."</a></li>");
           
        }
        
         var_dump($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."views/includes/categories-menu.html");
            
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/e-commerce/views/includes/categories-menu.html",implode('',$html));
    }
}

?>