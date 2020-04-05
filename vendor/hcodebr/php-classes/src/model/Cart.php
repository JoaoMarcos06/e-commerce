<?php
namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\MailerPHP;
use \Hcode\Model\User;

class Cart extends Model { 
    
    const SESSION = "Cart";
    const SESSION_ERROR = "Cart_error";
    
    public static function getFromSession(){
        
        $cart = new Cart();
        
        if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION["Cart"]["idcart"] > 0){
            
            $cart->get((int)$_SESSION[Cart::SESSION]["idcart"]);
            
        }else{
            $cart->getFromSessionID();
            
            if(!(int) $cart->getidcart() > 0){
                
                $data = [
                    'dessessionid' => session_id()
                ];
                
                if(User::checkLogin(false) === true){
                    $user = User::getFromSession();
                    
                    $data['iduser'] = $user->getiduser();
                
                }
                
                $cart->setData($data);
                
                $cart->save();
                
                $cart->setToSession();
            }
        }
        
        return $cart;
    }
    
    public function get($idcart){
        
        $sql = new Sql();
        
        $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart",[
            ":idcart" => $idcart
        ]);
        
        if(count($results) > 0){        
            $this->setData($results[0]);
        }
    }
    
    public function getFromSessionID(){
        
        $sql = new Sql();
        
        $results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = : dessessionid",[
            ":dessessionid" => session_id()
        ]);
        
        if(count($results) > 0){
            $this->setData($results[0]);
        }
    }

    public function save(){
        
        $sql = new Sql();
        
        $results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)",[
            ":idcart" => $this->getidcart(),
            ":dessessionid" => $this->getdessessionid(),
            ":iduser" => $this->getiduser(),
            ":deszipcode" => $this->getdeszipcode(),
            ":vlfreight" => $this->getvlfreight(),
            ":nrdays" => $this->getnrdays()
        ]);
       
        $this->setData($results[0]);
        
    }
    
    public function setToSession(){
        $_SESSION[Cart::SESSION] = $this->getData();
    }
    
    public function addProduct(Product $product){
        
        $sql = new Sql();
        
        $sql->query("INSERT INTO tb_cartsproducts(idcart,idproduct) VALUES(:idcart, :idproduct)",[
            ":idcart" => $this->getidcart(),
            ":idproduct" => $product->getidproduct()
        ]);
        
        $this->getCalculateTotals();
           
    }
    
    public function removeProduct(Product $product, $all = false){
        
        $sql = new Sql();
        
        if($all){
            $sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL", array(
            ":idcart" => $this->getidcart(),
            ":idproduct" => $product->getidproduct()
        ));
        }else{
            $sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL LIMIT 1",[
            ":idcart" => $this->getidcart(),
            ":idproduct" => $product->getidproduct()
        ]);
        }
        
        $this->getCalculateTotal();
        
    }
                
    public function getProducts(){
        
        $sql = new Sql();
        
        $products = $sql->select("
            SELECT p.idproduct, p.desproduct, p.vlprice, p.vlwidth, p.vlheight, p.vllength,p.vlweight, p.desurl, COUNT(*) AS nrqtd, SUM(p.vlprice) as total 
            FROM tb_cartsproducts cp 
            INNER JOIN tb_products p ON cp.idproduct = p.idproduct 
            WHERE cp.idcart = :idcart AND cp.dtremoved IS NULL 
            GROUP BY p.idproduct, p.desproduct, p.vlprice,p.vlwidth, p.vlheight, p.vllength,p.vlweight,p.desurl 
            ORDER BY p.desproduct",[
                ":idcart" => $this->getidcart()
        ]);
        
        
          return Product::checklist($products);
        
    }
    
    
    public function getProductsTotals(){
        
        $sql = new Sql();
        
        $results = $sql->select("
                        SELECT SUM(p.vlwidth) vlwidth, SUM(p.vlheight) vlheight, SUM(p.vllength) vllength, SUM(p.vlweight) vlweight, SUM(vlprice) vlprice,COUNT(*) AS nrqtd
                        FROM tb_products p 
                        INNER JOIN tb_cartsproducts cp 
                        ON p.idproduct = cp.idproduct
                        WHERE cp.idcart = :idcart AND DTREMOVED IS NULL", [
                            ":idcart" => $this->getidcart()
                        ]);
        
        if(count($results) > 0){
            return $results[0];
        }else{
            return [];
        }
        
    }
    
    public function setFreight($zipcode){
        
        $zipcode = str_replace('-','',$zipcode);
        
        $totals = $this->getProductsTotals();
        
        
        if($totals['nrqtd'] > 0){
            
            if($totals["vlheight"] < 2) $totals["vlheight"] = 2;
            if($totals["vlheight"] < 105) $totals["vlheight"] = 105;
            
            if($totals["vllength"] < 16) $totals["vllength"] = 16;
            if($totals["vllength"] >105) $totals["vllength"] = 105;
            
            if($totals["vlwidth"] < 11) $totals["vlwidth"] = 16;
            if($totals["vlwidth"] >105) $totals["vlwidth"] = 105;
            
            if($totals["vlweight"] > 1) $totals["vlwight"] = 1;
            
            $params_query = http_build_query([
                "nCdEmpresa" => '',
                "sDsSenha" => '',
                "nCdServico" => '41106',
                "sCepOrigem" => '09853120',
                "sCepOrigem" => '09853120',
                "sCepDestino" => $zipcode,
                "nVlPeso" => $totals["vlweight"],
                "nCdFormato" => 1,
                "nVlComprimento" => $totals["vllength"],
                "nVlAltura" => $totals["vlheight"],
                "nVlLargura" => $totals["vlwidth"],
                "nVlDiametro" => '0',
                "sCdMaoPropria" => 'S',
                "nVlValorDeclarado" => '0',
                "sCdAvisoRecebimento" => 'S',
            ]);
            
            $xml = (object) simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$params_query);
            
            
            $result = $xml->Servicos->cServico;
            
            if($result->MsgErro != '')
                Cart::setMsgError($result->MsgErro);
            else
                Cart::clearMsgError();
                                
            
            $this->setnrdays($result->PrazoEntrega);
            $this->setvlfreight(Cart::formatValueToDecimal($result->Valor));
            $this->setdeszipcode($zipcode);

            $this->save();
            
            return $result;
            
        }else{
            
        }
        
    }
    
    public function updateFreight(){
        
        if($this->getdeszipcode() != ''){
            $this->setFreight($this->getdeszipcode());
        }
        
    }
    
    public static function formatValueToDecimal($value):float{
        
        $value = str_replace('.','',$value);
        return str_replace(',','.',$value);
        
    }
    
    public static function setMsgError($msg){
        
        $_SESSION[Cart::SESSION_ERROR] = $msg;
        
    }
    
    public static function getMsgError(){
        
        $msg =  (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : "";
        Cart::clearMsgError();
        
        return $msg;
    }
    
     public static function clearMsgError(){
        
         $_SESSION[Cart::SESSION_ERROR] = NULL;
        
    }
    
    public function getData(){
        
        $this->calculateTotal();

        return parent::getData();
    }
    
    public function calculateTotal(){
        
        $this->updateFreight();
        
        $totals = $this->getProductsTotals();
        
        $this->setvlsubtotal($totals["vlprice"]);
        $this->setvltotal($totals["vlprice"] + $this->getvlfreight());
        
    }

}

?>