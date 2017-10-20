<?php

namespace Mail;

use Application\Config;

class Mailer {

	private $mail;

	public function setUp() {
		$this->mail = new PHPMailer;             
	
	    $this->mail->SMTPAuth = true;                       
	    $this->mail->SMTPSecure = Config::SECURE_SSL;                   
	    $this->mail->Host = Config::HOST_MAILER;
	    $this->mail->Port = Config::MAIL_PORT;                             
	    $this->mail->CharSet = Config::CHARSET;                     
	    $this->mail->Encoding = Config::ENCODING;
	    // $this->mail->AuthType = 'CRAM-MD5';                        
	    $this->mail->Username = Config::MAIL_USERNAME;    // this must correct email account
	    $this->mail->Password = Config::MAIL_PASSWORD;    // this must correct email password
	    $this->mail->From = Config::MAIL_USERNAME;        
	    $this->mail->FromName = Config::HOST_NAME;

	    $this->mail->isSMTP();
	    $this->mail->isHTML(true);   

	}	

	public function setAddress($sAddress, $sName = '', $sType = 'to')
    {
        switch ($sType) {
            case 'to':
                return $this->mail->addAddress($sAddress, $sName);
            case 'cc':
                return $this->mail->addCC($sAddress, $sName);
            case 'bcc':
                return $this->mail->addBCC($sAddress, $sName);
        }

        return false;
    }

	public function setSubject($subject) {
		$this->mail->Subject = $subject;
		return $this;
	}

	public function setBody($body) {
		$this->mail->Body = $body;
		return $this;
	}

	public function sendMail() {
		return ($this->mail->send()) ? true : $this->mail->ErrorInfo;
	}



	public function __destruct() {
		$this->mail->ClearAddresses();
	}

}
