<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\MailerPHP;
use \Hcode\Model\Product;

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
        
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/e-commerce/views/includes/categories-menu.html",implode('',$html));
    }
    
    public function getProducts($related = true){
        
        $Sql = new Sql();
         
        if($related){
            return $Sql->select("
                SELECT * FROM tb_products WHERE idproduct IN (
                    SELECT p.idproduct 
                    FROM tb_products p 
                    INNER JOIN tb_productscategories pc ON p.idproduct = pc.idproduct 
                    WHERE pc.idcategory = :idcategory
                );",
                [":idcategory" => $this->getidcategory()]);
        }else{
            return $Sql->select("
                SELECT * FROM tb_products WHERE idproduct NOT IN (
                    SELECT p.idproduct 
                    FROM tb_products p 
                    INNER JOIN tb_productscategories pc ON p.idproduct = pc.idproduct 
                    WHERE pc.idcategory = :idcategory
                );",
                [":idcategory" => $this->getidcategory()]);
        }
        
        
    }
    
    public function addProduct(Product $product){
        
        $Sql = new Sql();
        
        var_dump($Sql->query("INSERT INTO tb_productscategories(idcategory, idproduct) VALUES (:idcategory, :idproduct)",[
            ":idcategory" => $this->getidcategory(),
            ":idproduct" => $product->getidproduct()
        ]));
        
    }
    
    public function removeProduct(Product $product){
        
        $Sql = new Sql();
        
        $Sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct",[
            ":idcategory" => $this->getidcategory(),
            ":idproduct" => $product->getidproduct()
        ]);
        
    }
}

?>