<?php

namespace Hcode;

use Rain\Tpl;

class Page{
    
    private $tpl;
    private $options = [];
    private $defaults = [
        "data" => [],
        "header" => true,
        "footer" => true
    ];
    
    public function __construct($opts = array(), $tpl_dir = "/views/"){
        
        $this->options = array_merge($this->defaults, $opts);
        
        $config = array(
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"]."/e-commerce".$tpl_dir,
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"]."/e-commerce/views-cache/",
            "debug" => false
        );
        
        
        Tpl::configure($config);
        
        $this->tpl = new Tpl;
        
        
        $this->setData($this->options["data"]);
        
        if($this->options["header"]=== true) $this->tpl->draw("header");
    }
    
    public function setTPL($name,$data = array(), $returnHtml = false){
        
        $this->setData($data);
        
        return $this->tpl->draw($name,$returnHtml);        
    }
    
    private function setData($data = array()){
        
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }
        
    }
    
    public function __destruct(){
        if($this->options["footer"]=== true) $this->tpl->draw("footer");
        
    }
    
    public function getTpl(){
        return $this->tpl;
    }
    
}


?>