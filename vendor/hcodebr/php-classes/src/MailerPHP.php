<?php

namespace Hcode;

use Rain\Tpl;

class MailerPHP { 
    
    const USERNAME = "YOUR@EMAIL.COM";
    const PASSWORD = "PASSWORD";
    const NAME = "YOUR NAME";
    
    private $mail;

    public function __construct($address, $name,$subject, $tplName, $data = array()){
        
        $config = array(
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"]."/e-commerce/views/email/",
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"]."/e-commerce/views-cache/",
            "debug" => false
        );
        
        
        Tpl::configure($config);
        
        $tpl = new Tpl;
        
        foreach($data as $key => $value){
            
            $tpl->assign($key,$value);
            
        }
        
        $html = $tpl->draw($tplName, true);
        
        
        $this->mail = new \PHPMailer\PHPMailer\PHPMailer;

        $this->mail->isSMTP();

        $this->mail->SMTPDebug = 0;

        $this->mail->Host = 'smtp.gmail.com';

        $this->mail->Port = 587;

        $this->mail->SMTPSecure = 'tls';

        $this->mail->SMTPAuth = true;

        $this->mail->Username = MailerPHP::USERNAME;

        $this->mail->Password = MailerPHP::PASSWORD;

        $this->mail->setFrom(MailerPHP::USERNAME, MailerPHP::NAME);

        $this->mail->addAddress($address, $name);

        $this->mail->Subject = $subject;

        $this->mail->msgHTML($html);

        $this->mail->AltBody = 'TESTE DE ENVIO DE EMAIL DE PHP7';

        
    } 
    
    public function send(){
        
        return $this->mail->send();
        
    }

}

?>
